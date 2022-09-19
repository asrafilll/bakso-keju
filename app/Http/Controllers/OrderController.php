<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderStoreRequest;
use App\Models\Branch;
use App\Models\Order;
use App\Models\OrderLineItem;
use App\Models\OrderSource;
use App\Models\Product;
use App\Models\Reseller;
use Exception;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $orderQuery = Order::query()
            ->select([
                'orders.*',
                'order_sources.name as order_source_name',
                'branches.name as branch_name',
            ])
            ->join('order_sources', 'orders.order_source_id', 'order_sources.id')
            ->join('branches', 'orders.branch_id', 'branches.id');

        if ($request->filled('filter')) {
            $orderQuery->where(function ($query) use ($request) {
                $filterables = [
                    'product_name',
                    'branch_name',
                ];

                foreach ($filterables as $filterable) {
                    $query->orWhere($filterable, 'LIKE', "%{$request->get('filter')}%");
                }
            });
        }

        $orders = $orderQuery->latest()->paginate();

        return Response::view('order.index', [
            'orders' => $orders,
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $actions = [
            'fetch-branches' => function () use ($request) {
                $branches = Branch::query()
                    ->where('name', 'LIKE', "%{$request->get('term')}%")
                    ->orderBy('name')
                    ->get();

                return Response::json($branches);
            },
            'fetch-order-sources' => function () use ($request) {
                $orderSources = OrderSource::query()
                    ->where('name', 'LIKE', "%{$request->get('term')}%")
                    ->orderBy('name')
                    ->get();

                return Response::json($orderSources);
            },
            'fetch-resellers' => function () use ($request) {
                $resellers = Reseller::query()
                    ->where('name', 'LIKE', "%{$request->get('term')}%")
                    ->orderBy('name')
                    ->get();

                return Response::json($resellers);
            },
            'fetch-products' => function () use ($request) {
                $products = Product::query()
                    ->where('name', 'LIKE', "%{$request->get('term')}%")
                    ->orderBy('name')
                    ->get();

                return Response::json($products);
            },
            'default' => function () {
                return Response::view('order.create');
            },
        ];

        return $actions[$request->get('action', 'default')]();
    }

    /**
     * @param OrderStoreRequest $orderStoreRequest
     * @return \Illuminate\Http\Response
     */
    public function store(OrderStoreRequest $orderStoreRequest)
    {
        try {
            DB::beginTransaction();

            /** @var Branch */
            $branch = Branch::find($orderStoreRequest->get('branch_id'));

            if (!$branch) {
                throw ValidationException::withMessages([
                    'branch_id' => __('validation.exists', [
                        'attribute' => 'branch_id'
                    ]),
                ]);
            }

            /** @var OrderSource */
            $orderSource = OrderSource::find($orderStoreRequest->get('order_source_id'));

            if (!$orderSource) {
                throw ValidationException::withMessages([
                    'order_source_id' => __('validation.exists', [
                        'attribute' => 'order_source_id'
                    ]),
                ]);
            }

            $reseller = null;

            if ($orderStoreRequest->filled('reseller_id')) {
                $reseller = Reseller::find($orderStoreRequest->get('reseller_id'));

                if (!$reseller) {
                    throw ValidationException::withMessages([
                        'reseller_id' => __('validation.exists', [
                            'attribute' => 'reseller_id',
                        ])
                    ]);
                }
            }

            /** @var Collection */
            $lineItems = new Collection($orderStoreRequest->get('line_items'));
            /** @var array<string> */
            $lineItemsProductIDs = $lineItems->pluck('product_id')->toArray();
            /** @var EloquentCollection<Product> */
            $products = Product::query()
                ->whereIn('id', $lineItemsProductIDs)
                ->get();
            /** @var Collection */
            $orderLineItems = new Collection();

            foreach ($lineItems as $lineItem) {
                $product = $products->firstWhere('id', $lineItem['product_id']);

                if (!$product) {
                    continue;
                }

                $quantity = intval($lineItem['quantity']);

                $orderLineItems->push(new OrderLineItem([
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'price' => $product->price,
                    'quantity' => $quantity,
                    'total' => $product->price * $quantity,
                ]));
            }

            $orderNumber = implode('', [
                $branch->order_number_prefix,
                '-',
                Carbon::now()->format('Ym'),
                str_pad(
                    $branch->next_order_number,
                    4,
                    '0',
                    STR_PAD_LEFT
                )
            ]);

            $resellerOrder = !is_null($reseller);
            $resellerId = $resellerOrder ? $reseller->id : null;
            $percentageDiscount = $resellerOrder ? $reseller->percentage_discount : 0;
            $totalLineItemsQuantity = $orderLineItems->sum('quantity');
            $totalLineItemsPrice = $orderLineItems->sum('total');
            $totalDiscount = round($totalLineItemsPrice * ($percentageDiscount / 100));
            $totalPrice = $totalLineItemsPrice - $totalDiscount;

            /** @var Order */
            $order = new Order($orderStoreRequest->only([
                'branch_id',
                'order_source_id',
                'customer_name',
            ]) + [
                'reseller_order' => $resellerOrder,
                'reseller_id' => $resellerId,
                'order_number' => $orderNumber,
                'percentage_discount' => $percentageDiscount,
                'total_discount' => $totalDiscount,
                'total_line_items_quantity' => $totalLineItemsQuantity,
                'total_line_items_price' => $totalLineItemsPrice,
                'total_price' => $totalPrice,
            ]);

            $order->save();
            $order->orderLineItems()->createMany($orderLineItems->toArray());
            $branch->next_order_number++;
            $branch->save();

            DB::commit();

            return Response::redirectTo('/orders/create')
                ->with('success', __('crud.created', [
                    'resource' => 'order',
                ]));
        } catch (Exception $e) {
            DB::rollBack();

            return Response::redirectTo('/orders/create')
                ->with('failed', $e->getMessage());
        }
    }
}
