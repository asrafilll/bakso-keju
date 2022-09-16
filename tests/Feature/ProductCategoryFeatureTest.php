<?php

namespace Tests\Feature;

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
        /** @var User */
        $user = User::factory()->create();
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
        /** @var User */
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/product-categories');

        $response->assertSee($productCategory->name);
    }

    /**
     * @test
     * @return void
     */
    public function shouldShowCreateProductCategoryPage()
    {
        /** @var User */
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/product-categories/create');

        $response->assertStatus(200);
    }

    /**
     * @test
     * @return void
     */
    public function shouldCreateProductCategory()
    {
        /** @var User */
        $user = User::factory()->create();

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
        /** @var User */
        $user = User::factory()->create();

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
        /** @var User */
        $user = User::factory()->create();

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
        /** @var User */
        $user = User::factory()->create();

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
        /** @var User */
        $user = User::factory()->create();

        $this->actingAs($user)->delete("/product-categories/{$productCategory->id}");

        $this->assertDatabaseMissing('product_categories', [
            'id' => $productCategory->id,
        ]);
    }
}
