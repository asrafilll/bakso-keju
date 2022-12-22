<?php

namespace App\Actions;

use App\Models\Branch;
use App\Models\ManufactureProductComponent;
use App\Models\ManufactureProductComponentLineItem;
use App\Models\Order;
use App\Models\ProductComponent;
use App\Models\ProductComponentInventory;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CreateManufactureProductComponentAction
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

        /** @var Collection */
        $lineItems = new Collection($data['line_items']);
        /** @var array<string> */
        $lineItemsProductComponentIDs = $lineItems->pluck('product_component_id')->toArray();
        /** @var EloquentCollection<ProductComponent> */
        $productComponents = ProductComponent::query()
            ->whereIn('id', $lineItemsProductComponentIDs)
            ->get();
        /** @var Collection */
        $manufactureProductComponentLineItems = new Collection();

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

            $manufactureProductComponentLineItems->push(new ManufactureProductComponentLineItem([
                'product_component_id' => $productComponent->id,
                'product_component_name' => $productComponent->name,
                'price' => $price,
                'quantity' => $quantity,
                'total_weight' => $totalWeight,
                'total_price' => $price * $quantity,
            ]));
        }

        $orderNumber = Carbon::now()->format('YmdHis');
        $totalLineItemsQuantity = $manufactureProductComponentLineItems->sum('quantity');
        $totalLineItemsWeight = $manufactureProductComponentLineItems->sum('total_weight');
        $totalLineItemsPrice = $manufactureProductComponentLineItems->sum('total_price');

        /** @var ManufactureProductComponent */
        $manufactureProductComponent = new ManufactureProductComponent([
            'created_at' => $data['created_at'],
            'created_by' => $authenticatedUser->id,
            'branch_id' => $data['branch_id'],
            'order_number' => $orderNumber,
            'total_line_items_quantity' => $totalLineItemsQuantity,
            'total_line_items_weight' => $totalLineItemsWeight,
            'total_line_items_price' => $totalLineItemsPrice,
        ]);

        $manufactureProductComponent->save();

        foreach ($manufactureProductComponentLineItems as $manufactureProductComponentLineItem) {
            $manufactureProductComponentLineItem->manufacture_product_component_id = $manufactureProductComponent->id;
            $manufactureProductComponentLineItem->save();

            $productComponentInventory = ProductComponentInventory::query()
                ->where('branch_id', $manufactureProductComponent->branch_id)
                ->where('product_component_id', $manufactureProductComponentLineItem->product_component_id)
                ->first();

            if ($productComponentInventory) {
                $productComponentInventory->quantity += $manufactureProductComponentLineItem->quantity;
                $productComponentInventory->save();
            } else {
                ProductComponentInventory::create([
                    'branch_id' => $manufactureProductComponent->branch_id,
                    'product_component_id' => $manufactureProductComponentLineItem->product_component_id,
                    'quantity' => $manufactureProductComponentLineItem->quantity,
                ]);
            }
        }

        DB::commit();

        return $manufactureProductComponent;
    }
}
