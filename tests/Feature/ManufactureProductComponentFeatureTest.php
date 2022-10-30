<?php

namespace Tests\Feature;

use App\Actions\CreateManufactureProductComponentAction;
use App\Enums\PermissionEnum;
use App\Models\Branch;
use App\Models\ManufactureProductComponent;
use App\Models\Permission;
use App\Models\ProductComponent;
use App\Models\ProductComponentInventory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ManufactureProductComponentFeatureTest extends TestCase
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
    public function shouldShowManufactureProductComponentIndexPage()
    {
        /** @var Permission */
        $permission = Permission::query()
            ->where('name', PermissionEnum::view_manufacture_product_components())
            ->first();
        /** @var User */
        $user = User::factory()->create();
        $user->permissions()->sync($permission->id);
        $response = $this->actingAs($user)->get('/manufacture-product-components');

        $response->assertStatus(200);
    }

    /**
     * @test
     * @return void
     */
    public function shouldContainsManufactureProductComponentOnManufactureProductComponentIndexPage()
    {
        /** @var Permission */
        $permission = Permission::query()
            ->where('name', PermissionEnum::view_manufacture_product_components())
            ->first();
        /** @var User */
        $user = User::factory()->create();
        $user->permissions()->sync($permission->id);

        /** @var Branch */
        $branch = Branch::factory()->create();
        /** @var ManufactureProductComponent */
        $manufactureProductComponent = ManufactureProductComponent::factory()
            ->for($branch)
            ->create([
                'created_by' => $user->id,
            ]);
        $response = $this->actingAs($user)->get('/manufacture-product-components');

        $response->assertSee([
            $manufactureProductComponent->order_number,
            $manufactureProductComponent->created_at,
            $branch->name,
        ]);
    }

    /**
     * @test
     * @return void
     */
    public function shouldShowCreateManufactureProductComponentPage()
    {
        /** @var Permission */
        $permission = Permission::query()
            ->where('name', PermissionEnum::create_manufacture_product_component())
            ->first();
        /** @var User */
        $user = User::factory()->create();
        $user->permissions()->sync($permission->id);
        $response = $this->actingAs($user)->get('/manufacture-product-components/create');

        $response->assertStatus(200);
    }

    /**
     * @test
     * @return void
     */
    public function shouldCreateManufactureProductComponent()
    {
        /** @var Branch */
        $branch = Branch::factory()->create();
        /** @var ProductComponent */
        $productComponent = ProductComponent::factory()->create();
        /** @var Permission */
        $permission = Permission::query()
            ->where('name', PermissionEnum::create_manufacture_product_component())
            ->first();
        /** @var User */
        $user = User::factory()->create();
        $user->permissions()->sync($permission->id);
        $response = $this->actingAs($user)->post('/manufacture-product-components', [
            'created_at' => '2022-01-01 00:00:00',
            'branch_id' => $branch->id,
            'line_items' => [
                [
                    'product_component_id' => $productComponent->id,
                    'price' => 10000,
                    'quantity' => 2,
                    'total_weight' => 3000,
                ],
            ],
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('manufacture_product_components', [
            'created_at' => '2022-01-01 00:00:00',
            'created_by' => $user->id,
            'branch_id' => $branch->id,
            'total_line_items_quantity' => 2,
            'total_line_items_weight' => 3000,
            'total_line_items_price' => 10000 * 2,
        ]);

        $this->assertDatabaseHas('manufacture_product_component_line_items', [
            'product_component_id' => $productComponent->id,
            'product_component_name' => $productComponent->name,
            'price' => 10000,
            'quantity' => 2,
            'total_weight' => 3000,
            'total_price' => 10000 * 2,
        ]);

        $this->assertDatabaseHas('product_component_inventories', [
            'branch_id' => $branch->id,
            'product_component_id' => $productComponent->id,
            'quantity' => 2,
        ]);
    }

    /**
     * @test
     * @return void
     */
    public function shouldShowManufactureProductComponentDetailPage()
    {
        /** @var Permission */
        $permission = Permission::query()
            ->where('name', PermissionEnum::view_manufacture_product_components())
            ->first();
        /** @var User */
        $user = User::factory()->create();
        $user->permissions()->sync($permission->id);
        /** @var Branch */
        $branch = Branch::factory()->create();
        /** @var ManufactureProductComponent */
        $manufactureProductComponent = ManufactureProductComponent::factory()
            ->for($branch)
            ->for($user, 'creator')
            ->create();
        $response = $this->actingAs($user)->get("/manufacture-product-components/{$manufactureProductComponent->id}");

        $response->assertStatus(200);
    }

    /**
     * @test
     * @return void
     */
    public function shouldContainsManufactureProductComponentDataWhenShowManufactureProductComponentDetailPage()
    {
        /** @var Branch */
        $branch = Branch::factory()->create();
        /** @var ManufactureProductComponent */
        $manufactureProductComponent = ManufactureProductComponent::factory()
            ->for($branch)
            ->create();
        /** @var Permission */
        $permission = Permission::query()
            ->where('name', PermissionEnum::view_manufacture_product_components())
            ->first();
        /** @var User */
        $user = User::factory()->create();
        $user->permissions()->sync($permission->id);
        $response = $this->actingAs($user)->get("/manufacture-product-components/{$manufactureProductComponent->id}");

        $response->assertSee([
            $manufactureProductComponent->order_number,
            $branch->name,
        ]);
    }

    /**
     * @test
     * @return void
     */
    public function shouldDeleteManufactureProductComponent()
    {
        /** @var Permission */
        $permission = Permission::query()
            ->where('name', PermissionEnum::delete_manufacture_product_component())
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
                'quantity' => 10,
            ]);
        $data = [
            'created_at' => '2022-01-01 00:00:00',
            'branch_id' => $branch->id,
            'line_items' => [
                [
                    'product_component_id' => $productComponent->id,
                    'price' => 10000,
                    'quantity' => 2,
                    'total_weight' => 3000,
                ],
            ],
        ];
        $manufactureProductComponent = resolve(CreateManufactureProductComponentAction::class)->execute($data + [
            'created_by' => $user->id,
        ]);

        $response = $this->actingAs($user)->delete("/manufacture-product-components/{$manufactureProductComponent->id}");
        $response->assertSessionHas(['success']);

        $this->assertDatabaseMissing('manufacture_product_components', [
            'id' => $manufactureProductComponent->id,
        ]);

        $this->assertDatabaseMissing('manufacture_product_component_line_items', [
            'manufacture_product_component_id' => $manufactureProductComponent->id,
            'product_component_id' => $productComponent->id,
        ]);

        $this->assertDatabaseHas('product_component_inventories', [
            'id' => $productComponentInventory->id,
            'quantity' => 10,
        ]);
    }
}
