<?php

namespace Tests\Feature;

use App\Enums\PermissionEnum;
use App\Models\Branch;
use App\Models\Permission;
use App\Models\ProductComponent;
use App\Models\ProductComponentInventory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductComponentInventoryFeatureTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * @return void
     */
    public function shouldShowProductComponentInventoryIndexPage()
    {
        /** @var Permission */
        $permission = Permission::query()
            ->where('name', PermissionEnum::view_product_component_inventories())
            ->first();
        /** @var User */
        $user = User::factory()->create();
        $user->permissions()->sync($permission->id);
        $response = $this->actingAs($user)->get('/product-component-inventories');

        $response->assertStatus(200);
    }

    /**
     * @test
     * @return void
     */
    public function shouldContainsProductComponentInventoryOnProductComponentInventoryIndexPage()
    {
        /** @var Branch */
        $branch = Branch::factory()->create();
        /** @var ProductComponent */
        $productComponent = ProductComponent::factory()->create();
        /** @var ProductComponentInventory */
        $productInventory = ProductComponentInventory::factory()
            ->for($branch)
            ->for($productComponent)
            ->create();
        /** @var Permission */
        $permission = Permission::query()
            ->where('name', PermissionEnum::view_product_component_inventories())
            ->first();
        /** @var User */
        $user = User::factory()->create();
        $user->permissions()->sync($permission->id);
        $response = $this->actingAs($user)->get('/product-component-inventories');

        $response->assertSee([
            $branch->name,
            $productComponent->name,
            $productInventory->quantity,
        ]);
    }
}
