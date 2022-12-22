<?php

namespace Database\Seeders;

use App\Actions\CreateInventoryAction;
use App\Models\Branch;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Seeder;

class InventorySeeder extends Seeder
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
        /** @var EloquentCollection<Product> */
        $products = Product::all();
        /** @var User */
        $user = User::first();
        /** @var CreateInventoryAction */
        $createInventoryAction = app(CreateInventoryAction::class);

        $branches->each(function ($branch) use ($products, $user, $createInventoryAction) {
            $products->each(function ($product) use ($branch, $user, $createInventoryAction) {
                $createInventoryAction->execute([
                    'branch_id' => $branch->id,
                    'product_id' => $product->id,
                    'quantity' => 1000,
                    'note' => 'Created from seeder'
                ], $user);
            });
        });
    }
}
