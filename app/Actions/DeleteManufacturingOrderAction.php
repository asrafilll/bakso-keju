<?php

namespace App\Actions;

use App\Models\ManufacturingOrder;
use App\Models\ManufacturingOrderLineItem;
use App\Models\ProductComponentInventory;
use Illuminate\Support\Facades\DB;

class DeleteManufacturingOrderAction
{
    /**
     * @param ManufacturingOrder $manufacturingOrder
     * @return ManufacturingOrder
     */
    public function execute(ManufacturingOrder $manufacturingOrder)
    {
        DB::beginTransaction();

        $manufacturingOrder->load(['manufacturingOrderLineItems']);
        $manufacturingOrder
            ->manufacturingOrderLineItems
            ->each(function (ManufacturingOrderLineItem $manufacturingOrderLineItem) use ($manufacturingOrder) {
                /** @var ProductComponentInventory */
                $productComponentInventory = ProductComponentInventory::query()
                    ->where('branch_id', $manufacturingOrder->branch_id)
                    ->where('product_component_id', $manufacturingOrderLineItem->product_component_id)
                    ->first();

                $productComponentInventory->quantity -= $manufacturingOrderLineItem->quantity;
                $productComponentInventory->save();
            });

        $manufacturingOrder->manufacturingOrderLineItems()->delete();
        $manufacturingOrder->delete();

        DB::commit();

        return $manufacturingOrder;
    }
}
