<?php

namespace App\Exports;

use App\Models\PurchaseLineItem;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PurchaseLineItemsExport implements FromQuery, WithHeadings, WithMapping
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
        $purchaseLineItemQuery = PurchaseLineItem::query()
            ->select([
                'purchases.purchase_number',
                'purchases.created_at',
                'branches.name as branch_name',
                'purchases.customer_name',
                'purchase_line_items.item_name',
                'purchase_line_items.price',
                'purchase_line_items.quantity',
                'purchase_line_items.total',
            ])
            ->join('purchases', 'purchase_line_items.purchase_id', 'purchases.id')
            ->join('branches', 'purchases.branch_id', 'branches.id')
            ->join('branch_users', 'branches.id', 'branch_users.branch_id')
            ->where('branch_users.user_id', data_get($this->data, 'user_id'));

        $term = data_get($this->data, 'term');

        if ($term) {
            $purchaseLineItemQuery->where(function ($query) use ($term) {
                $searchables = [
                    'purchases.purchase_number',
                    'purchases.customer_name',
                    'branches.name',
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
                $purchaseLineItemQuery->where($filterable, $filterValue);
            }
        }

        $startCreatedAt = data_get($this->data, 'start_created_at');

        if ($startCreatedAt) {
            $purchaseLineItemQuery->whereRaw('DATE(purchases.created_at) >= ?', [
                $startCreatedAt,
            ]);
        }

        $endCreatedAt = data_get($this->data, 'end_created_at');

        if ($endCreatedAt) {
            $purchaseLineItemQuery->whereRaw('DATE(purchases.created_at) <= ?', [
                $endCreatedAt,
            ]);
        }

        $purchaseLineItemQuery->orderByDesc('purchases.id');

        return $purchaseLineItemQuery;
    }

    /**
     * @param  mixed  $row
     * @return array
     */
    public function map($row): array
    {
        return [
            $row->purchase_number,
            $row->created_at->format('m/d/Y H:i'),
            $row->branch_name,
            $row->customer_name,
            $row->item_name,
            $row->price,
            $row->quantity,
            $row->total,
        ];
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            __('Purchase number'),
            __('Date created'),
            __('Branch'),
            __('Customer name'),
            __('Item'),
            __('Price'),
            __('Quantity'),
            __('Total'),
        ];
    }
}
