<?php

namespace Tests\Feature;

use App\Actions\CreateManufacturingOrderAction;
use App\Enums\PermissionEnum;
use App\Models\Branch;
use App\Models\ManufacturingOrder;
use App\Models\Permission;
use App\Models\Product;
use App\Models\ProductComponent;
use App\Models\ProductInventory;
use App\Models\Reseller;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class ManufacturingOrderFeatureTest extends TestCase
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
    public function shouldShowManufacturingOrderIndexPage()
    {
        /** @var Permission */
        $permission = Permission::query()
            ->where('name', PermissionEnum::view_manufacturing_orders())
            ->first();
        /** @var User */
        $user = User::factory()->create();
        $user->permissions()->sync($permission->id);
        $response = $this->actingAs($user)->get('/manufacturing-orders');

        $response->assertStatus(200);
    }

    /**
     * @test
     * @return void
     */
    public function shouldContainsManufacturingOrderOnManufacturingOrderIndexPage()
    {
        /** @var Permission */
        $permission = Permission::query()
            ->where('name', PermissionEnum::view_manufacturing_orders())
            ->first();
        /** @var User */
        $user = User::factory()->create();
        $user->permissions()->sync($permission->id);

        /** @var Branch */
        $branch = Branch::factory()->create();
        /** @var ManufacturingOrder */
        $manufacturingOrder = ManufacturingOrder::factory()
            ->for($branch)
            ->create([
                'created_by' => $user->id,
            ]);
        $response = $this->actingAs($user)->get('/manufacturing-orders');

        $response->assertSee([
            $manufacturingOrder->order_number,
            $manufacturingOrder->created_at,
            $branch->name,
        ]);
    }

    /**
     * @test
     * @return void
     */
    public function shouldShowCreateManufacturingOrderPage()
    {
        /** @var Permission */
        $permission = Permission::query()
            ->where('name', PermissionEnum::create_manufacturing_order())
            ->first();
        /** @var User */
        $user = User::factory()->create();
        $user->permissions()->sync($permission->id);
        $response = $this->actingAs($user)->get('/manufacturing-orders/create');

        $response->assertStatus(200);
    }

    /**
     * @test
     * @return void
     */
    public function shouldCreateManufacturingOrder()
    {
        /** @var Branch */
        $branch = Branch::factory()->create();
        /** @var ProductComponent */
        $productComponent = ProductComponent::factory()->create();
        /** @var Permission */
        $permission = Permission::query()
            ->where('name', PermissionEnum::create_manufacturing_order())
            ->first();
        /** @var User */
        $user = User::factory()->create();
        $user->permissions()->sync($permission->id);
        $response = $this->actingAs($user)->post('/manufacturing-orders', [
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

        $this->assertDatabaseHas('manufacturing_orders', [
            'created_at' => '2022-01-01 00:00:00',
            'created_by' => $user->id,
            'branch_id' => $branch->id,
            'total_line_items_quantity' => 2,
            'total_line_items_weight' => 3000,
            'total_line_items_price' => 10000 * 2,
        ]);

        $this->assertDatabaseHas('manufacturing_order_line_items', [
            'product_component_id' => $productComponent->id,
            'product_component_name' => $productComponent->name,
            'price' => 10000,
            'quantity' => 2,
            'total_weight' => 3000,
            'total_price' => 10000 * 2,
        ]);
    }

    /**
     * @test
     * @return void
     */
    public function shouldShowManufacturingOrderDetailPage()
    {
        /** @var Branch */
        $branch = Branch::factory()->create();
        /** @var ManufacturingOrder */
        $manufacturingOrder = ManufacturingOrder::factory()
            ->for($branch)
            ->create();
        /** @var Permission */
        $permission = Permission::query()
            ->where('name', PermissionEnum::view_manufacturing_orders())
            ->first();
        /** @var User */
        $user = User::factory()->create();
        $user->permissions()->sync($permission->id);
        $response = $this->actingAs($user)->get("/manufacturing-orders/{$manufacturingOrder->id}");

        $response->assertStatus(200);
    }

    /**
     * @test
     * @return void
     */
    public function shouldContainsManufacturingOrderDataWhenShowManufacturingOrderDetailPage()
    {
        /** @var Branch */
        $branch = Branch::factory()->create();
        /** @var ManufacturingOrder */
        $manufacturingOrder = ManufacturingOrder::factory()
            ->for($branch)
            ->create();
        /** @var Permission */
        $permission = Permission::query()
            ->where('name', PermissionEnum::view_manufacturing_orders())
            ->first();
        /** @var User */
        $user = User::factory()->create();
        $user->permissions()->sync($permission->id);
        $response = $this->actingAs($user)->get("/manufacturing-orders/{$manufacturingOrder->id}");

        $response->assertSee([
            $manufacturingOrder->order_number,
            $branch->name,
            $manufacturingOrder->customer_name,
        ]);
    }

    /**
     * @test
     * @return void
     */
    public function shouldDeleteManufacturingOrder()
    {
        /** @var Permission */
        $permission = Permission::query()
            ->where('name', PermissionEnum::delete_manufacturing_order())
            ->first();
        /** @var User */
        $user = User::factory()->create();
        $user->permissions()->sync($permission->id);
        /** @var Branch */
        $branch = Branch::factory()->create();
        /** @var ProductComponent */
        $productComponent = ProductComponent::factory()->create();
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
        $manufacturingOrder = resolve(CreateManufacturingOrderAction::class)->execute($data + [
            'created_by' => $user->id,
        ]);

        $response = $this->actingAs($user)->delete("/manufacturing-orders/{$manufacturingOrder->id}");
        $response->assertSessionHas(['success']);

        $this->assertDatabaseMissing('manufacturing_orders', [
            'id' => $manufacturingOrder->id,
        ]);
    }
}
