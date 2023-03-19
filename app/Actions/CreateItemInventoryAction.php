<?php

namespace App\Actions;

use App\Models\Branch;
use App\Models\Item;
use App\Models\ItemInventory;
use App\Models\ItemInventoryHistory;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;

class CreateItemInventoryAction
{
    /**
     * @param array $data
     * @param User $authenticatedUser
     * @return ItemInventoryHistory
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

        /** @var ItemInventoryHistory */
        $inventory = ItemInventoryHistory::create([
            'branch_id' => $data['branch_id'],
            'item_id' => $data['item_id'],
            'quantity' => $data['quantity'],
        ]);
        /** @var ItemInventory|null */
        $itemInventory = ItemInventory::query()
            ->where([
                'branch_id' => $inventory->branch_id,
                'item_id' => $inventory->item_id,
            ])
            ->first();

        if ($itemInventory) {
            $itemInventory->update([
                'quantity' => $itemInventory->quantity + $inventory->quantity,
            ]);
        } else {
            ItemInventory::create([
                'branch_id' => $data['branch_id'],
                'item_id' => $data['item_id'],
                'quantity' => $data['quantity'],
            ]);
        }

        DB::commit();

        return $inventory;
    }
}
