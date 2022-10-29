<?php

namespace App\Actions;

use App\Models\Branch;
use App\Models\ManufacturingOrder;
use App\Models\ManufacturingOrderLineItem;
use App\Models\Order;
use App\Models\ProductComponent;
use App\Models\ProductComponentInventory;
use Exception;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CreateManufacturingOrderAction
{
    /**
     * @param array $data
     * @return Order
     */
    public function execute(array $data)
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

        /** @var Collection */
        $lineItems = new Collection($data['line_items']);
        /** @var array<string> */
        $lineItemsProductComponentIDs = $lineItems->pluck('product_component_id')->toArray();
        /** @var EloquentCollection<ProductComponent> */
        $productComponents = ProductComponent::query()
            ->whereIn('id', $lineItemsProductComponentIDs)
            ->get();
        /** @var Collection */
        $manufacturingOrderLineItems = new Collection();

        foreach ($lineItems as $lineItem) {
            $productComponent = $productComponents->firstWhere('id', $lineItem['product_component_id']);

            if (!$productComponent) {
                throw new Exception(
                    __("Some product component not found"),
                    422
                );
            }

            $price = intval($lineItem['price']);
            $quantity = intval($lineItem['quantity']);
            $totalWeight = floatval($lineItem['total_weight']);

            $manufacturingOrderLineItems->push(new ManufacturingOrderLineItem([
                'product_component_id' => $productComponent->id,
                'product_component_name' => $productComponent->name,
                'price' => $price,
                'quantity' => $quantity,
                'total_weight' => $totalWeight,
                'total_price' => $price * $quantity,
            ]));
        }

        $orderNumber = Carbon::now()->format('YmdHis');
        $totalLineItemsQuantity = $manufacturingOrderLineItems->sum('quantity');
        $totalLineItemsWeight = $manufacturingOrderLineItems->sum('total_weight');
        $totalLineItemsPrice = $manufacturingOrderLineItems->sum('total_price');

        /** @var ManufacturingOrder */
        $manufacturingOrder = new ManufacturingOrder([
            'created_at' => $data['created_at'],
            'created_by' => $data['created_by'],
            'branch_id' => $data['branch_id'],
            'order_number' => $orderNumber,
            'total_line_items_quantity' => $totalLineItemsQuantity,
            'total_line_items_weight' => $totalLineItemsWeight,
            'total_line_items_price' => $totalLineItemsPrice,
        ]);

        $manufacturingOrder->save();

        foreach ($manufacturingOrderLineItems as $manufacturingOrderLineItem) {
            $manufacturingOrderLineItem->manufacturing_order_id = $manufacturingOrder->id;
            $manufacturingOrderLineItem->save();

            $productComponentInventory = ProductComponentInventory::query()
                ->where('branch_id', $manufacturingOrder->branch_id)
                ->where('product_component_id', $manufacturingOrderLineItem->product_component_id)
                ->first();

            if ($productComponentInventory) {
                $productComponentInventory->quantity += $manufacturingOrderLineItem->quantity;
                $productComponentInventory->save();
            } else {
                ProductComponentInventory::create([
                    'branch_id' => $manufacturingOrder->branch_id,
                    'product_component_id' => $manufacturingOrderLineItem->product_component_id,
                    'quantity' => $manufacturingOrderLineItem->quantity,
                ]);
            }
        }

        DB::commit();

        return $manufacturingOrder;
    }
}
