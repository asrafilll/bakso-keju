<?php

namespace App\Http\Controllers;

use App\Http\Requests\InventoryStoreRequest;
use App\Models\Branch;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\ProductInventory;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class InventoryController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $inventoryQuery = Inventory::query()
            ->select([
                'inventories.*',
                'products.name as product_name',
                'branches.name as branch_name',
                'users.name as created_by_name'
            ])
            ->join('products', 'inventories.product_id', 'products.id')
            ->join('branches', 'inventories.branch_id', 'branches.id')
            ->join('users', 'inventories.created_by', 'users.id');

        if ($request->filled('filter')) {
            $inventoryQuery->where(function ($query) use ($request) {
                $filterables = [
                    'products.name',
                    'branches.name',
                    'users.name',
                    'inventories.quantity',
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

    /**
     * @param Request
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
     * @return \Illuminate\Http\Response
     */
    public function store(InventoryStoreRequest $inventoryStoreRequest)
    {
        try {
            DB::beginTransaction();

            /** @var Inventory */
            $inventory = Inventory::create($inventoryStoreRequest->validated() + [
                'created_by' => $inventoryStoreRequest->user()->id,
            ]);
            /** @var ProductInventory|null */
            $productInventory = ProductInventory::query()
                ->where([
                    'branch_id' => $inventory->branch_id,
                    'product_id' => $inventory->product_id,
                ])
                ->first();

            if ($productInventory) {
                $productInventory->update([
                    'quantity' => $productInventory->quantity + $inventory->quantity,
                ]);
            } else {
                ProductInventory::create($inventoryStoreRequest->only([
                    'branch_id',
                    'product_id',
                    'quantity',
                ]));
            }

            DB::commit();

            return Response::redirectTo('/inventories/create')
                ->with('success', __('crud.created', [
                    'resource' => 'inventory',
                ]));
        } catch (Exception $e) {
            DB::rollBack();

            return Response::redirectTo('/inventories/create')
                ->with('failed', $e->getMessage());
        }
    }
}
