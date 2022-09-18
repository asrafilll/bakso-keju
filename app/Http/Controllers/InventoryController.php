<?php

namespace App\Http\Controllers;

use App\Http\Requests\InventoryStoreRequest;
use App\Models\Branch;
use App\Models\Inventory;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class InventoryController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        switch ($request->get('action')) {
            case 'fetch-branches':
                $branches = Branch::query()
                    ->where('name', 'LIKE', "%{$request->get('term')}%")
                    ->orderBy('name')
                    ->get();

                return Response::json($branches);

            case 'fetch-products':
                $products = Product::query()
                    ->where('name', 'LIKE', "%{$request->get('term')}%")
                    ->orderBy('name')
                    ->get();

                return Response::json($products);

            default:
                $inventoryQuery = Inventory::query()
                    ->select([
                        'inventories.*',
                        'products.name as product_name',
                        'branches.name as branch_name',
                    ])
                    ->join('products', 'inventories.product_id', 'products.id')
                    ->join('branches', 'inventories.branch_id', 'branches.id');

                if ($request->filled('filter')) {
                    $inventoryQuery->where(function ($query) use ($request) {
                        $filterables = [
                            'product_name',
                            'branch_name',
                        ];

                        foreach ($filterables as $filterable) {
                            $query->orWhere($filterable, 'LIKE', "%{$request->get('filter')}%");
                        }
                    });
                }

                $inventories = $inventoryQuery->latest()->paginate();

                return Response::view('inventory.index', [
                    'inventories' => $inventories,
                ]);
        }
    }

    /**
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return Response::view('inventory.create');
    }

    /**
     * @param InventoryStoreRequest $inventoryStoreRequest
     * @return \Illuminate\Http\Response
     */
    public function store(InventoryStoreRequest $inventoryStoreRequest)
    {
        Inventory::create($inventoryStoreRequest->validated());

        return Response::redirectTo('/inventories/create')
            ->with('success', __('crud.created', [
                'resource' => 'inventory',
            ]));
    }
}
