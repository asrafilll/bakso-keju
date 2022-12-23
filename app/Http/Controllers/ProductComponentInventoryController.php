<?php

namespace App\Http\Controllers;

use App\Actions\SearchBranchesAction;
use App\Models\ProductComponentInventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class ProductComponentInventoryController extends Controller
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
                $productComponentInventoryQuery = ProductComponentInventory::query()
                    ->select([
                        'product_component_inventories.*',
                        'product_components.name as product_component_name',
                        'branches.name as branch_name',
                    ])
                    ->join('product_components', 'product_component_inventories.product_component_id', 'product_components.id')
                    ->join('branches', 'product_component_inventories.branch_id', 'branches.id')
                    ->join('branch_users', 'product_component_inventories.branch_id', 'branch_users.branch_id')
                    ->where('branch_users.user_id', $request->user()->id);

                if ($request->filled('term')) {
                    $productComponentInventoryQuery->where(function ($query) use ($request) {
                        $searchables = [
                            'product_components.name',
                            'branches.name',
                            'product_component_inventories.quantity',
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
                        $productComponentInventoryQuery->where($filterable, $request->get($filterable));
                    }
                }

                $productComponentInventories = $productComponentInventoryQuery->latest()->paginate();

                return Response::view('product-component-inventory.index', [
                    'productComponentInventories' => $productComponentInventories,
                ]);
            },
        ];

        return $actions[$request->get('action', 'default')]();
    }
}
