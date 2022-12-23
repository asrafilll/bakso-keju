<?php

namespace App\Actions;

use App\Models\Branch;
use App\Models\ManufactureProduct;
use App\Models\ManufactureProductLineProduct;
use App\Models\ManufactureProductLineProductComponent;
use App\Models\Product;
use App\Models\ProductComponent;
use App\Models\ProductComponentInventory;
use App\Models\ProductInventory;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CreateManufactureProductAction
{
    /**
     * @param array $data
     * @param User $authenticatedUser
     * @return ManufactureProduct
     */
    public function execute(array $data, User $authenticatedUser)
    {
        DB::beginTransaction();

        /** @var Branch */
        $branch = Branch::find($data['branch_id']);

        if (!$branch) {
            throw ValidationException::withMessages([
                'branch_id' => __('validation.exists', [
                    'attribute' => 'branch_id'
                ]),
            ]);
        }

        if (!$authenticatedUser->hasRegisteredToBranch($branch)) {
            throw new Exception(
                __("You are not registered to this branch"),
                422
            );
        }

        /** @var Collection */
        $lineProductComponents = new Collection($data['line_product_components']);
        /** @var array<string> */
        $lineProductComponentsProductComponentIDs = $lineProductComponents->pluck('product_component_id')->toArray();
        /** @var EloquentCollection<ProductComponent> */
        $productComponents = ProductComponent::query()
            ->select([
                'product_components.*',
                'product_component_inventories.quantity',
            ])
            ->join('product_component_inventories', 'product_component_inventories.product_component_id', 'product_components.id')
            ->where('product_component_inventories.branch_id', $branch->id)
            ->whereIn('product_components.id', $lineProductComponentsProductComponentIDs)
            ->get();
        /** @var Collection */
        $manufactureProductLineProductComponents = new Collection();

        foreach ($lineProductComponents as $lineProductComponent) {
            $productComponent = $productComponents->firstWhere('id', $lineProductComponent['product_component_id']);

            if (!$productComponent) {
                throw new Exception(
                    __("Some product component not found"),
                    422
                );
            }

            $quantity = intval($lineProductComponent['quantity']);

            if ($quantity > $productComponent->quantity) {
                throw new Exception("Product component quantity less than needed");
            }

            $manufactureProductLineProductComponents->push(new ManufactureProductLineProductComponent([
                'product_component_id' => $productComponent->id,
                'product_component_name' => $productComponent->name,
                'quantity' => $quantity,
            ]));
        }

        /** @var Collection */
        $lineProducts = new Collection($data['line_products']);
        /** @var array<string> */
        $lineProductsProductIDs = $lineProducts->pluck('product_id')->toArray();
        /** @var EloquentCollection<Product> */
        $products = Product::query()
            ->whereIn('id', $lineProductsProductIDs)
            ->get();
        /** @var Collection */
        $manufactureProductLineProducts = new Collection();

        foreach ($lineProducts as $lineProduct) {
            $product = $products->firstWhere('id', $lineProduct['product_id']);

            if (!$product) {
                throw new Exception(
                    __("Some product not found"),
                    422
                );
            }

            $quantity = intval($lineProduct['quantity']);

            $manufactureProductLineProducts->push(new ManufactureProductLineProduct([
                'product_id' => $product->id,
                'product_name' => $product->name,
                'quantity' => $quantity,
            ]));
        }

        $orderNumber = Carbon::now()->format('YmdHis');
        $totalLineProductComponentsQuantity = $manufactureProductLineProductComponents->sum('quantity');
        $totalLineProductsQuantity = $manufactureProductLineProducts->sum('quantity');

        /** @var ManufactureProduct */
        $manufactureProduct = new ManufactureProduct([
            'created_at' => $data['created_at'],
            'created_by' => $authenticatedUser->id,
            'branch_id' => $data['branch_id'],
            'order_number' => $orderNumber,
            'total_line_product_components_quantity' => $totalLineProductComponentsQuantity,
            'total_line_products_quantity' => $totalLineProductsQuantity,
        ]);

        $manufactureProduct->save();

        foreach ($manufactureProductLineProductComponents as $manufactureProductLineProductComponent) {
            $manufactureProductLineProductComponent->manufacture_product_id = $manufactureProduct->id;
            $manufactureProductLineProductComponent->save();

            $productComponentInventory = ProductComponentInventory::query()
                ->where('branch_id', $manufactureProduct->branch_id)
                ->where('product_component_id', $manufactureProductLineProductComponent->product_component_id)
                ->first();

            $productComponentInventory->quantity -= $manufactureProductLineProductComponent->quantity;
            $productComponentInventory->save();
        }

        foreach ($manufactureProductLineProducts as $manufactureProductLineProduct) {
            $manufactureProductLineProduct->manufacture_product_id = $manufactureProduct->id;
            $manufactureProductLineProduct->save();

            /** @var ProductInventory */
            $productInventory = ProductInventory::query()
                ->where('branch_id', $manufactureProduct->branch_id)
                ->where('product_id', $manufactureProductLineProduct->product_id)
                ->first();

            if ($productInventory) {
                $productInventory->quantity += $manufactureProductLineProduct->quantity;
                $productInventory->save();
            } else {
                ProductInventory::create([
                    'branch_id' => $manufactureProduct->branch_id,
                    'product_id' => $manufactureProductLineProduct->product_id,
                    'quantity' => $manufactureProductLineProduct->quantity,
                ]);
            }
        }

        DB::commit();

        return $manufactureProduct;
    }
}
