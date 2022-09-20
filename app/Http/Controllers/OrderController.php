<?php

namespace App\Http\Controllers;

use App\Actions\CreateOrderAction;
use App\Actions\DeleteOrderAction;
use App\Actions\SearchBranchesAction;
use App\Actions\SearchOrderSourcesAction;
use App\Http\Requests\OrderStoreRequest;
use App\Models\Branch;
use App\Models\Order;
use App\Models\OrderSource;
use App\Models\Product;
use App\Models\Reseller;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class OrderController extends Controller
{
    /**
     * @param Request $request
     * @param SearchBranchesAction $searchBranchesAction
     * @param SearchOrderSourcesAction $searchOrderSourcesAction
     * @return \Illuminate\Http\Response
     */
    public function index(
        Request $request,
        SearchBranchesAction $searchBranchesAction,
        SearchOrderSourcesAction $searchOrderSourcesAction
    ) {
        $actions = [
            'fetch-branches' => function () use ($request, $searchBranchesAction) {
                return Response::json(
                    $searchBranchesAction->execute($request->get('term'))
                );
            },
            'fetch-order-sources' => function () use ($request, $searchOrderSourcesAction) {
                return Response::json(
                    $searchOrderSourcesAction->execute($request->get('term'))
                );
            },
            'default' => function () use ($request) {
                $orderQuery = Order::query()
                    ->select([
                        'orders.*',
                        'order_sources.name as order_source_name',
                        'branches.name as branch_name',
                    ])
                    ->join('order_sources', 'orders.order_source_id', 'order_sources.id')
                    ->join('branches', 'orders.branch_id', 'branches.id');

                if ($request->filled('term')) {
                    $orderQuery->where(function ($query) use ($request) {
                        $searchables = [
                            'orders.order_number',
                            'branches.name',
                            'order_sources.name',
                            'orders.customer_name',
                        ];

                        foreach ($searchables as $searchable) {
                            $query->orWhere($searchable, 'LIKE', "%{$request->get('term')}%");
                        }
                    });
                }

                $filterables = [
                    'branch_id',
                    'order_source_id',
                ];

                foreach ($filterables as $filterable) {
                    if ($request->filled($filterable)) {
                        $orderQuery->where($filterable, $request->get($filterable));
                    }
                }

                $sortables = [
                    'order_number',
                    'created_at',
                    'percentage_discount',
                    'total_discount',
                    'total_line_items_quantity',
                    'total_line_items_price',
                    'total_price',
                ];
                $sort = 'created_at';
                $direction = 'desc';

                if ($request->filled('sort') && in_array($request->get('sort'), $sortables)) {
                    $sort = $request->get('sort');
                }

                if ($request->filled('direction') && in_array($request->get('direction'), ['asc', 'desc'])) {
                    $direction = $request->get('direction');
                }

                $orders = $orderQuery->orderBy($sort, $direction)->paginate();

                return Response::view('order.index', [
                    'orders' => $orders,
                ]);
            },
        ];

        return $actions[$request->get('action', 'default')]();
    }

    /**
     * @param Request $request
     * @param SearchBranchesAction $searchBranchesAction
     * @param SearchOrderSourcesAction $searchOrderSourcesAction
     * @return \Illuminate\Http\Response
     */
    public function create(
        Request $request,
        SearchBranchesAction $searchBranchesAction,
        SearchOrderSourcesAction $searchOrderSourcesAction
    ) {
        $actions = [
            'fetch-branches' => function () use ($request, $searchBranchesAction) {
                return Response::json(
                    $searchBranchesAction->execute($request->get('term'))
                );
            },
            'fetch-order-sources' => function () use ($request, $searchOrderSourcesAction) {
                return Response::json(
                    $searchOrderSourcesAction->execute($request->get('term'))
                );
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
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function show(Order $order, Request $request)
    {
        $order->load([
            'branch',
            'orderSource',
            'orderLineItems',
        ]);

        $actions = [
            'print-invoice' => function () use ($order) {
                $pdf = Pdf::loadView('order.invoice', [
                    'order' => $order,
                ])->setPaper('a8');

                return $pdf->stream();
            },
            'default' => function () use ($order) {
                return Response::view('order.show', [
                    'order' => $order,
                ]);
            }
        ];

        return $actions[$request->get('action', 'default')]();
    }

    public function destroy(
        Order $order,
        DeleteOrderAction $deleteOrderAction
    ) {
        try {
            $deleteOrderAction->execute($order);

            return Response::redirectTo('/orders')
                ->with('success', __('crud.deleted', [
                    'resource' => 'order',
                ]));
        } catch (Exception $e) {
            return Response::redirectTo('/orders')
                ->with('failed', $e->getMessage());
        }
    }
}
