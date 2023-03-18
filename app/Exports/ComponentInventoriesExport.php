<?php

namespace App\Exports;

use App\Models\ComponentInventory;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ComponentInventoriesExport implements FromQuery, WithHeadings, WithMapping
{
    /**
     * @var array<int, string>
     */
    protected $data = [];

    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * @return \Illuminate\Database\Query\Builder
     */
    public function query()
    {
        $itemInventoryQuery = ComponentInventory::query()
            ->select([
                'component_inventories.*',
                'product_components.name as product_components_name',
                'branches.name as branch_name',
            ])
            ->join('product_components', 'component_inventories.product_component_id', 'product_components.id')
            ->join('branches', 'component_inventories.branch_id', 'branches.id')
            ->join('branch_users', 'component_inventories.branch_id', 'branch_users.branch_id')
            ->where('branch_users.user_id', data_get($this->data, 'user_id'));

        $term = data_get($this->data, 'term');
        if ($term) {
            $itemInventoryQuery->where(function ($query) use ($term) {
                $searchables = [
                    'product_components.name',
                    'branches.name',
                    'users.name',
                    'component_inventories.quantity',
                ];

                foreach ($searchables as $searchable) {
                    $query->orWhere($searchable, 'LIKE', "%{$term}%");
                }
            });
        }

        $filterables = [
            'component_inventories.branch_id' => 'branch_id',
        ];

        foreach ($filterables as $filterKey => $filterable) {
            $filterValue = data_get($this->data, $filterable);

            if ($filterValue) {
                $itemInventoryQuery->where($filterKey, $filterValue);
            }
        }

        $itemInventoryQuery->orderByDesc('product_components.id');

        return $itemInventoryQuery;
    }

    /**
     * @param  mixed  $row
     * @return array
     */
    public function map($row): array
    {
        return [
            $row->created_at->format('m/d/Y H:i'),
            $row->branch_name,
            $row->items_name,
            $row->quantity,
        ];
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            __('Date created'),
            __('Branch'),
            __('Component'),
            __('Quantity'),
        ];
    }
}
