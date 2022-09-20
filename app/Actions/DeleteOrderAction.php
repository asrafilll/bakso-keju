<?php

namespace App\Actions;

use App\Models\Order;
use App\Models\OrderLineItem;
use App\Models\ProductInventory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DeleteOrderAction
{
    /**
     * @param Order $order
     * @return Order
     */
    public function execute(Order $order)
    {
        DB::beginTransaction();

        $order->load(['orderLineItems']);
        $order->orderLineItems->each(
            function (OrderLineItem $orderLineItem) use ($order) {
                /** @var ProductInventory */
                $productInventory = ProductInventory::query()
                    ->where('branch_id', $order->branch_id)
                    ->where('product_id', $orderLineItem->product_id)
                    ->first();

                $productInventory->quantity += $orderLineItem->quantity;
                $productInventory->save();
            }
        );

        $order->deleted_at = Carbon::now();
        $order->save();

        DB::commit();

        return $order;
    }
}
