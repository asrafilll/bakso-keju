<?php

namespace Tests\Feature;

use App\Models\Branch;
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
        /** @var User */
        $user = User::factory()->create();

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
        /** @var User */
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/products');

        $response->assertSee($sampleProduct->name);
    }

    /**
     * @test
     * @return void
     */
    public function shouldShowProductCreatePage()
    {
        /** @var User */
        $user = User::factory()->create();

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
        /** @var User */
        $user = User::factory()->create();
        /** @var ProductCategory */
        $productCategory = ProductCategory::factory()
            ->for(Product::factory(), 'parentProductCategory')
            ->create();
        $this->actingAs($user)->post('/products', [
            'name' => 'Product 1',
            'price' => 10000,
            'product_category_id' => $productCategory->id,
        ]);

        $this->assertDatabaseHas('products', [
            'name' => 'Product 1',
            'price' => 10000,
            'product_category_id' => $productCategory->id,
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
            ->for(Product::factory(), 'parentProductCategory')
            ->create();
        /** @var Product */
        $product = Product::factory()
            ->for($productCategory)
            ->create();
        /** @var User */
        $user = User::factory()->create();

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
            ->for(Product::factory(), 'parentProductCategory')
            ->create();
        /** @var Product */
        $product = Product::factory()
            ->state([
                'product_category_id' => $productCategory->id,
            ])
            ->create();
        /** @var User */
        $user = User::factory()->create();

        $this->actingAs($user)->put("/products/{$product->id}", [
            'name' => 'Product #1',
            'price' => 10000,
            'product_category_id' => $productCategory->id,
        ]);

        $this->assertDatabaseHas('products', [
            'name' => 'Product #1',
            'price' => 10000,
            'product_category_id' => $productCategory->id,
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
        /** @var User */
        $user = User::factory()->create();

        $this->actingAs($user)->delete("/products/{$product->id}");

        $this->assertDatabaseMissing('products', [
            'id' => $product->id,
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
            ->for(Product::factory(), 'parentProductCategory')
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
        /** @var User */
        $user = User::factory()->create();

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
            ->for(Product::factory(), 'parentProductCategory')
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
        /** @var User */
        $user = User::factory()->create();

        $response = $this->actingAs($user)->delete("/products/{$product->id}");

        $response->assertSessionHas('failed');
    }
}
