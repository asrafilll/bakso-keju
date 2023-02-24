<?php

namespace App\Exports;

use App\Models\ProductComponentInventory;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ManufactureComponentInventoriesExport implements FromQuery, WithHeadings, WithMapping
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
        $productComponentInventoryQuery = ProductComponentInventory::query()
            ->select([
                'product_component_inventories.*',
                'product_components.name as product_component_name',
                'branches.name as branch_name',
            ])
            ->join('product_components', 'product_component_inventories.product_component_id', 'product_components.id')
            ->join('branches', 'product_component_inventories.branch_id', 'branches.id')
            ->join('branch_users', 'product_component_inventories.branch_id', 'branch_users.branch_id')
            ->where('branch_users.user_id', data_get($this->data, 'user_id'));

        $term = data_get($this->data, 'term');

        if ($term) {
            $productComponentInventoryQuery->where(function ($query) use ($term) {
                $searchables = [
                    'product_components.name',
                    'branches.name',
                    'product_component_inventories.quantity',
                ];

                foreach ($searchables as $searchable) {
                    $query->orWhere($searchable, 'LIKE', "%{$term}%");
                }
            });
        }

        $filterables = [
            'product_component_inventories.branch_id' => 'branch_id',
        ];

        foreach ($filterables as $filterKey => $filterable) {
            $filterValue = data_get($this->data, $filterable);

            if ($filterValue) {
                $productComponentInventoryQuery->where($filterKey, $filterValue);
            }
        }

        $productComponentInventoryQuery->orderByDesc('product_components.id');

        return $productComponentInventoryQuery;
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
            $row->product_component_name,
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
            __('Product'),
            __('Quantity'),
        ];
    }
}
