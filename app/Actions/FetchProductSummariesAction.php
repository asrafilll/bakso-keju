<?php

namespace App\Actions;

use App\Models\Branch;
use App\Models\OrderSource;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class FetchProductSummariesAction
{
    /**
     * @param mixed $fromDate
     * @param mixed $toDate
     * @return array<string, mixed>
     */
    public function execute(
        $fromDate = null,
        $toDate = null
    ) {
        $fromDate = $fromDate ?: Carbon::now()->format('Y-m-d');
        $toDate = $toDate ?: Carbon::now()->format('Y-m-d');
        $productSummaries = $this->getProductSummaries($fromDate, $toDate);
        $branchesWithOrderSources = $this->getBranchesWithOrderSources();

        return [
            'branches' => $branchesWithOrderSources,
            'products' => $this->transform(
                $productSummaries,
                $branchesWithOrderSources
            )
        ];
    }

    /**
     * @param string $fromDate
     * @param string $toDate
     * @return Collection
     */
    private function getProductSummaries($fromDate, $toDate)
    {
        return Collection::make(
            DB::select("
                SELECT
                    products.id as product_id,
                    CONCAT_WS(' - ', sub_product_categories.name, products.name) as product_name,
                    order_summaries.branch_id,
                    order_summaries.branch_name,
                    order_summaries.order_source_id,
                    order_summaries.order_source_name,
                    IFNULL(SUM(order_summaries.quantity), 0) as total_quantity,
                    IFNULL(SUM(order_summaries.total), 0) as total_price
                FROM
                    products
                LEFT JOIN product_categories sub_product_categories ON
                    products.product_category_id = sub_product_categories.id
                LEFT JOIN (
                    SELECT
                        order_line_items.product_id,
                        order_line_items.quantity,
                        order_line_items.total,
                        orders.branch_id,
                        branches.name as branch_name,
                        orders.order_source_id,
                        order_sources.name as order_source_name
                    FROM
                        order_line_items
                    JOIN orders ON
                        order_line_items.order_id = orders.id
                    JOIN branches on
                        orders.branch_id = branches.id
                    JOIN order_sources on
                        orders.order_source_id = order_sources.id
                    WHERE
                        DATE(orders.created_at) >= ?
                            AND DATE(orders.created_at) <= ?
                        ) as order_summaries ON
                        products.id = order_summaries.product_id
                GROUP BY
                    order_summaries.branch_id,
                    order_summaries.order_source_id,
                    products.id
                ORDER BY
                    product_name ASC,
                    branch_name ASC,
                    order_source_name ASC;
            ", [
                $fromDate,
                $toDate
            ]),
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function getBranchesWithOrderSources()
    {
        /** @var EloquentCollection<Branch> */
        $branchCollection = Branch::query()
            ->orderBy('name')
            ->get();
        /** @var EloquentCollection<OrderSource> */
        $orderSourceCollection = OrderSource::query()
            ->orderBy('name')
            ->get();

        $branches = [];

        foreach ($branchCollection as $branchItem) {
            $branches[$branchItem->id] = [
                'id' => $branchItem->id,
                'name' => $branchItem->name,
                'total_quantity' => 0,
                'idr_total_quantity' => '0',
                'total_price' => 0,
                'idr_total_price' => '0',
                'order_sources' => [],
            ];
            foreach ($orderSourceCollection as $orderSourceItem) {
                $branches[$branchItem->id]['order_sources'][$orderSourceItem->id] = [
                    'id' => $orderSourceItem->id,
                    'name' => $orderSourceItem->name,
                    'total_quantity' => 0,
                    'idr_total_quantity' => '0',
                    'total_price' => 0,
                    'idr_total_price' => '0',
                ];
            }
        }

        return $branches;
    }

    /**
     * @param Collection $productSummaries
     * @param array<int, mixed> $branchesWithOrderSources
     * @return array<string, mixed>
     */
    private function transform(
        $productSummaries,
        $branchesWithOrderSources
    ) {
        $productSummariesMap = [];

        foreach ($productSummaries as $productSummary) {
            if (!array_key_exists($productSummary->product_id, $productSummariesMap)) {
                $productSummariesMap[$productSummary->product_id] = [
                    'product_id' => $productSummary->product_id,
                    'product_name' => $productSummary->product_name,
                    'total_quantity' => 0,
                    'idr_total_quantity' => '0',
                    'total_price' => 0,
                    'idr_total_price' => '0',
                    'branches' => $branchesWithOrderSources,
                ];
            }

            if (is_null($productSummary->branch_id)) {
                continue;
            }

            $totalQuantity = intval($productSummary->total_quantity);
            $totalPrice = intval($productSummary->total_price);
            $productSummariesMap[$productSummary->product_id]['total_quantity'] += $totalQuantity;
            $productSummariesMap[$productSummary->product_id]['idr_total_quantity'] = $this->getIdrCurrency(
                $productSummariesMap[$productSummary->product_id]['total_quantity']
            );
            $productSummariesMap[$productSummary->product_id]['total_price'] += $totalPrice;
            $productSummariesMap[$productSummary->product_id]['idr_total_price'] = $this->getIdrCurrency(
                $productSummariesMap[$productSummary->product_id]['total_price']
            );
            $productSummariesMap[$productSummary->product_id]['branches'][$productSummary->branch_id]['total_quantity'] += $totalQuantity;
            $productSummariesMap[$productSummary->product_id]['branches'][$productSummary->branch_id]['idr_total_quantity'] = $this->getIdrCurrency(
                $productSummariesMap[$productSummary->product_id]['branches'][$productSummary->branch_id]['total_quantity']
            );
            $productSummariesMap[$productSummary->product_id]['branches'][$productSummary->branch_id]['total_price'] += $totalPrice;
            $productSummariesMap[$productSummary->product_id]['branches'][$productSummary->branch_id]['idr_total_price'] = $this->getIdrCurrency(
                $productSummariesMap[$productSummary->product_id]['branches'][$productSummary->branch_id]['total_price']
            );
            $productSummariesMap[$productSummary->product_id]['branches'][$productSummary->branch_id]['order_sources'][$productSummary->order_source_id]['total_quantity'] = $totalQuantity;
            $productSummariesMap[$productSummary->product_id]['branches'][$productSummary->branch_id]['order_sources'][$productSummary->order_source_id]['idr_total_quantity'] = $this->getIdrCurrency(
                $totalQuantity
            );
            $productSummariesMap[$productSummary->product_id]['branches'][$productSummary->branch_id]['order_sources'][$productSummary->order_source_id]['total_price'] = $totalPrice;
            $productSummariesMap[$productSummary->product_id]['branches'][$productSummary->branch_id]['order_sources'][$productSummary->order_source_id]['idr_total_price'] = $this->getIdrCurrency(
                $totalPrice
            );
        }

        return $productSummariesMap;
    }

    /**
     * @param int $value
     * @return string
     */
    private function getIdrCurrency(int $value)
    {
        return number_format(
            $value,
            '0',
            ',',
            '.'
        );
    }
}
