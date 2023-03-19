<?php

namespace App\Exports;

use App\Models\ItemInventory;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ItemInventoryHistoriesExport implements FromQuery, WithHeadings, WithMapping
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
                'item_inventory_histories.*',
                'items.name as items_name',
                'branches.name as branch_name',
            ])
            ->join('items', 'item_inventory_histories.item_id', 'items.id')
            ->join('branches', 'item_inventory_histories.branch_id', 'branches.id')
            ->join('branch_users', 'item_inventory_histories.branch_id', 'branch_users.branch_id')
            ->where('branch_users.user_id', data_get($this->data, 'user_id'));

        $term = data_get($this->data, 'term');
        if ($term) {
            $itemInventoryQuery->where(function ($query) use ($term) {
                $searchables = [
                    'items.name',
                    'branches.name',
                    'users.name',
                    'item_inventory_histories.quantity',
                ];

                foreach ($searchables as $searchable) {
                    $query->orWhere($searchable, 'LIKE', "%{$term}%");
                }
            });
        }

        $filterables = [
            'item_inventory_histories.branch_id' => 'branch_id',
        ];

        foreach ($filterables as $filterKey => $filterable) {
            $filterValue = data_get($this->data, $filterable);

            if ($filterValue) {
                $itemInventoryQuery->where($filterKey, $filterValue);
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
            __('Item'),
            __('Quantity'),
        ];
    }
}
