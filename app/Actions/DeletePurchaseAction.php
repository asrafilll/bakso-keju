<?php

namespace App\Actions;

use App\Models\Purchase;
use App\Models\PurchaseLineItem;
use App\Models\ItemInventory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DeletePurchaseAction
{
    /**
     * @param Purchase $purchase
     * @return Purchase
     */
    public function execute(Purchase $purchase)
    {
        DB::beginTransaction();

        $purchase->load(['purchaseLineItems']);
        $purchase->purchaseLineItems->each(
            function (PurchaseLineItem $purchaseLineItem) use ($purchase) {
                /** @var ItemInventory */
                $itemInventory = ItemInventory::query()
                    ->where('branch_id', $purchase->branch_id)
                    ->where('item_id', $purchaseLineItem->item_id)
                    ->first();

                $itemInventory->quantity -= $purchaseLineItem->quantity;
                $itemInventory->save();
            }
        );

        $purchase->deleted_at = Carbon::now();
        $purchase->save();

        DB::commit();

        return $purchase;
    }
}
