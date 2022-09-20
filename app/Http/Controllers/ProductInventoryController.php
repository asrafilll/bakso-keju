<?php

namespace App\Http\Controllers;

use App\Models\ProductInventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class ProductInventoryController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $productInventoryQuery = ProductInventory::query()
            ->select([
                'product_inventories.*',
                'products.name as product_name',
                'branches.name as branch_name',
            ])
            ->join('products', 'product_inventories.product_id', 'products.id')
            ->join('branches', 'product_inventories.branch_id', 'branches.id');

        if ($request->filled('filter')) {
            $productInventoryQuery->where(function ($query) use ($request) {
                $filterables = [
                    'products.name',
                    'branches.name',
                    'product_inventories.quantity',
                ];

                foreach ($filterables as $filterable) {
                    $query->orWhere($filterable, 'LIKE', "%{$request->get('filter')}%");
                }
            });
        }

        $productInventories = $productInventoryQuery->latest()->paginate();

        return Response::view('product-inventory.index', [
            'productInventories' => $productInventories,
        ]);
    }
}
