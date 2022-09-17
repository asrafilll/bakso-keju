<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventoryFeatureTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * @return void
     */
    public function shouldShowInventoryIndexPage()
    {
        /** @var User */
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/inventories');

        $response->assertStatus(200);
    }

    /**
     * @test
     * @return void
     */
    public function shouldContainsInventoryOnInventoryIndexPage()
    {
        /** @var Product */
        $product = Product::factory()->create();
        /** @var Branch */
        $branch = Branch::factory()->create();
        /** @var Inventory */
        $inventory = Inventory::factory()
            ->for($product)
            ->for($branch)
            ->create();;
        /** @var User */
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/inventories');

        $response->assertSee([
            $product->name,
            $branch->name,
            $inventory->quantity,
        ]);
    }

    /**
     * @test
     * @return void
     */
    public function shouldShowCreateInventoryPage()
    {
        /** @var User */
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/inventories/create');

        $response->assertStatus(200);
    }

    /**
     * @test
     * @return void
     */
    public function shouldCreateInventory()
    {
        /** @var Product */
        $product = Product::factory()->create();
        /** @var Branch */
        $branch = Branch::factory()->create();
        /** @var User */
        $user = User::factory()->create();

        $this->actingAs($user)->post('/inventories', [
            'product_id' => $product->id,
            'branch_id' => $branch->id,
            'quantity' => 10,
        ]);

        $this->assertDatabaseHas('inventories', [
            'product_id' => $product->id,
            'branch_id' => $branch->id,
            'quantity' => 10,
        ]);
    }

    /**
     * @test
     * @return void
     */
    public function shouldIncreaseInventoryQuantityWhenCreateSameInventory()
    {
        /** @var Product */
        $product = Product::factory()->create();
        /** @var Branch */
        $branch = Branch::factory()->create();
        /** @var Inventory */
        $inventory = Inventory::factory()
            ->state(['quantity' => 1])
            ->for($product)
            ->for($branch)
            ->create();
        /** @var User */
        $user = User::factory()->create();

        $this->actingAs($user)->post('/inventories', [
            'product_id' => $product->id,
            'branch_id' => $branch->id,
            'quantity' => 10,
        ]);

        $this->assertDatabaseHas('inventories', [
            'id' => $inventory->id,
            'product_id' => $product->id,
            'branch_id' => $branch->id,
            'quantity' => 11,
        ]);
    }
}
