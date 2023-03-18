<?php

namespace App\Http\Controllers;

use App\Actions\CreateComponentInventoryAction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use App\Actions\SearchBranchesAction;
use App\Exports\ComponentInventoriesExport;
use App\Http\Requests\ComponentInventoryStoreRequest;
use App\Models\ComponentInventory;
use App\Models\ProductComponent;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Exception;

class ComponentInventoryController extends Controller
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
            'export' => function () use ($request) {
                return Excel::download(
                    new ComponentInventoriesExport($request->all() + [
                        'user_id' => $request->user()->id,
                    ]),
                    'component_inventories-' . Carbon::now()->unix() . '.xlsx'
                );
            },
            'default' => function () use ($request) {
                $componentInventoryQuery = ComponentInventory::query()
                    ->select([
                        'component_inventories.*',
                        'product_components.name as product_component_name',
                        'branches.name as branch_name',
                    ])
                    ->join('product_components', 'component_inventories.product_component_id', 'product_components.id')
                    ->join('branches', 'component_inventories.branch_id', 'branches.id')
                    ->join('branch_users', 'component_inventories.branch_id', 'branch_users.branch_id')
                    ->where('branch_users.user_id', $request->user()->id);

                if ($request->filled('term')) {
                    $componentInventoryQuery->where(function ($query) use ($request) {
                        $searchables = [
                            'product_components.name',
                            'branches.name',
                            'component_inventories.quantity',
                        ];

                        foreach ($searchables as $searchable) {
                            $query->orWhere($searchable, 'LIKE', "%{$request->get('term')}%");
                        }
                    });
                }

                $filterables = [
                    'component_inventories.branch_id' => 'branch_id',
                ];

                foreach ($filterables as $field => $filterable) {
                    if ($request->filled($filterable)) {
                        $componentInventoryQuery->where($field, $request->get($filterable));
                    }
                }

                $componentInventories = $componentInventoryQuery->latest()->paginate();

                return Response::view('component-inventory.index', [
                    'componentInventories' => $componentInventories,
                ]);
            },
        ];

        return $actions[$request->get('action', 'default')]();
    }

    /**
     * @param Request
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
            'fetch-components' => function () use ($request) {
                $components = ProductComponent::query()
                    ->where('name', 'LIKE', "%{$request->get('term')}%")
                    ->orderBy('name')
                    ->get();

                return Response::json($components);
            },
            'default' => function () {
                return Response::view('component-inventory.create');
            },
        ];

        return $actions[$request->get('action', 'default')]();
    }

    /**
     * @param ItemInventoryStoreRequest $componentInventoryStoreRequest
     * @param CreateComponentInventoryAction $createComponentInventoryAction
     * @return \Illuminate\Http\Response
     */
    public function store(
        ComponentInventoryStoreRequest $componentInventoryStoreRequest,
        CreateComponentInventoryAction $createComponentInventoryAction
    ) {
        try {
            $createComponentInventoryAction->execute(
                $componentInventoryStoreRequest->all(),
                $componentInventoryStoreRequest->user()
            );

            return Response::redirectTo('/component-inventories/create')
                ->with('success', __('crud.created', [
                    'resource' => 'component_inventories',
                ]));
        } catch (Exception $e) {
            return Response::redirectTo('/component-inventories/create')
                ->with('failed', $e->getMessage());
        }
    }
}
