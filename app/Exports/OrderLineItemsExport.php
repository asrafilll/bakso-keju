<?php

namespace App\Exports;

use App\Models\OrderLineItem;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class OrderLineItemsExport implements FromQuery, WithHeadings, WithMapping
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
        $orderLineItemQuery = OrderLineItem::query()
            ->select([
                'orders.order_number',
                'orders.created_at',
                'branches.name as branch_name',
                'order_sources.name as order_source_name',
                'orders.customer_name',
                'orders.customer_phone_number',
                'orders.percentage_discount',
                'orders.total_discount',
                'order_line_items.product_name',
                'order_line_items.price',
                'order_line_items.quantity',
                'order_line_items.total',
            ])
            ->join('orders', 'order_line_items.order_id', 'orders.id')
            ->join('order_sources', 'orders.order_source_id', 'order_sources.id')
            ->join('branches', 'orders.branch_id', 'branches.id')
            ->join('branch_users', 'branches.id', 'branch_users.branch_id')
            ->where('branch_users.user_id', data_get($this->data, 'user_id'));

        $term = data_get($this->data, 'term');

        if ($term) {
            $orderLineItemQuery->where(function ($query) use ($term) {
                $searchables = [
                    'orders.order_number',
                    'branches.name',
                    'order_sources.name',
                    'orders.customer_name',
                ];

                foreach ($searchables as $searchable) {
                    $query->orWhere($searchable, 'LIKE', "%{$term}%");
                }
            });
        }

        $filterables = [
            'branch_id',
            'order_source_id',
        ];

        foreach ($filterables as $filterable) {
            $filterValue = data_get($this->data, $filterable);

            if ($filterValue) {
                $orderLineItemQuery->where($filterable, $filterValue);
            }
        }

        $startCreatedAt = data_get($this->data, 'start_created_at');

        if ($startCreatedAt) {
            $orderLineItemQuery->whereRaw('DATE(orders.created_at) >= ?', [
                $startCreatedAt,
            ]);
        }

        $endCreatedAt = data_get($this->data, 'end_created_at');

        if ($endCreatedAt) {
            $orderLineItemQuery->whereRaw('DATE(orders.created_at) <= ?', [
                $endCreatedAt,
            ]);
        }

        $orderLineItemQuery->orderByDesc('orders.id');

        return $orderLineItemQuery;
    }

    /**
     * @param  mixed  $row
     * @return array
     */
    public function map($row): array
    {
        return [
            $row->order_number,
            $row->created_at->format('m/d/Y H:i'),
            $row->branch_name,
            $row->order_source_name,
            $row->customer_name,
            $row->customer_phone_number,
            $row->product_name,
            $row->price,
            $row->quantity,
            $row->percentage_discount,
            $row->total_discount,
            $row->total,
        ];
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
            __('Order source'),
            __('Customer name'),
            __('Customer phone'),
            __('Product'),
            __('Price'),
            __('Quantity'),
            __('Percentage Discount'),
            __('Total Discount'),
            __('Total'),
        ];
    }
}
