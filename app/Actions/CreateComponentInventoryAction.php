<?php

namespace App\Actions;

use App\Models\Branch;
use App\Models\ComponentInventory;
use App\Models\ProductComponentInventory;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;

class CreateComponentInventoryAction
{
    /**
     * @param array $data
     * @param User $authenticatedUser
     * @return ComponentInventory
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
        $componentInventory = ComponentInventory::create([
            'branch_id' => $data['branch_id'],
            'product_component_id' => $data['product_component_id'],
            'quantity' => $data['quantity'],
        ]);
        /** @var ProductComponentInventory|null */
        $productComponentInventory = ProductComponentInventory::query()
            ->where([
                'branch_id' => $componentInventory->branch_id,
                'product_component_id' => $componentInventory->product_component_id,
            ])
            ->first();

        if ($productComponentInventory) {
            $productComponentInventory->update([
                'quantity' => $productComponentInventory->quantity + $componentInventory->quantity,
            ]);
        } else {
            ProductComponentInventory::create([
                'branch_id' => $data['branch_id'],
                'product_component_id' => $data['product_component_id'],
                'quantity' => $data['quantity'],
            ]);
        }

        DB::commit();

        return $componentInventory;
    }
}
