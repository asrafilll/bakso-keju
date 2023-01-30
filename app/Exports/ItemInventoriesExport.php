<?php

namespace App\Exports;

use App\Models\ItemInventory;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ItemInventoriesExport implements FromQuery, WithHeadings, WithMapping
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
        $itemInventoryQuery = ItemInventory::query()
            ->select([
                'item_inventories.*',
                'items.name as items_name',
                'branches.name as branch_name',
            ])
            ->join('items', 'item_inventories.item_id', 'items.id')
            ->join('branches', 'item_inventories.branch_id', 'branches.id')
            ->join('branch_users', 'item_inventories.branch_id', 'branch_users.branch_id')
            ->where('branch_users.user_id', data_get($this->data, 'user_id'));

        $term = data_get($this->data, 'term');
        if ($term) {
            $itemInventoryQuery->where(function ($query) use ($term) {
                $searchables = [
                    'items.name',
                    'branches.name',
                    'users.name',
                    'item_inventories.quantity',
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
                $itemInventoryQuery->where($filterable, $filterValue);
            }
        }

        $itemInventoryQuery->orderByDesc('items.id');

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
            __('Product'),
            __('Quantity'),
        ];
    }
}
