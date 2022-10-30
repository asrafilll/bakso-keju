<?php

namespace App\Actions;

use App\Models\ManufactureProduct;
use App\Models\ManufactureProductLineProduct;
use App\Models\ManufactureProductLineProductComponent;
use App\Models\ProductComponentInventory;
use App\Models\ProductInventory;
use Illuminate\Support\Facades\DB;

class DeleteManufactureProductAction
{
    /**
     * @param ManufactureProduct $manufactureProduct
     * @return ManufactureProduct
     */
    public function execute(ManufactureProduct $manufactureProduct)
    {
        DB::beginTransaction();

        $manufactureProduct->load([
            'lineProductComponents',
            'lineProducts',
        ]);
        $manufactureProduct
            ->lineProductComponents
            ->each(function (ManufactureProductLineProductComponent $manufactureProductLineProductComponent) use ($manufactureProduct) {
                /** @var ProductComponentInventory */
                $productComponentInventory = ProductComponentInventory::query()
                    ->where('branch_id', $manufactureProduct->branch_id)
                    ->where('product_component_id', $manufactureProductLineProductComponent->product_component_id)
                    ->first();

                $productComponentInventory->quantity += $manufactureProductLineProductComponent->quantity;
                $productComponentInventory->save();
            });
        $manufactureProduct
            ->lineProducts
            ->each(function (ManufactureProductLineProduct $manufactureProductLineProduct) use ($manufactureProduct) {
                /** @var ProductInventory */
                $productInventory = ProductInventory::query()
                    ->where('branch_id', $manufactureProduct->branch_id)
                    ->where('product_id', $manufactureProductLineProduct->product_id)
                    ->first();

                $productInventory->quantity -= $manufactureProductLineProduct->quantity;
                $productInventory->save();
            });

        $manufactureProduct->lineProductComponents()->delete();
        $manufactureProduct->lineProducts()->delete();
        $manufactureProduct->delete();

        DB::commit();

        return $manufactureProduct;
    }
}
