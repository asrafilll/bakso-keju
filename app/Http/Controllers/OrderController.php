<?php

namespace App\Http\Controllers;

use App\Actions\CreateOrderAction;
use App\Http\Requests\OrderStoreRequest;
use App\Models\Branch;
use App\Models\Order;
use App\Models\OrderSource;
use App\Models\Product;
use App\Models\Reseller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

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
                    ->select([
                        'products.*',
                    ])
                    ->join('product_inventories', 'products.id', 'product_inventories.product_id')
                    ->where('product_inventories.branch_id', $request->get('branch_id'))
                    ->where('product_inventories.quantity', '>', 0)
                    ->where('products.name', 'LIKE', "%{$request->get('term')}%")
                    ->orderBy('products.name')
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
     * @param CreateOrderAction $createOrderAction
     * @return \Illuminate\Http\Response
     */
    public function store(
        OrderStoreRequest $orderStoreRequest,
        CreateOrderAction $createOrderAction
    ) {
        try {
            $createOrderAction->execute($orderStoreRequest->all());

            return Response::redirectTo('/orders/create')
                ->with('success', __('crud.created', [
                    'resource' => 'order',
                ]));
        } catch (Exception $e) {
            return Response::redirectTo('/orders/create')
                ->with('failed', $e->getMessage());
        }
    }

    /**
     * @param Order $order
     * @return \Illuminate\Http\Response
     */
    public function show(Order $order)
    {
        $order->load([
            'branch',
            'orderSource',
            'orderLineItems',
        ]);

        return Response::view('order.show', [
            'order' => $order,
        ]);
    }
}
