<?php

namespace App\Http\Controllers;

use App\Actions\CreateInventoryAction;
use App\Actions\SearchBranchesAction;
use App\Exports\InventoriesExport;
use App\Http\Requests\InventoryStoreRequest;
use App\Models\Branch;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\ProductInventory;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Maatwebsite\Excel\Facades\Excel;

class InventoryController extends Controller
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
                    new InventoriesExport($request->all() + [
                        'user_id' => $request->user()->id,
                    ]),
                    'inventories-' . Carbon::now()->unix() . '.xlsx'
                );
            },
            'default' => function () use ($request) {
                $inventoryQuery = Inventory::query()
                    ->select([
                        'inventories.*',
                        'products.name as product_name',
                        'branches.name as branch_name',
                        'users.name as created_by_name'
                    ])
                    ->join('products', 'inventories.product_id', 'products.id')
                    ->join('branches', 'inventories.branch_id', 'branches.id')
                    ->join('branch_users', 'inventories.branch_id', 'branch_users.branch_id')
                    ->join('users', 'inventories.created_by', 'users.id')
                    ->where('branch_users.user_id', $request->user()->id);

                if ($request->filled('term')) {
                    $inventoryQuery->where(function ($query) use ($request) {
                        $searchables = [
                            'products.name',
                            'branches.name',
                            'users.name',
                            'inventories.quantity',
                        ];

                        foreach ($searchables as $searchable) {
                            $query->orWhere($searchable, 'LIKE', "%{$request->get('term')}%");
                        }
                    });
                }

                $filterables = [
                    'inventories.branch_id' => 'branch_id',
                ];

                foreach ($filterables as $field => $filterable) {
                    if ($request->filled($filterable)) {
                        $inventoryQuery->where($field, $request->get($filterable));
                    }
                }

                $inventories = $inventoryQuery->latest()->paginate();

                return Response::view('inventory.index', [
                    'inventories' => $inventories,
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
            'fetch-products' => function () use ($request) {
                $products = Product::query()
                    ->where('name', 'LIKE', "%{$request->get('term')}%")
                    ->orderBy('name')
                    ->get();

                return Response::json($products);
            },
            'default' => function () {
                return Response::view('inventory.create');
            },
        ];

        return $actions[$request->get('action', 'default')]();
    }

    /**
     * @param InventoryStoreRequest $inventoryStoreRequest
     * @param CreateInventoryAction $createInventoryAction
     * @return \Illuminate\Http\Response
     */
    public function store(
        InventoryStoreRequest $inventoryStoreRequest,
        CreateInventoryAction $createInventoryAction
    ) {
        try {
            $createInventoryAction->execute(
                $inventoryStoreRequest->all(),
                $inventoryStoreRequest->user()
            );

            return Response::redirectTo('/inventories/create')
                ->with('success', __('crud.created', [
                    'resource' => 'inventory',
                ]));
        } catch (Exception $e) {
            return Response::redirectTo('/inventories/create')
                ->with('failed', $e->getMessage());
        }
    }
}
