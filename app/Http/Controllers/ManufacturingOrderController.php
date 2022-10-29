<?php

namespace App\Http\Controllers;

use App\Actions\CreateManufacturingOrderAction;
use App\Actions\SearchBranchesAction;
use App\Http\Requests\ManufacturingOrderStoreRequest;
use App\Models\ManufacturingOrder;
use App\Models\ProductComponent;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class ManufacturingOrderController extends Controller
{
    /**
     * @param Request $request
     * @param SearchBranchesAction $searchBranchesAction
     * @return \Illuminate\Http\Response
     */
    public function index(
        Request $request,
        SearchBranchesAction $searchBranchesAction
    ) {
        $actions = [
            'fetch-branches' => function () use ($request, $searchBranchesAction) {
                return Response::json(
                    $searchBranchesAction->execute($request->get('term'))
                );
            },
            'default' => function () use ($request) {
                $manufacturingOrderQuery = ManufacturingOrder::query()
                    ->select([
                        'manufacturing_orders.*',
                        'branches.name as branch_name',
                    ])
                    ->join('branches', 'manufacturing_orders.branch_id', 'branches.id');

                if ($request->filled('term')) {
                    $manufacturingOrderQuery->where(function ($query) use ($request) {
                        $searchables = [
                            'orders.order_number',
                            'branches.name',
                        ];

                        foreach ($searchables as $searchable) {
                            $query->orWhere($searchable, 'LIKE', "%{$request->get('term')}%");
                        }
                    });
                }

                $filterables = [
                    'branch_id',
                ];

                foreach ($filterables as $filterable) {
                    if ($request->filled($filterable)) {
                        $manufacturingOrderQuery->where($filterable, $request->get($filterable));
                    }
                }

                if ($request->filled('start_created_at')) {
                    $manufacturingOrderQuery->whereRaw('DATE(orders.created_at) >= ?', [
                        $request->get('start_created_at'),
                    ]);
                }

                if ($request->filled('end_created_at')) {
                    $manufacturingOrderQuery->whereRaw('DATE(orders.created_at) <= ?', [
                        $request->get('end_created_at'),
                    ]);
                }

                $sortables = [
                    'order_number',
                    'created_at',
                    'total_line_items_weight',
                    'total_line_items_quantity',
                    'total_line_items_price',
                ];
                $sort = 'created_at';
                $direction = 'desc';

                if ($request->filled('sort') && in_array($request->get('sort'), $sortables)) {
                    $sort = $request->get('sort');
                }

                if ($request->filled('direction') && in_array($request->get('direction'), ['asc', 'desc'])) {
                    $direction = $request->get('direction');
                }

                $manufacturingOrders = $manufacturingOrderQuery->orderBy($sort, $direction)->paginate();

                return Response::view('manufacturing-order.index', [
                    'manufacturingOrders' => $manufacturingOrders,
                ]);
            },
        ];

        return $actions[$request->get('action', 'default')]();
    }

    /**
     * @param Request $request
     * @param SearchBranchesAction $searchBranchesAction
     * @return \Illuminate\Http\Response
     */
    public function create(
        Request $request,
        SearchBranchesAction $searchBranchesAction
    ) {
        $actions = [
            'fetch-branches' => function () use ($request, $searchBranchesAction) {
                return Response::json(
                    $searchBranchesAction->execute($request->get('term'))
                );
            },
            'fetch-product-components' => function () use ($request) {
                $productComponents = ProductComponent::query()
                    ->orderBy('name')
                    ->get();

                return Response::json($productComponents);
            },
            'default' => function () {
                return Response::view('manufacturing-order.create');
            },
        ];

        return $actions[$request->get('action', 'default')]();
    }

    /**
     * @param ManufacturingOrderStoreRequest $request
     * @param CreateManufacturingOrderAction $createManufacturingOrderAction
     * @return \Illuminate\Http\Response
     */
    public function store(
        ManufacturingOrderStoreRequest $manufacturingOrderStoreRequest,
        CreateManufacturingOrderAction $createManufacturingOrderAction
    ) {
        try {
            $order = $createManufacturingOrderAction->execute(
                $manufacturingOrderStoreRequest->all() + [
                    'created_by' => $manufacturingOrderStoreRequest->user()->id,
                ]
            );

            return Response::redirectTo('/manufacturing-orders/' . $order->id)
                ->with('success', __('crud.created', [
                    'resource' => 'manufacturing order',
                ]));
        } catch (Exception $e) {
            return Response::redirectTo('/manufacturing-orders/create')
                ->with('failed', $e->getMessage());
        }
    }

    /**
     * @param ManufacturingOrder $manufacturingOrder
     * @return \Illuminate\Http\Response
     */
    public function show(ManufacturingOrder $manufacturingOrder)
    {
        $manufacturingOrder->load([
            'branch',
            'manufacturingOrderLineItems',
            'creator',
        ]);

        return Response::view('manufacturing-order.show', [
            'manufacturingOrder' => $manufacturingOrder,
        ]);
    }

    /**
     * @param ManufacturingOrder $manufacturingOrder
     * @return \Illuminate\Http\Response
     */
    public function destroy(ManufacturingOrder $manufacturingOrder)
    {
        $manufacturingOrder->manufacturingOrderLineItems()->delete();
        $manufacturingOrder->delete();

        return Response::redirectTo('/manufacturing-orders')
            ->with('success', __('crud.deleted', [
                'resource' => 'manufacturing order',
            ]));
    }
}
