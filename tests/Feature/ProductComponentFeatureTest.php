<?php

namespace Tests\Feature;

use App\Enums\PermissionEnum;
use App\Models\Permission;
use App\Models\ProductComponent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductComponentFeatureTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * @return void
     */
    public function shouldShowProductComponentIndexPage()
    {
        /** @var Permission */
        $permission = Permission::query()
            ->where('name', PermissionEnum::view_product_components())
            ->first();
        /** @var User */
        $user = User::factory()->create();
        $user->permissions()->sync($permission->id);
        $response = $this->actingAs($user)->get('/product-components');

        $response->assertStatus(200);
    }

    /**
     * @test
     * @return void
     */
    public function shouldContainsProductComponentDataOnProductComponentIndexPage()
    {
        /** @var ProductComponent */
        $productComponent = ProductComponent::factory()->create();

        /** @var Permission */
        $permission = Permission::query()
            ->where('name', PermissionEnum::view_product_components())
            ->first();
        /** @var User */
        $user = User::factory()->create();
        $user->permissions()->sync($permission->id);
        $response = $this->actingAs($user)->get('/product-components');

        $response->assertSee($productComponent->name);
    }

    /**
     * @test
     * @return void
     */
    public function shouldShowProductComponentCreatePage()
    {
        /** @var Permission */
        $permission = Permission::query()
            ->where('name', PermissionEnum::create_product_component())
            ->first();
        /** @var User */
        $user = User::factory()->create();
        $user->permissions()->sync($permission->id);
        $response = $this->actingAs($user)->get('/product-components/create');

        $response->assertStatus(200);
    }

    /**
     * @test
     * @return void
     */
    public function shouldCreateProductComponent()
    {
        /** @var Permission */
        $permission = Permission::query()
            ->where('name', PermissionEnum::create_product_component())
            ->first();
        /** @var User */
        $user = User::factory()->create();
        $user->permissions()->sync($permission->id);
        $this->actingAs($user)->post('/product-components', [
            'name' => 'Product Component 1',
        ]);

        $this->assertDatabaseHas('product_components', [
            'name' => 'Product Component 1',
        ]);
    }

    /**
     * @test
     * @return void
     */
    public function shouldShowProductComponentDetailPage()
    {
        /** @var Permission */
        $permission = Permission::query()
            ->where('name', PermissionEnum::view_product_components())
            ->first();
        /** @var User */
        $user = User::factory()->create();
        $user->permissions()->sync($permission->id);

        /** @var ProductComponent */
        $productComponent = ProductComponent::factory()->create();
        $response = $this->actingAs($user)->get("/product-components/{$productComponent->id}");

        $response->assertStatus(200);
    }

    /**
     * @test
     * @return void
     */
    public function shouldUpdateProductComponent()
    {
        /** @var Permission */
        $permission = Permission::query()
            ->where('name', PermissionEnum::update_product_component())
            ->first();
        /** @var User */
        $user = User::factory()->create();
        $user->permissions()->sync($permission->id);

        /** @var ProductComponent */
        $productComponent = ProductComponent::factory()->create();
        $this->actingAs($user)->put("/product-components/{$productComponent->id}",  [
            'name' => 'Sample Product Component'
        ]);

        $this->assertDatabaseHas('product_components', [
            'id' => $productComponent->id,
            'name' => 'Sample Product Component',
        ]);
    }

    /**
     * @test
     * @return void
     */
    public function shouldDeleteProductComponent()
    {
        /** @var Permission */
        $permission = Permission::query()
            ->where('name', PermissionEnum::delete_product_component())
            ->first();
        /** @var User */
        $user = User::factory()->create();
        $user->permissions()->sync($permission->id);

        /** @var ProductComponent */
        $productComponent = ProductComponent::factory()->create();
        $this->actingAs($user)->delete("/product-components/{$productComponent->id}",);

        $this->assertDatabaseMissing('product_components', [
            'id' => $productComponent->id,
        ]);
    }
}
