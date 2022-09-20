<?php

namespace App\Actions;

use App\Models\Branch;
use Illuminate\Database\Eloquent\Collection;

class SearchBranchesAction
{
    /**
     * @param string $term
     * @return Collection<Branch>
     */
    public function execute($term)
    {
        return Branch::query()
            ->where('name', 'LIKE', "%{$term}%")
            ->orderBy('name')
            ->get();
    }
}
