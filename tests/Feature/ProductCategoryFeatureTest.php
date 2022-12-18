<?php

namespace Tests\Feature;

use App\Enums\PermissionEnum;
use App\Models\Permission;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductCategoryFeatureTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * @return void
     */
    public function shouldShowProductCategoryIndexPage()
    {
        /** @var Permission */
        $permission = Permission::query()
            ->where('name', PermissionEnum::view_product_categories())
            ->first();
        /** @var User */
        $user = User::factory()->create();
        $user->permissions()->sync($permission->id);
        $response = $this->actingAs($user)->get('/product-categories');

        $response->assertStatus(200);
    }

    /**
     * @test
     * @return void
     */
    public function shouldContainsProductCategoryOnProductCategoryIndexPage()
    {
        /** @var ProductCategory */
        $productCategory = ProductCategory::factory()->create();;
        /** @var Permission */
        $permission = Permission::query()
            ->where('name', PermissionEnum::view_product_categories())
            ->first();
        /** @var User */
        $user = User::factory()->create();
        $user->permissions()->sync($permission->id);
        $response = $this->actingAs($user)->get('/product-categories');

        $response->assertSee($productCategory->name);
    }

    /**
     * @test
     * @return void
     */
    public function shouldShowCreateProductCategoryPage()
    {
        /** @var Permission */
        $permission = Permission::query()
            ->where('name', PermissionEnum::create_product_category())
            ->first();
        /** @var User */
        $user = User::factory()->create();
        $user->permissions()->sync($permission->id);
        $response = $this->actingAs($user)->get('/product-categories/create');

        $response->assertStatus(200);
    }

    /**
     * @test
     * @return void
     */
    public function shouldCreateProductCategory()
    {
        /** @var Permission */
        $permission = Permission::query()
            ->where('name', PermissionEnum::create_product_category())
            ->first();
        /** @var User */
        $user = User::factory()->create();
        $user->permissions()->sync($permission->id);
        $this->actingAs($user)->post('/product-categories', [
            'name' => 'ProductCategory #1',
        ]);

        $this->assertDatabaseHas('product_categories', [
            'name' => 'ProductCategory #1',
        ]);
    }

    /**
     * @test
     * @return void
     */
    public function shouldShowProductCategoryDetailPage()
    {
        /** @var ProductCategory */
        $productCategory = ProductCategory::factory()->create();;
        /** @var Permission */
        $permission = Permission::query()
            ->where('name', PermissionEnum::view_product_categories())
            ->first();
        /** @var User */
        $user = User::factory()->create();
        $user->permissions()->sync($permission->id);
        $response = $this->actingAs($user)->get("/product-categories/{$productCategory->id}");

        $response->assertStatus(200);
    }

    /**
     * @test
     * @return void
     */
    public function shouldContainsProductCategoryDataOnProductCategoryDetailPage()
    {
        /** @var ProductCategory */
        $productCategory = ProductCategory::factory()->create();
        /** @var Permission */
        $permission = Permission::query()
            ->where('name', PermissionEnum::view_product_categories())
            ->first();
        /** @var User */
        $user = User::factory()->create();
        $user->permissions()->sync($permission->id);
        $response = $this->actingAs($user)->get("/product-categories/{$productCategory->id}");

        $response->assertSee($productCategory->name);
    }

    /**
     * @test
     * @return void
     */
    public function shouldUpdateProductCategory()
    {
        /** @var ProductCategory */
        $productCategory = ProductCategory::factory()->create();
        /** @var Permission */
        $permission = Permission::query()
            ->where('name', PermissionEnum::update_product_category())
            ->first();
        /** @var User */
        $user = User::factory()->create();
        $user->permissions()->sync($permission->id);
        $this->actingAs($user)->put("/product-categories/{$productCategory->id}", [
            'name' => 'ProductCategory #2',
        ]);

        $this->assertDatabaseHas('product_categories', [
            'id' => $productCategory->id,
            'name' => 'ProductCategory #2',
        ]);
    }

    /**
     * @test
     * @return void
     */
    public function shouldDeleteProductCategory()
    {
        /** @var ProductCategory */
        $productCategory = ProductCategory::factory()->create();
        /** @var Permission */
        $permission = Permission::query()
            ->where('name', PermissionEnum::delete_product_category())
            ->first();
        /** @var User */
        $user = User::factory()->create();
        $user->permissions()->sync($permission->id);
        $this->actingAs($user)->delete("/product-categories/{$productCategory->id}");

        $this->assertDatabaseMissing('product_categories', [
            'id' => $productCategory->id,
        ]);
    }

    /**
     * @test
     * @return void
     */
    public function shouldDeleteProductCategoryWhichUsedByProducts()
    {
        /** @var ProductCategory */
        $productCategory = ProductCategory::factory()
            ->has(Product::factory())
            ->create();
        /** @var Permission */
        $permission = Permission::query()
            ->where('name', PermissionEnum::delete_product_category())
            ->first();
        /** @var User */
        $user = User::factory()->create();
        $user->permissions()->sync($permission->id);
        $this->actingAs($user)->delete("/product-categories/{$productCategory->id}");

        $this->assertDatabaseMissing('product_categories', [
            'id' => $productCategory->id,
        ]);
    }

    /**
     * @test
     * @return void
     */
    public function shouldCreateSubProductCategory()
    {
        /** @var ProductCategory */
        $productCategory = ProductCategory::factory()->create();
        /** @var Permission */
        $permission = Permission::query()
            ->where('name', PermissionEnum::create_product_category())
            ->first();
        /** @var User */
        $user = User::factory()->create();
        $user->permissions()->sync($permission->id);
        $this->actingAs($user)->post('/product-categories', [
            'name' => 'Sub ProductCategory #1',
            'parent_id' => $productCategory->id,
        ]);

        $this->assertDatabaseHas('product_categories', [
            'name' => 'Sub ProductCategory #1',
            'parent_id' => $productCategory->id,
        ]);
    }

    /**
     * @test
     * @return void
     */
    public function shouldUpdateSubProductCategory()
    {
        /** @var ProductCategory */
        $productCategory = ProductCategory::factory()->create();
        /** @var ProductCategory */
        $subProductCategory = ProductCategory::factory()
            ->for($productCategory, 'parentProductCategory')
            ->create();
        /** @var Permission */
        $permission = Permission::query()
            ->where('name', PermissionEnum::update_product_category())
            ->first();
        /** @var User */
        $user = User::factory()->create();
        $user->permissions()->sync($permission->id);
        $this->actingAs($user)->put('/product-categories/' . $subProductCategory->id, [
            'name' => 'Sub ProductCategory #001',
            'parent_id' => $productCategory->id,
        ]);

        $this->assertDatabaseHas('product_categories', [
            'name' => 'Sub ProductCategory #001',
            'parent_id' => $productCategory->id,
        ]);
    }
}
