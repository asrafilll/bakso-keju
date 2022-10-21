<?php

namespace Database\Seeders;

use App\Actions\CreateOrderAction;
use App\Models\Branch;
use App\Models\OrderSource;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /** @var EloquentCollection<Branch> */
        $branches = Branch::all();
        /** @var EloquentCollection<OrderSource> */
        $orderSources = OrderSource::all();
        /** @var CreateOrderAction */
        $createOrderAction = app(CreateOrderAction::class);

        foreach ($branches as $branch) {
            foreach ($orderSources as $orderSource) {
                $lineItems = Product::query()
                    ->select(['products.id'])
                    ->join('product_inventories', 'product_inventories.product_id', 'products.id')
                    ->where('product_inventories.branch_id', $branch->id)
                    ->inRandomOrder()
                    ->take(3)
                    ->get()
                    ->map(function ($product) {
                        return [
                            'product_id' => $product->id,
                            'quantity' => random_int(1, 10),
                        ];
                    })
                    ->toArray();

                $createOrderAction->execute([
                    'created_at' => Carbon::now()->addDays(random_int(-14, 14))->format('Y-m-d H:i:s'),
                    'branch_id' => $branch->id,
                    'order_source_id' => $orderSource->id,
                    'customer_name' => 'John Doe',
                    'line_items' => $lineItems
                ]);
            }
        }
    }
}
