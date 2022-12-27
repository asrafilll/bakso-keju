<?php

namespace App\Exports;

use App\Models\ProductInventory;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProductInventoriesExport implements FromQuery, WithHeadings, WithMapping
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
        $productInventoryQuery = ProductInventory::query()
            ->select([
                'product_inventories.*',
                'products.name as product_name',
                'branches.name as branch_name',
            ])
            ->join('products', 'product_inventories.product_id', 'products.id')
            ->join('branches', 'product_inventories.branch_id', 'branches.id')
            ->join('branch_users', 'product_inventories.branch_id', 'branch_users.branch_id')
            ->where('branch_users.user_id', data_get($this->data, 'user_id'));

        $term = data_get($this->data, 'term');

        if ($term) {
            $productInventoryQuery->where(function ($query) use ($term) {
                $searchables = [
                    'products.name',
                    'branches.name',
                    'product_inventories.quantity',
                ];

                foreach ($searchables as $searchable) {
                    $query->orWhere($searchable, 'LIKE', "%{$term}%");
                }
            });
        }

        $filterables = [
            'branch_id',
        ];

        foreach ($filterables as $filterable) {
            $filterValue = data_get($this->data, $filterable);

            if ($filterValue) {
                $productInventoryQuery->where($filterable, $filterValue);
            }
        }

        $productInventoryQuery->orderByDesc('product_inventories.id');

        return $productInventoryQuery;
    }

    /**
     * @param  mixed  $row
     * @return array
     */
    public function map($row): array
    {
        return [
            $row->updated_at->format('m/d/Y H:i'),
            $row->branch_name,
            $row->product_name,
            $row->quantity,
        ];
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            __('Date updated'),
            __('Branch'),
            __('Product'),
            __('Quantity'),
        ];
    }
}
