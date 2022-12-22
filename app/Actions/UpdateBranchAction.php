<?php

namespace App\Actions;

use App\Models\Branch;
use Illuminate\Support\Facades\DB;

class UpdateBranchAction
{
    /**
     * @param Branch $branch
     * @param array $data
     * @return Branch
     */
    public function execute(Branch $branch, array $data): Branch
    {
        DB::beginTransaction();

        if (array_key_exists('is_main', $data) && boolval($data['is_main'])) {
            Branch::query()->update([
                'is_main' => false,
            ]);
        }

        $branch->update($data);
        $branch->users()->delete();
        $userIDs = data_get($data, 'user_ids');

        if ($userIDs && count($userIDs) > 0) {
            $branch->users()->createMany(
                array_map(fn ($id) => ['user_id' => $id], $userIDs)
            );
        }

        DB::commit();

        return $branch;
    }
}
