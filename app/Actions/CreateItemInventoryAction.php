<?php

namespace App\Actions;

use App\Models\Branch;
use App\Models\Item;
use App\Models\ItemInventory;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;

class CreateItemInventoryAction
{
    /**
     * @param array $data
     * @param User $authenticatedUser
     * @return ItemInventory
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

        /** @var ItemInventory */
        $inventory = ItemInventory::create([
            'branch_id' => $data['branch_id'],
            'item_id' => $data['item_id'],
            'quantity' => $data['quantity'],
        ]);

        /** @var Item|null */
        $item = Item::query()
            ->where(['id' => $inventory->item_id])
            ->first();

        $item->update([
            'quantity' => $item->quantity + $inventory->quantity,
        ]);

        DB::commit();

        return $inventory;
    }
}
