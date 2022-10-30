<?php

namespace Tests\Feature;

use App\Actions\CreateManufactureProductAction;
use App\Enums\PermissionEnum;
use App\Models\Branch;
use App\Models\ManufactureProduct;
use App\Models\Permission;
use App\Models\Product;
use App\Models\ProductComponent;
use App\Models\ProductComponentInventory;
use App\Models\ProductInventory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ManufactureProductFeatureTest extends TestCase
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
    public function shouldShowManufactureProductIndexPage()
    {
        /** @var Permission */
        $permission = Permission::query()
            ->where('name', PermissionEnum::view_manufacture_products())
            ->first();
        /** @var User */
        $user = User::factory()->create();
        $user->permissions()->sync($permission->id);
        $response = $this->actingAs($user)->get('/manufacture-products');

        $response->assertStatus(200);
    }

    /**
     * @test
     * @return void
     */
    public function shouldContainsManufactureProductOnManufactureProductIndexPage()
    {
        /** @var Permission */
        $permission = Permission::query()
            ->where('name', PermissionEnum::view_manufacture_products())
            ->first();
        /** @var User */
        $user = User::factory()->create();
        $user->permissions()->sync($permission->id);

        /** @var Branch */
        $branch = Branch::factory()->create();
        /** @var ManufactureProduct */
        $manufactureProduct = ManufactureProduct::factory()
            ->for($branch)
            ->create([
                'created_by' => $user->id,
            ]);
        $response = $this->actingAs($user)->get('/manufacture-products');

        $response->assertSee([
            $manufactureProduct->order_number,
            $manufactureProduct->created_at,
            $branch->name,
        ]);
    }

    /**
     * @test
     * @return void
     */
    public function shouldShowCreateManufactureProductPage()
    {
        /** @var Permission */
        $permission = Permission::query()
            ->where('name', PermissionEnum::create_manufacture_product())
            ->first();
        /** @var User */
        $user = User::factory()->create();
        $user->permissions()->sync($permission->id);
        $response = $this->actingAs($user)->get('/manufacture-products/create');

        $response->assertStatus(200);
    }

    /**
     * @test
     * @return void
     */
    public function shouldCreateManufactureProduct()
    {
        /** @var Branch */
        $branch = Branch::factory()->create();
        /** @var ProductComponent */
        $productComponent = ProductComponent::factory()->create();
        /** @var ProductComponentInventory */
        $productComponentInventory = ProductComponentInventory::factory()
            ->for($branch)
            ->for($productComponent)
            ->create([
                'quantity' => 5
            ]);
        /** @var Product */
        $product = Product::factory()->create();
        /** @var Permission */
        $permission = Permission::query()
            ->where('name', PermissionEnum::create_manufacture_product())
            ->first();
        /** @var User */
        $user = User::factory()->create();
        $user->permissions()->sync($permission->id);
        $response = $this->actingAs($user)->post('/manufacture-products', [
            'created_at' => '2022-01-01 00:00:00',
            'branch_id' => $branch->id,
            'line_product_components' => [
                [
                    'product_component_id' => $productComponent->id,
                    'quantity' => 5,
                ],
            ],
            'line_products' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 2
                ],
            ],
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('manufacture_products', [
            'created_at' => '2022-01-01 00:00:00',
            'created_by' => $user->id,
            'branch_id' => $branch->id,
            'total_line_product_components_quantity' => 5,
            'total_line_products_quantity' => 2,
        ]);

        $this->assertDatabaseHas('manufacture_product_line_product_components', [
            'product_component_id' => $productComponent->id,
            'product_component_name' => $productComponent->name,
            'quantity' => 5,
        ]);

        $this->assertDatabaseHas('product_component_inventories', [
            'id' => $productComponentInventory->id,
            'branch_id' => $branch->id,
            'product_component_id' => $productComponent->id,
            'quantity' => 0,
        ]);

        $this->assertDatabaseHas('product_inventories', [
            'branch_id' => $branch->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);
    }

    /**
     * @test
     * @return void
     */
    public function shouldCreateManufactureProductWithExistingProductInventory()
    {
        /** @var Branch */
        $branch = Branch::factory()->create();
        /** @var ProductComponent */
        $productComponent = ProductComponent::factory()->create();
        /** @var ProductComponentInventory */
        $productComponentInventory = ProductComponentInventory::factory()
            ->for($branch)
            ->for($productComponent)
            ->create([
                'quantity' => 5
            ]);
        /** @var Product */
        $product = Product::factory()->create();
        /** @var ProductInventory */
        $productInventory = ProductInventory::factory()
            ->for($branch)
            ->for($product)
            ->create([
                'quantity' => 5,
            ]);
        /** @var Permission */
        $permission = Permission::query()
            ->where('name', PermissionEnum::create_manufacture_product())
            ->first();
        /** @var User */
        $user = User::factory()->create();
        $user->permissions()->sync($permission->id);
        $response = $this->actingAs($user)->post('/manufacture-products', [
            'created_at' => '2022-01-01 00:00:00',
            'branch_id' => $branch->id,
            'line_product_components' => [
                [
                    'product_component_id' => $productComponent->id,
                    'quantity' => 5,
                ],
            ],
            'line_products' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 2
                ],
            ],
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('manufacture_products', [
            'created_at' => '2022-01-01 00:00:00',
            'created_by' => $user->id,
            'branch_id' => $branch->id,
            'total_line_product_components_quantity' => 5,
            'total_line_products_quantity' => 2,
        ]);

        $this->assertDatabaseHas('manufacture_product_line_product_components', [
            'product_component_id' => $productComponent->id,
            'product_component_name' => $productComponent->name,
            'quantity' => 5,
        ]);

        $this->assertDatabaseHas('product_component_inventories', [
            'id' => $productComponentInventory->id,
            'branch_id' => $branch->id,
            'product_component_id' => $productComponent->id,
            'quantity' => 0,
        ]);

        $this->assertDatabaseHas('product_inventories', [
            'id' => $productInventory->id,
            'branch_id' => $branch->id,
            'product_id' => $product->id,
            'quantity' => 7,
        ]);
    }

    /**
     * @test
     * @return void
     */
    public function shouldFailedToCreateManufactureProductWhenProductComponentQuantityLessThanNeeded()
    {
        /** @var Branch */
        $branch = Branch::factory()->create();
        /** @var ProductComponent */
        $productComponent = ProductComponent::factory()->create();
        ProductComponentInventory::factory()
            ->for($branch)
            ->for($productComponent)
            ->create([
                'quantity' => 2,
            ]);
        /** @var Product */
        $product = Product::factory()->create();
        /** @var Permission */
        $permission = Permission::query()
            ->where('name', PermissionEnum::create_manufacture_product())
            ->first();
        /** @var User */
        $user = User::factory()->create();
        $user->permissions()->sync($permission->id);
        $response = $this->actingAs($user)->post('/manufacture-products', [
            'created_at' => '2022-01-01 00:00:00',
            'branch_id' => $branch->id,
            'line_product_components' => [
                [
                    'product_component_id' => $productComponent->id,
                    'quantity' => 5,
                ],
            ],
            'line_products' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 2
                ],
            ],
        ]);

        $response->assertSessionHas('failed');
    }

    /**
     * @test
     * @return void
     */
    public function shouldShowManufactureProductDetailPage()
    {
        /** @var Permission */
        $permission = Permission::query()
            ->where('name', PermissionEnum::view_manufacture_products())
            ->first();
        /** @var User */
        $user = User::factory()->create();
        $user->permissions()->sync($permission->id);
        /** @var Branch */
        $branch = Branch::factory()->create();
        /** @var ManufactureProduct */
        $manufactureProduct = ManufactureProduct::factory()
            ->for($branch)
            ->for($user, 'creator')
            ->create();
        $response = $this->actingAs($user)->get("/manufacture-products/{$manufactureProduct->id}");

        $response->assertStatus(200);
    }

    /**
     * @test
     * @return void
     */
    public function shouldContainsManufactureProductDataWhenShowManufactureProductDetailPage()
    {
        /** @var Branch */
        $branch = Branch::factory()->create();
        /** @var ManufactureProduct */
        $manufactureProduct = ManufactureProduct::factory()
            ->for($branch)
            ->create();
        /** @var Permission */
        $permission = Permission::query()
            ->where('name', PermissionEnum::view_manufacture_products())
            ->first();
        /** @var User */
        $user = User::factory()->create();
        $user->permissions()->sync($permission->id);
        $response = $this->actingAs($user)->get("/manufacture-products/{$manufactureProduct->id}");

        $response->assertSee([
            $manufactureProduct->order_number,
            $branch->name,
        ]);
    }

    /**
     * @test
     * @return void
     */
    public function shouldDeleteManufactureProduct()
    {
        /** @var Permission */
        $permission = Permission::query()
            ->where('name', PermissionEnum::delete_manufacture_product())
            ->first();
        /** @var User */
        $user = User::factory()->create();
        $user->permissions()->sync($permission->id);
        /** @var Branch */
        $branch = Branch::factory()->create();
        /** @var ProductComponent */
        $productComponent = ProductComponent::factory()->create();
        /** @var ProductComponentInventory */
        $productComponentInventory = ProductComponentInventory::factory()
            ->for($branch)
            ->for($productComponent)
            ->create([
                'quantity' => 5
            ]);
        /** @var Product */
        $product = Product::factory()->create();
        /** @var ProductInventory */
        $productInventory = ProductInventory::factory()
            ->for($branch)
            ->for($product)
            ->create([
                'quantity' => 5,
            ]);
        $data = [
            'created_at' => '2022-01-01 00:00:00',
            'created_by' => $user->id,
            'branch_id' => $branch->id,
            'line_product_components' => [
                [
                    'product_component_id' => $productComponent->id,
                    'quantity' => 5,
                ],
            ],
            'line_products' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 2
                ],
            ],
        ];
        $manufactureProduct = resolve(CreateManufactureProductAction::class)->execute($data);

        $response = $this->actingAs($user)->delete("/manufacture-products/{$manufactureProduct->id}");
        $response->assertSessionHas(['success']);

        $this->assertDatabaseMissing('manufacture_products', [
            'id' => $manufactureProduct->id,
        ]);

        $this->assertDatabaseMissing('manufacture_product_line_product_components', [
            'manufacture_product_id' => $manufactureProduct->id,
            'product_component_id' => $productComponent->id,
        ]);

        $this->assertDatabaseMissing('manufacture_product_line_products', [
            'manufacture_product_id' => $manufactureProduct->id,
            'product_id' => $product->id,
        ]);

        $this->assertDatabaseHas('product_component_inventories', [
            'id' => $productComponentInventory->id,
            'quantity' => 5,
        ]);

        $this->assertDatabaseHas('product_inventories', [
            'id' => $productInventory->id,
            'quantity' => 5,
        ]);
    }
}
