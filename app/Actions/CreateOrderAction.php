<?php

namespace App\Actions;

use App\Models\Branch;
use App\Models\Order;
use App\Models\OrderLineHamper;
use App\Models\OrderLineHampers;
use App\Models\OrderLineItem;
use App\Models\OrderSource;
use App\Models\Product;
use App\Models\ProductHamper;
use App\Models\ProductInventory;
use App\Models\Reseller;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CreateOrderAction
{
    /**
     * @param array $data
     * @param User $authenticatedUser
     * @return Order
     */
    public function execute(array $data, User $authenticatedUser)
    {
        DB::beginTransaction();

        /** @var Branch */
        $branch = Branch::find($data['branch_id']);

        if (!$branch) {
            throw ValidationException::withMessages([
                'branch_id' => __('validation.exists', [
                    'attribute' => 'branch_id'
                ]),
            ]);
        }

        if (!$authenticatedUser->hasRegisteredToBranch($branch)) {
            throw new Exception(
                __("You are not registered to this branch"),
                422
            );
        }

        /** @var OrderSource */
        $orderSource = OrderSource::find($data['order_source_id']);

        if (!$orderSource) {
            throw ValidationException::withMessages([
                'order_source_id' => __('validation.exists', [
                    'attribute' => 'order_source_id'
                ]),
            ]);
        }

        $reseller = null;

        if (isset($data['reseller_id']) && !empty($data['reseller_id'])) {
            $reseller = Reseller::find($data['reseller_id']);

            if (!$reseller) {
                throw ValidationException::withMessages([
                    'reseller_id' => __('validation.exists', [
                        'attribute' => 'reseller_id',
                    ])
                ]);
            }
        }


        /** @var Collection */
        $orderLineItems = new Collection();

        if (!empty($data['line_items'])) {
            /** @var Collection */
            $lineItems = new Collection($data['line_items']);
            /** @var array<string> */
            $lineItemsProductIDs = $lineItems->pluck('product_id')->toArray();
            /** @var EloquentCollection<Product> */
            $products = Product::query()
                ->select([
                    'products.*',
                    DB::raw('IFNULL(product_prices.price, products.price) as active_price'),
                    'product_inventories.quantity',
                ])
                ->join('product_inventories', 'product_inventories.product_id', 'products.id')
                ->leftJoin('product_prices', function ($join) use ($orderSource) {
                    $join
                        ->on('products.id', '=', 'product_prices.product_id')
                        ->where('product_prices.order_source_id', $orderSource->id)
                        ->where('product_prices.price', '>', 0);
                })
                ->where('product_inventories.branch_id', $branch->id)
                ->where('product_inventories.quantity', '>', 0)
                ->whereIn('products.id', $lineItemsProductIDs)
                ->get();

            foreach ($lineItems as $lineItem) {
                $product = $products->firstWhere('id', $lineItem['product_id']);

                if (!$product) {
                    throw new Exception(
                        __("Some product not found"),
                        422
                    );
                }

                $quantity = intval($lineItem['quantity']);

                if ($product->quantity < $quantity) {
                    throw new Exception(
                        __("{$product->name} doesn't have enough quantity"),
                        422
                    );
                }

                $orderLineItems->push(new OrderLineItem([
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'price' => $product->active_price,
                    'quantity' => $quantity,
                    'total' => $product->active_price * $quantity,
                ]));
            }
        }

        /** @var Collection */
        $orderLineHampers = new Collection();
        $qtyHamperProduct = 0;
        $totalHamperProduct = 0;

        if (!empty($data['products'])) {
            $lineHampers = new Collection($data['products']);

            foreach ($lineHampers as $hamper) {
                $hamperId = $hamper['product_hamper_id'];
                $qty = $hamper['quantity'];

                $productHamper = ProductHamper::find($hamperId);

                $items = $productHamper->productHamperLines;

                foreach ($items as $item) {
                    $qtyHamperProduct += $item->quantity;
                }

                $totalHamperProduct += $productHamper->price * $qty;

                $orderLineHampers->push(new OrderLineHamper([
                    'product_hamper_id' => $productHamper->id,
                    'hamper_name' => $productHamper->name,
                    'price' => $productHamper->price,
                    'quantity' => $qty,
                    'total' => $productHamper->price * $qty,
                ]));
            }
        }

        $orderNumber = implode('', [
            $branch->order_number_prefix,
            '-',
            Carbon::now()->format('Ym'),
            str_pad(
                $branch->next_order_number,
                4,
                '0',
                STR_PAD_LEFT
            )
        ]);

        $resellerOrder = !is_null($reseller);
        $resellerId = $resellerOrder ? $reseller->id : null;
        $percentageDiscount = $resellerOrder ? $reseller->percentage_discount : 0;
        $totalLineItemsQuantity = $orderLineItems->isEmpty() ? $qtyHamperProduct : ($orderLineItems->sum('quantity') + $qtyHamperProduct);
        $totalLineItemsPrice = $orderLineItems->isEmpty() ? $totalHamperProduct : ($orderLineItems->sum('total') + $totalHamperProduct);
        $totalDiscount = round($totalLineItemsPrice * ($percentageDiscount / 100));
        $totalPrice = $totalLineItemsPrice - $totalDiscount;

        /** @var Order */
        $order = new Order([
            'created_at' => data_get($data, 'created_at'),
            'branch_id' => data_get($data, 'branch_id'),
            'order_source_id' => data_get($data, 'order_source_id'),
            'customer_name' => data_get($data, 'customer_name'),
            'customer_phone_number' => data_get($data, 'customer_phone_number'),
            'reseller_order' => $resellerOrder,
            'reseller_id' => $resellerId,
            'order_number' => $orderNumber,
            'percentage_discount' => $percentageDiscount,
            'total_discount' => $totalDiscount,
            'total_line_items_quantity' => $totalLineItemsQuantity,
            'total_line_items_price' => $totalLineItemsPrice,
            'total_price' => $totalPrice,
        ]);

        $order->save();
        $orderLineItems->each(function ($orderLineItem) use ($order) {
            $orderLineItem->order_id = $order->id;
            $orderLineItem->save();

            /** @var ProductInventory */
            $productInventory = ProductInventory::query()
                ->where([
                    'branch_id' => $order->branch_id,
                    'product_id' => $orderLineItem->product_id,
                ])
                ->first();

            $productInventory->quantity -= $orderLineItem->quantity;
            $productInventory->save();
        });

        $orderLineHampers->each(function ($orderLineHamper) use ($order) {
            $orderLineHamper->order_id = $order->id;
            $orderLineHamper->save();

            /** @var ProductHamper */
            $productHampers = ProductHamper::query()
                ->where([
                    'branch_id' => $order->branch_id,
                    'id' => $orderLineHamper->product_hamper_id,
                ])->first();

            foreach ($productHampers->productHamperLines as $product) {
                /** @var ProductInventory */
                $productInventory = ProductInventory::query()
                    ->where([
                        'branch_id' => $order->branch_id,
                        'product_id' => $product->product_id,
                    ])
                    ->first();

                $productInventory->quantity -= $product->quantity;
                $productInventory->save();
            }
        });

        $branch->next_order_number++;
        $branch->save();

        DB::commit();

        return $order;
    }
}
