<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Product;
use App\Models\ProductInventory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductInventoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * @return void
     */
    public function shouldShowProductInventoryIndexPage()
    {
        /** @var User */
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/product-inventories');

        $response->assertStatus(200);
    }

    /**
     * @test
     * @return void
     */
    public function shouldContainsProductInventoryOnProductInventoryIndexPage()
    {
        /** @var Branch */
        $branch = Branch::factory()->create();
        /** @var Product */
        $product = Product::factory()->create();
        /** @var ProductInventory */
        $productInventory = ProductInventory::factory()
            ->for($branch)
            ->for($product)
            ->create();
        /** @var User */
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/product-inventories');

        $response->assertSee([
            $branch->name,
            $product->name,
            $productInventory->quantity,
        ]);
    }
}
