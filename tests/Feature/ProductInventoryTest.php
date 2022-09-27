<?php

namespace Tests\Feature;

use App\Enums\PermissionEnum;
use App\Models\Branch;
use App\Models\Permission;
use App\Models\Product;
use App\Models\ProductInventory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductInventoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Indicates whether the default seeder should run before each test.
     *
     * @var bool
     */
    protected $seed = true;

    /**
     * @test
     * @return void
     */
    public function shouldShowProductInventoryIndexPage()
    {
        /** @var Permission */
        $permission = Permission::query()
            ->where('name', PermissionEnum::view_product_inventories())
            ->first();
        /** @var User */
        $user = User::factory()->create();
        $user->permissions()->sync($permission->id);
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
        /** @var Permission */
        $permission = Permission::query()
            ->where('name', PermissionEnum::view_product_inventories())
            ->first();
        /** @var User */
        $user = User::factory()->create();
        $user->permissions()->sync($permission->id);
        $response = $this->actingAs($user)->get('/product-inventories');

        $response->assertSee([
            $branch->name,
            $product->name,
            $productInventory->quantity,
        ]);
    }
}
