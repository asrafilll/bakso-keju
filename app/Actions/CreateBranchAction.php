<?php

namespace App\Actions;

use App\Models\Branch;

class CreateBranchAction
{
    /**
     * @param array $data
     * @return Branch
     */
    public function execute(array $data): Branch
    {
        if (array_key_exists('is_main', $data) && boolval($data['is_main'])) {
            Branch::query()->update([
                'is_main' => false,
            ]);
        }

        return Branch::create($data);
    }
}
