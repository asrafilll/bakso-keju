<?php

namespace App\Exports;

use App\Models\ManufactureProductComponentLineItem;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ManufactureProductComponentLineItemsExport implements FromQuery, WithHeadings, WithMapping
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
     * @return Builder|EloquentBuilder|Relation
     */
    public function query()
    {
        $manufactureProductComponentLineItemQuery = ManufactureProductComponentLineItem::query()
            ->select([
                'manufacture_product_components.order_number',
                'manufacture_product_components.created_at',
                'branches.name as branch_name',
                'manufacture_product_component_line_items.product_component_name',
                'manufacture_product_component_line_items.price',
                'manufacture_product_component_line_items.quantity',
                'manufacture_product_component_line_items.total_weight',
                'manufacture_product_component_line_items.total_price',
            ])
            ->join(
                'manufacture_product_components',
                'manufacture_product_component_line_items.manufacture_product_component_id',
                'manufacture_product_components.id'
            )
            ->join(
                'branches',
                'manufacture_product_components.branch_id',
                'branches.id'
            )
            ->join(
                'branch_users',
                'manufacture_product_components.branch_id',
                'branch_users.branch_id'
            )
            ->where('branch_users.user_id', data_get($this->data, 'user_id'));

        $term = data_get($this->data, 'term');

        if ($term) {
            $manufactureProductComponentLineItemQuery->where(function ($query) use ($term) {
                $searchables = [
                    'manufacture_product_components.order_number',
                    'branches.name',
                ];

                foreach ($searchables as $searchable) {
                    $query->orWhere($searchable, 'LIKE', "%{$term}%");
                }
            });
        }

        $filterables = [
            'manufacture_product_components.branch_id' => 'branch_id',
        ];

        foreach ($filterables as $filterKey => $filterable) {
            $filterValue = data_get($this->data, $filterable);

            if ($filterValue) {
                $manufactureProductComponentLineItemQuery->where($filterKey, $filterValue);
            }
        }

        $startCreatedAt = data_get($this->data, 'start_created_at');

        if ($startCreatedAt) {
            $manufactureProductComponentLineItemQuery->whereRaw('DATE(manufacture_product_components.created_at) >= ?', [
                $startCreatedAt,
            ]);
        }

        $endCreatedAt = data_get($this->data, 'end_created_at');

        if ($endCreatedAt) {
            $manufactureProductComponentLineItemQuery->whereRaw('DATE(manufacture_product_components.created_at) <= ?', [
                $endCreatedAt,
            ]);
        }

        $manufactureProductComponentLineItemQuery->orderByDesc('manufacture_product_components.created_at');

        return $manufactureProductComponentLineItemQuery;
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            __('Order number'),
            __('Date created'),
            __('Branch'),
            __('Product component name'),
            __('Price'),
            __('Quantity'),
            __('Total weight'),
            __('Total price'),
        ];
    }

    /**
     * @param  mixed  $row
     * @return array
     */
    public function map($row): array
    {
        return [
            $row->order_number,
            $row->created_at->format('m/D/Y H:i'),
            $row->branch_name,
            $row->product_component_name,
            $row->price,
            $row->quantity,
            $row->total_weight,
            $row->total_price,
        ];
    }
}
