<?php

namespace App\Actions;

use App\Models\Branch;
use App\Models\Inventory;
use App\Models\ProductInventory;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;

class CreateInventoryAction
{
    /**
     * @param array $data
     * @param User $authenticatedUser
     * @return Inventory
     */
    public function execute(array $data, User $authenticatedUser)
    {
        DB::beginTransaction();

        /** @var Branch */
        $branch = Branch::find(data_get($data, 'branch_id'));

        if (!$authenticatedUser->hasRegisteredToBranch($branch)) {
            throw new Exception(
                __("You are not registered to this branch"),
                422
            );
        }

        /** @var Inventory */
        $inventory = Inventory::create([
            'branch_id' => $data['branch_id'],
            'product_id' => $data['product_id'],
            'quantity' => $data['quantity'],
            'note' => $data['note'],
            'created_by' => $authenticatedUser->id,
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
