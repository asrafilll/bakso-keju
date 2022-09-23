<?php

namespace App\Actions;

use App\Models\Inventory;
use App\Models\ProductInventory;
use Illuminate\Support\Facades\DB;

class CreateInventoryAction
{
    /**
     * @param array $data
     * @return Inventory
     */
    public function execute(array $data)
    {
        DB::beginTransaction();

        /** @var Inventory */
        $inventory = Inventory::create([
            'branch_id' => $data['branch_id'],
            'product_id' => $data['product_id'],
            'quantity' => $data['quantity'],
            'note' => $data['note'],
            'created_by' => $data['created_by'],
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
            ProductInventory::create([
                'branch_id' => $data['branch_id'],
                'product_id' => $data['product_id'],
                'quantity' => $data['quantity'],
            ]);
        }

        DB::commit();

        return $inventory;
    }
}
