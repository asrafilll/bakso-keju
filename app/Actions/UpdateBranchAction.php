<?php

namespace App\Actions;

use App\Models\Branch;

class UpdateBranchAction
{
    /**
     * @param Branch $branch
     * @param array $data
     * @return Branch
     */
    public function execute(Branch $branch, array $data): Branch
    {
        if (array_key_exists('is_main', $data) && boolval($data['is_main'])) {
            Branch::query()->update([
                'is_main' => false,
            ]);
        }

        $branch->update($data);

        return $branch;
    }
}
