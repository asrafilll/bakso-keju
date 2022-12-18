<?php

namespace Tests\Feature;

use App\Enums\PermissionEnum;
use App\Models\Branch;
use App\Models\OrderSource;
use App\Models\Permission;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductInventory;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductFeatureTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * @return void
     */
    public function shouldShowProductIndexPage()
    {
        /** @var Permission */
        $permission = Permission::query()
            ->where('name', PermissionEnum::view_products())
            ->first();
        /** @var User */
        $user = User::factory()->create();
        $user->permissions()->sync($permission->id);
        $response = $this->actingAs($user)->get('/products');

        $response->assertStatus(200);
    }

    /**
     * @test
     * @return void
     */
    public function shouldContainsProductDataOnProductIndexPage()
    {
        /** @var Collection<Product> */
        $products = Product::factory(3)->create();
        /** @var Product */
        $sampleProduct = $products->first();
        /** @var Permission */
        $permission = Permission::query()
            ->where('name', PermissionEnum::view_products())
            ->first();
        /** @var User */
        $user = User::factory()->create();
        $user->permissions()->sync($permission->id);
        $response = $this->actingAs($user)->get('/products');

        $response->assertSee($sampleProduct->name);
    }

    /**
     * @test
     * @return void
     */
    public function shouldShowProductCreatePage()
    {
        /** @var Permission */
        $permission = Permission::query()
            ->where('name', PermissionEnum::create_product())
            ->first();
        /** @var User */
        $user = User::factory()->create();
        $user->permissions()->sync($permission->id);
        $response = $this->actingAs($user)->get('/products/create');

        $response->assertStatus(200);
        $response->assertViewHas('productCategories');
    }

    /**
     * @test
     * @return void
     */
    public function shouldCreateProduct()
    {
        /** @var Permission */
        $permission = Permission::query()
            ->where('name', PermissionEnum::create_product())
            ->first();
        /** @var User */
        $user = User::factory()->create();
        $user->permissions()->sync($permission->id);
        /** @var ProductCategory */
        $productCategory = ProductCategory::factory()
            ->for(ProductCategory::factory(), 'parentProductCategory')
            ->create();
        /** @var OrderSource */
        $orderSource = OrderSource::factory()->create();
        $this->actingAs($user)->post('/products', [
            'name' => 'Product 1',
            'price' => 10000,
            'product_category_id' => $productCategory->id,
            'prices' => [
                [
                    'order_source_id' => $orderSource->id,
                    'price' => 11000,
                ],
            ],
        ]);

        $this->assertDatabaseHas('products', [
            'name' => 'Product 1',
            'price' => 10000,
            'product_category_id' => $productCategory->id,
        ]);

        $this->assertDatabaseHas('product_prices', [
            'order_source_id' => $orderSource->id,
            'price' => 11000,
        ]);
    }

    /**
     * @test
     * @return void
     */
    public function shouldShowProductDetailPage()
    {
        /** @var ProductCategory */
        $productCategory = ProductCategory::factory()
            ->for(ProductCategory::factory(), 'parentProductCategory')
            ->create();
        /** @var Product */
        $product = Product::factory()
            ->for($productCategory)
            ->create();
        /** @var Permission */
        $permission = Permission::query()
            ->where('name', PermissionEnum::view_products())
            ->first();
        /** @var User */
        $user = User::factory()->create();
        $user->permissions()->sync($permission->id);
        $response = $this->actingAs($user)->get("/products/{$product->id}");

        $response->assertSee([
            $product->name,
            $product->price,
            $productCategory->id,
        ]);
        $response->assertViewHas('productCategories');
    }

    /**
     * @test
     * @return void
     */
    public function shouldUpdateProduct()
    {
        /** @var ProductCategory */
        $productCategory = ProductCategory::factory()
            ->for(ProductCategory::factory(), 'parentProductCategory')
            ->create();
        /** @var Product */
        $product = Product::factory()
            ->state([
                'product_category_id' => $productCategory->id,
            ])
            ->create();
        /** @var OrderSource */
        $orderSource = OrderSource::factory()->create();
        $product->productPrices()->create([
            'order_source_id' => $orderSource->id,
            'price' => 10000,
        ]);
        /** @var Permission */
        $permission = Permission::query()
            ->where('name', PermissionEnum::update_product())
            ->first();
        /** @var User */
        $user = User::factory()->create();
        $user->permissions()->sync($permission->id);
        $this->actingAs($user)->put("/products/{$product->id}", [
            'name' => 'Product #1',
            'price' => 10000,
            'product_category_id' => $productCategory->id,
            'prices' => [
                [
                    'order_source_id' => $orderSource->id,
                    'price' => 11000,
                ],
            ],
        ]);

        $this->assertDatabaseHas('products', [
            'name' => 'Product #1',
            'price' => 10000,
            'product_category_id' => $productCategory->id,
        ]);

        $this->assertDatabaseHas('product_prices', [
            'product_id' => $product->id,
            'order_source_id' => $orderSource->id,
            'price' => 11000,
        ]);
    }

    /**
     * @test
     * @return void
     */
    public function shouldDeleteProduct()
    {
        /** @var Product */
        $product = Product::factory()->create();
        /** @var OrderSource */
        $orderSource = OrderSource::factory()->create();
        $product->productPrices()->create([
            'order_source_id' => $orderSource->id,
            'price' => 10000,
        ]);
        /** @var Permission */
        $permission = Permission::query()
            ->where('name', PermissionEnum::delete_product())
            ->first();
        /** @var User */
        $user = User::factory()->create();
        $user->permissions()->sync($permission->id);
        $this->actingAs($user)->delete("/products/{$product->id}");

        $this->assertDatabaseMissing('products', [
            'id' => $product->id,
        ]);
        $this->assertDatabaseMissing('product_prices', [
            'product_id' => $product->id,
        ]);
    }

    /**
     * @test
     * @return void
     */
    public function shouldShowProductDetailPageWithProductInventoryData()
    {
        /** @var Branch */
        $branch = Branch::factory()->create();
        /** @var ProductCategory */
        $productCategory = ProductCategory::factory()
            ->for(ProductCategory::factory(), 'parentProductCategory')
            ->create();
        /** @var Product */
        $product = Product::factory()
            ->for($productCategory)
            ->has(
                ProductInventory::factory()
                    ->state([
                        'quantity' => 10,
                    ])
                    ->for($branch)
            )
            ->create();
        /** @var Permission */
        $permission = Permission::query()
            ->where('name', PermissionEnum::view_products())
            ->first();
        /** @var User */
        $user = User::factory()->create();
        $user->permissions()->sync($permission->id);
        $response = $this->actingAs($user)->get("/products/{$product->id}");

        $response->assertSee([
            $product->name,
            $product->price,
            $productCategory->id,
            $branch->name,
            10,
        ]);
        $response->assertViewHas('productCategories');
    }

    /**
     * @test
     * @return void
     */
    public function shouldFailedToDeleteProductWhenAlreadyHaveProductInventories()
    {
        /** @var Branch */
        $branch = Branch::factory()->create();
        /** @var ProductCategory */
        $productCategory = ProductCategory::factory()
            ->for(ProductCategory::factory(), 'parentProductCategory')
            ->create();
        /** @var Product */
        $product = Product::factory()
            ->for($productCategory)
            ->has(
                ProductInventory::factory()
                    ->state([
                        'quantity' => 10,
                    ])
                    ->for($branch)
            )
            ->create();
        /** @var Permission */
        $permission = Permission::query()
            ->where('name', PermissionEnum::delete_product())
            ->first();
        /** @var User */
        $user = User::factory()->create();
        $user->permissions()->sync($permission->id);
        $response = $this->actingAs($user)->delete("/products/{$product->id}");

        $response->assertSessionHas('failed');
    }
}
