<?php

namespace App\Actions;

use App\Models\ManufactureProductComponent;
use App\Models\ManufactureProductComponentLineItem;
use App\Models\ProductComponentInventory;
use Illuminate\Support\Facades\DB;

class DeleteManufactureProductComponentAction
{
    /**
     * @param ManufactureProductComponent $manufactureProductComponent
     * @return ManufactureProductComponent
     */
    public function execute(ManufactureProductComponent $manufactureProductComponent)
    {
        DB::beginTransaction();

        $manufactureProductComponent->load(['manufactureProductComponentLineItems']);
        $manufactureProductComponent
            ->manufactureProductComponentLineItems
            ->each(function (ManufactureProductComponentLineItem $manufactureProductComponentLineItem) use ($manufactureProductComponent) {
                /** @var ProductComponentInventory */
                $productComponentInventory = ProductComponentInventory::query()
                    ->where('branch_id', $manufactureProductComponent->branch_id)
                    ->where('product_component_id', $manufactureProductComponentLineItem->product_component_id)
                    ->first();

                $productComponentInventory->quantity -= $manufactureProductComponentLineItem->quantity;
                $productComponentInventory->save();
            });

        $manufactureProductComponent->manufactureProductComponentLineItems()->delete();
        $manufactureProductComponent->delete();

        DB::commit();

        return $manufactureProductComponent;
    }
}
