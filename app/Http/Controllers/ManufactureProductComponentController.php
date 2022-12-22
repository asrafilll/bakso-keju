<?php

namespace App\Http\Controllers;

use App\Actions\CreateManufactureProductComponentAction;
use App\Actions\DeleteManufactureProductComponentAction;
use App\Actions\SearchBranchesAction;
use App\Http\Requests\ManufactureProductComponentStoreRequest;
use App\Models\Branch;
use App\Models\ManufactureProductComponent;
use App\Models\ProductComponent;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class ManufactureProductComponentController extends Controller
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
                    $searchBranchesAction->execute(
                        $request->get('term'),
                        $request->user()
                    )
                );
            },
            'default' => function () use ($request) {
                $manufactureProductComponentQuery = ManufactureProductComponent::query()
                    ->select([
                        'manufacture_product_components.*',
                        'branches.name as branch_name',
                    ])
                    ->join('branches', 'manufacture_product_components.branch_id', 'branches.id');

                if ($request->filled('term')) {
                    $manufactureProductComponentQuery->where(function ($query) use ($request) {
                        $searchables = [
                            'manufacture_product_components.order_number',
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
                        $manufactureProductComponentQuery->where($filterable, $request->get($filterable));
                    }
                }

                if ($request->filled('start_created_at')) {
                    $manufactureProductComponentQuery->whereRaw('DATE(orders.created_at) >= ?', [
                        $request->get('start_created_at'),
                    ]);
                }

                if ($request->filled('end_created_at')) {
                    $manufactureProductComponentQuery->whereRaw('DATE(orders.created_at) <= ?', [
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

                $manufactureProductComponents = $manufactureProductComponentQuery->orderBy($sort, $direction)->paginate();

                return Response::view('manufacture-product-component.index', [
                    'manufactureProductComponents' => $manufactureProductComponents,
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
                    $searchBranchesAction->execute(
                        $request->get('term'),
                        $request->user()
                    )
                );
            },
            'fetch-product-components' => function () use ($request) {
                $productComponents = ProductComponent::query()
                    ->where('name', 'LIKE', "%{$request->get('term')}%")
                    ->orderBy('name')
                    ->get();

                return Response::json($productComponents);
            },
            'default' => function () {
                $mainBranch = Branch::query()
                    ->where('is_main', true)
                    ->first();

                return Response::view('manufacture-product-component.create', [
                    'mainBranch' => $mainBranch,
                ]);
            },
        ];

        return $actions[$request->get('action', 'default')]();
    }

    /**
     * @param ManufactureProductComponentStoreRequest $request
     * @param CreateManufactureProductComponentAction $createmanufactureProductComponentAction
     * @return \Illuminate\Http\Response
     */
    public function store(
        ManufactureProductComponentStoreRequest $manufactureProductComponentstoreRequest,
        CreateManufactureProductComponentAction $createmanufactureProductComponentAction
    ) {
        try {
            $order = $createmanufactureProductComponentAction->execute(
                $manufactureProductComponentstoreRequest->all() + [
                    'created_by' => $manufactureProductComponentstoreRequest->user()->id,
                ]
            );

            return Response::redirectTo('/manufacture-product-components/' . $order->id)
                ->with('success', __('crud.created', [
                    'resource' => 'manufacture product component',
                ]));
        } catch (Exception $e) {
            return Response::redirectTo('/manufacture-product-components/create')
                ->with('failed', $e->getMessage());
        }
    }

    /**
     * @param ManufactureProductComponent $manufactureProductComponent
     * @return \Illuminate\Http\Response
     */
    public function show(ManufactureProductComponent $manufactureProductComponent)
    {
        $manufactureProductComponent->load([
            'branch',
            'manufactureProductComponentLineItems',
            'creator',
        ]);

        return Response::view('manufacture-product-component.show', [
            'manufactureProductComponent' => $manufactureProductComponent,
        ]);
    }

    /**
     * @param ManufactureProductComponent $manufactureProductComponent
     * @param DeleteManufactureProductComponentAction $deletemanufactureProductComponentAction
     * @return \Illuminate\Http\Response
     */
    public function destroy(
        ManufactureProductComponent $manufactureProductComponent,
        DeleteManufactureProductComponentAction $deletemanufactureProductComponentAction
    ) {
        try {
            $deletemanufactureProductComponentAction->execute($manufactureProductComponent);

            return Response::redirectTo('/manufacture-product-components')
                ->with('success', __('crud.deleted', [
                    'resource' => 'manufacture product component',
                ]));
        } catch (Exception $e) {
            return Response::redirectTo('/manufacture-product-components')
                ->with('failed', $e->getMessage());
        }
    }
}
