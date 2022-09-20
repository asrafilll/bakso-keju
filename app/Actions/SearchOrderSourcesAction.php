<?php

namespace App\Actions;

use App\Models\OrderSource;
use Illuminate\Database\Eloquent\Collection;

class SearchOrderSourcesAction
{
    /**
     * @param string $term
     * @return Collection<OrderSource>
     */
    public function execute($term)
    {
        return OrderSource::query()
            ->where('name', 'LIKE', "%{$term}%")
            ->orderBy('name')
            ->get();
    }
}
