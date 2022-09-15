<?php

namespace Tests\Feature;

use App\Models\Product;
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
    }

    /**
     * @test
     * @return void
     */
    public function shouldCreateProduct()
    {
        /** @var User */
        $user = User::factory()->create();

        $this->actingAs($user)->post('/products', [
            'name' => 'Product 1',
            'price' => 10000,
        ]);

        $this->assertDatabaseHas('products', [
            'name' => 'Product 1',
            'price' => 10000,
        ]);
    }

    /**
     * @test
     * @return void
     */
    public function shouldShowProductDetailPage()
    {
        /** @var Product */
        $product = Product::factory()->create();
        /** @var User */
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get("/products/{$product->id}");

        $response->assertSee([
            $product->name,
            $product->price,
        ]);
    }
}
