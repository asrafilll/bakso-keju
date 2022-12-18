<?php

namespace Tests\Feature;

use App\Enums\PermissionEnum;
use App\Models\Branch;
use App\Models\Inventory;
use App\Models\Permission;
use App\Models\Product;
use App\Models\ProductInventory;
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
        /** @var Permission */
        $permission = Permission::query()
            ->where('name', PermissionEnum::view_inventories())
            ->first();
        /** @var User */
        $user = User::factory()->create();
        $user->permissions()->sync($permission->id);
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
        /** @var Permission */
        $permission = Permission::query()
            ->where('name', PermissionEnum::view_inventories())
            ->first();
        /** @var User */
        $user = User::factory()->create();
        $user->permissions()->sync($permission->id);
        /** @var Inventory */
        $inventory = Inventory::factory()
            ->state([
                'created_by' => $user->id,
            ])
            ->for($product)
            ->for($branch)
            ->create();

        $response = $this->actingAs($user)->get('/inventories');

        $response->assertSee([
            $product->name,
            $branch->name,
            $inventory->quantity,
            $user->name,
        ]);
    }

    /**
     * @test
     * @return void
     */
    public function shouldShowCreateInventoryPage()
    {
        /** @var Permission */
        $permission = Permission::query()
            ->where('name', PermissionEnum::create_inventory())
            ->first();
        /** @var User */
        $user = User::factory()->create();
        $user->permissions()->sync($permission->id);
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
        /** @var Permission */
        $permission = Permission::query()
            ->where('name', PermissionEnum::create_inventory())
            ->first();
        /** @var User */
        $user = User::factory()->create();
        $user->permissions()->sync($permission->id);
        $this->actingAs($user)->post('/inventories', [
            'product_id' => $product->id,
            'branch_id' => $branch->id,
            'quantity' => 10,
            'note' => 'example note',
        ]);

        $this->assertDatabaseHas('inventories', [
            'product_id' => $product->id,
            'branch_id' => $branch->id,
            'quantity' => 10,
            'note' => 'example note',
            'created_by' => $user->id,
        ]);
    }

    /**
     * @test
     * @return void
     */
    public function shouldCreateInventoryAndCreateProductInventory()
    {
        /** @var Product */
        $product = Product::factory()->create();
        /** @var Branch */
        $branch = Branch::factory()->create();
        /** @var Permission */
        $permission = Permission::query()
            ->where('name', PermissionEnum::create_inventory())
            ->first();
        /** @var User */
        $user = User::factory()->create();
        $user->permissions()->sync($permission->id);
        $this->actingAs($user)->post('/inventories', [
            'product_id' => $product->id,
            'branch_id' => $branch->id,
            'quantity' => 10,
            'note' => 'example note',
        ]);

        $this->assertDatabaseHas('product_inventories', [
            'product_id' => $product->id,
            'branch_id' => $branch->id,
            'quantity' => 10,
        ]);
    }

    /**
     * @test
     * @return void
     */
    public function shouldCreateInventoryAndUpdateProductInventory()
    {
        /** @var Product */
        $product = Product::factory()->create();
        /** @var Branch */
        $branch = Branch::factory()->create();
        /** @var ProductInventory */
        $productInventory = ProductInventory::factory()
            ->state([
                'quantity' => 3,
            ])
            ->for($product)
            ->for($branch)
            ->create();
        /** @var Permission */
        $permission = Permission::query()
            ->where('name', PermissionEnum::create_inventory())
            ->first();
        /** @var User */
        $user = User::factory()->create();
        $user->permissions()->sync($permission->id);
        $this->actingAs($user)->post('/inventories', [
            'product_id' => $product->id,
            'branch_id' => $branch->id,
            'quantity' => 10,
            'note' => 'example note',
        ]);

        $this->assertDatabaseHas('product_inventories', [
            'id' => $productInventory->id,
            'product_id' => $product->id,
            'branch_id' => $branch->id,
            'quantity' => 13,
        ]);
    }

    /**
     * @test
     * @return void
     */
    public function shouldCreateInventoryAndUpdateProductInventoryWithNegativeQuantity()
    {
        /** @var Product */
        $product = Product::factory()->create();
        /** @var Branch */
        $branch = Branch::factory()->create();
        /** @var ProductInventory */
        $productInventory = ProductInventory::factory()
            ->state([
                'quantity' => 3,
            ])
            ->for($product)
            ->for($branch)
            ->create();
        /** @var Permission */
        $permission = Permission::query()
            ->where('name', PermissionEnum::create_inventory())
            ->first();
        /** @var Permission */
        $negativeQuantityPermission = Permission::query()
            ->where('name', PermissionEnum::create_negative_quantity_inventory())
            ->first();
        /** @var User */
        $user = User::factory()->create();
        $user->permissions()->sync([$permission->id, $negativeQuantityPermission->id]);
        $this->actingAs($user)->post('/inventories', [
            'product_id' => $product->id,
            'branch_id' => $branch->id,
            'quantity' => -2,
            'note' => 'example note',
        ]);

        $this->assertDatabaseHas('product_inventories', [
            'id' => $productInventory->id,
            'product_id' => $product->id,
            'branch_id' => $branch->id,
            'quantity' => 1,
        ]);
    }
}
