<?php

namespace App\Exports;

use App\Models\Inventory;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class InventoriesExport implements FromQuery, WithHeadings, WithMapping
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
        $inventoryQuery = Inventory::query()
            ->select([
                'inventories.*',
                'products.name as product_name',
                'branches.name as branch_name',
                'users.name as created_by_name'
            ])
            ->join('products', 'inventories.product_id', 'products.id')
            ->join('branches', 'inventories.branch_id', 'branches.id')
            ->join('branch_users', 'inventories.branch_id', 'branch_users.branch_id')
            ->join('users', 'inventories.created_by', 'users.id')
            ->where('branch_users.user_id', data_get($this->data, 'user_id'));

        $term = data_get($this->data, 'term');

        if ($term) {
            $inventoryQuery->where(function ($query) use ($term) {
                $searchables = [
                    'products.name',
                    'branches.name',
                    'users.name',
                    'inventories.quantity',
                ];

                foreach ($searchables as $searchable) {
                    $query->orWhere($searchable, 'LIKE', "%{$term}%");
                }
            });
        }

        $filterables = [
            'inventories.branch_id' => 'branch_id',
        ];

        foreach ($filterables as $filterKey => $filterable) {
            $filterValue = data_get($this->data, $filterable);

            if ($filterValue) {
                $inventoryQuery->where($filterKey, $filterValue);
            }
        }

        $inventoryQuery->orderByDesc('inventories.id');

        return $inventoryQuery;
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
            $row->product_name,
            $row->quantity,
            $row->note,
            $row->created_by_name,
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
            __('Note'),
            __('Created by'),
        ];
    }
}
