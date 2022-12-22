<?php

namespace App\Actions;

use App\Models\Branch;
use Illuminate\Support\Facades\DB;

class CreateBranchAction
{
    /**
     * @param array $data
     * @return Branch
     */
    public function execute(array $data): Branch
    {
        DB::beginTransaction();

        if (array_key_exists('is_main', $data) && boolval($data['is_main'])) {
            Branch::query()->update([
                'is_main' => false,
            ]);
        }

        /** @var Branch */
        $branch = Branch::create($data);

        $userIDs = data_get($data, 'user_ids');

        if ($userIDs) {
            $branch
                ->users()
                ->createMany(
                    array_map(fn ($id) => ['user_id' => $id], $userIDs)
                );
        }

        DB::commit();

        return $branch;
    }
}
