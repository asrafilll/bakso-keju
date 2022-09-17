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

    /**
     * @test
     * @return void
     */
    public function shouldShowInventoryDetailPage()
    {
        /** @var Inventory */
        $inventory = Inventory::factory()
            ->for(Product::factory())
            ->for(Branch::factory())
            ->create();;
        /** @var User */
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get("/inventories/{$inventory->id}");

        $response->assertStatus(200);
    }

    /**
     * @test
     * @return void
     */
    public function shouldContainsInventoryDataOnInventoryDetailPage()
    {
        /** @var Inventory */
        $inventory = Inventory::factory()
            ->for(Product::factory())
            ->for(Branch::factory())
            ->create();
        /** @var User */
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get("/inventories/{$inventory->id}");

        $response->assertSee([
            $inventory->product_id,
            $inventory->branch_id,
            $inventory->quantity,
        ]);
    }

    /**
     * @test
     * @return void
     */
    public function shouldDeleteInventory()
    {
        /** @var Inventory */
        $inventory = Inventory::factory()
            ->for(Product::factory())
            ->for(Branch::factory())
            ->create();
        /** @var User */
        $user = User::factory()->create();

        $this->actingAs($user)->delete("/inventories/{$inventory->id}");

        $this->assertDatabaseMissing('inventories', [
            'id' => $inventory->id,
        ]);
    }
}
