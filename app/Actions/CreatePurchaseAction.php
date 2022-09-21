<?php

namespace App\Actions;

use App\Models\Branch;
use App\Models\Purchase;
use App\Models\PurchaseLineItem;
use App\Models\Item;
use App\Models\ItemInventory;
use Exception;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CreatePurchaseAction
{
    /**
     * @param array $data
     * @return Purchase
     */
    public function execute(array $data)
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

        /** @var Collection */
        $lineItems = new Collection($data['line_items']);
        /** @var array<string> */
        $lineItemsItemIDs = $lineItems->pluck('item_id')->toArray();
        /** @var EloquentCollection<Item> */
        $items = Item::query()
            ->whereIn('items.id', $lineItemsItemIDs)
            ->get();
        /** @var Collection */
        $purchaseLineItems = new Collection();

        foreach ($lineItems as $lineItem) {
            $item = $items->firstWhere('id', $lineItem['item_id']);

            if (!$item) {
                throw new Exception(
                    __("Some item not found"),
                    422
                );
            }

            $quantity = intval($lineItem['quantity']);

            $purchaseLineItems->push(new PurchaseLineItem([
                'item_id' => $item->id,
                'item_name' => $item->name,
                'price' => $item->price,
                'quantity' => $quantity,
                'total' => $item->price * $quantity,
            ]));
        }

        // $purchaseNumber = implode('', [
        //     $branch->purchase_number_prefix,
        //     '-',
        //     Carbon::now()->format('Ym'),
        //     str_pad(
        //         $branch->next_purchase_number,
        //         4,
        //         '0',
        //         STR_PAD_LEFT
        //     )
        // ]);
        $purchaseNumber = Carbon::now()->format('Ymd');

        $totalLineItemsQuantity = $purchaseLineItems->sum('quantity');
        $totalLineItemsPrice = $purchaseLineItems->sum('total');
        $totalPrice = $totalLineItemsPrice;

        /** @var Purchase */
        $purchase = new Purchase([
            'branch_id' => $data['branch_id'],
            'customer_name' => $data['customer_name'],
            'purchase_number' => $purchaseNumber,
            'total_line_items_quantity' => $totalLineItemsQuantity,
            'total_line_items_price' => $totalLineItemsPrice,
            'total_price' => $totalPrice,
        ]);

        $purchase->save();
        $purchaseLineItems->each(function ($purchaseLineItem) use ($purchase) {
            $purchaseLineItem->purchase_id = $purchase->id;
            $purchaseLineItem->save();

            /** @var ItemInventory */
            $itemInventory = ItemInventory::query()
                ->where([
                    'branch_id' => $purchase->branch_id,
                    'item_id' => $purchaseLineItem->item_id,
                ])
                ->first();

            if ($itemInventory) {
                $itemInventory->quantity += $purchaseLineItem->quantity;
                $itemInventory->save();
            } else {
                ItemInventory::create([
                    'branch_id' => $purchase->branch_id,
                    'item_id' => $purchaseLineItem->item_id,
                    'quantity' => $purchaseLineItem->quantity,
                ]);
            }
        });
        // $branch->next_purchase_number++;
        // $branch->save();

        DB::commit();

        return $purchase;
    }
}
