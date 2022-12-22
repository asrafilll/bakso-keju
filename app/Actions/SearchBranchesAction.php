<?php

namespace App\Actions;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class SearchBranchesAction
{
    /**
     * @param string|null $term
     * @param User $user
     * @return Collection<Branch>
     */
    public function execute($term, User $user)
    {
        return Branch::query()
            ->join('branch_users', 'branches.id', 'branch_users.branch_id')
            ->where('branch_users.user_id', $user->id)
            ->where('name', 'LIKE', "%{$term}%")
            ->orderBy('name')
            ->get();
    }
}
