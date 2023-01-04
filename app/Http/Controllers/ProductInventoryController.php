<?php

namespace App\Http\Controllers;

use App\Actions\SearchBranchesAction;
use App\Exports\ProductInventoriesExport;
use App\Models\ProductInventory;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Response;
use Maatwebsite\Excel\Facades\Excel;

class ProductInventoryController extends Controller
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
                    new ProductInventoriesExport($request->all() + [
                        'user_id' => $request->user()->id,
                    ]),
                    'product-inventories-' . Carbon::now()->unix() . '.xlsx'
                );
            },
            'default' => function () use ($request) {
                $productInventoryQuery = ProductInventory::query()
                    ->select([
                        'product_inventories.*',
                        'products.name as product_name',
                        'branches.name as branch_name',
                    ])
                    ->join('products', 'product_inventories.product_id', 'products.id')
                    ->join('branches', 'product_inventories.branch_id', 'branches.id')
                    ->join('branch_users', 'product_inventories.branch_id', 'branch_users.branch_id')
                    ->where('branch_users.user_id', $request->user()->id);

                if ($request->filled('term')) {
                    $productInventoryQuery->where(function ($query) use ($request) {
                        $searchables = [
                            'products.name',
                            'branches.name',
                            'product_inventories.quantity',
                        ];

                        foreach ($searchables as $searchable) {
                            $query->orWhere($searchable, 'LIKE', "%{$request->get('term')}%");
                        }
                    });
                }

                $filterables = [
                    'product_inventories.branch_id' => 'branch_id',
                ];

                foreach ($filterables as $field => $filterable) {
                    if ($request->filled($filterable)) {
                        $productInventoryQuery->where($field, $request->get($filterable));
                    }
                }

                $productInventories = $productInventoryQuery->latest()->paginate();

                return Response::view('product-inventory.index', [
                    'productInventories' => $productInventories,
                ]);
            },
        ];

        return $actions[$request->get('action', 'default')]();
    }
}
