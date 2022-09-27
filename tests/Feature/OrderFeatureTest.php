<?php

namespace Tests\Feature;

use App\Actions\CreateOrderAction;
use App\Enums\PermissionEnum;
use App\Models\Branch;
use App\Models\Order;
use App\Models\OrderSource;
use App\Models\Permission;
use App\Models\Product;
use App\Models\ProductInventory;
use App\Models\Reseller;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class OrderFeatureTest extends TestCase
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
    public function shouldShowOrderIndexPage()
    {
        /** @var Permission */
        $permission = Permission::query()
            ->where('name', PermissionEnum::view_orders())
            ->first();
        /** @var User */
        $user = User::factory()->create();
        $user->permissions()->sync($permission->id);
        $response = $this->actingAs($user)->get('/orders');

        $response->assertStatus(200);
    }

    /**
     * @test
     * @return void
     */
    public function shouldContainsOrderOnOrderIndexPage()
    {
        /** @var Branch */
        $branch = Branch::factory()->create();
        /** @var OrderSource */
        $orderSource = OrderSource::factory()->create();
        /** @var Order */
        $order = Order::factory()
            ->for($branch)
            ->for($orderSource)
            ->create();
        /** @var Permission */
        $permission = Permission::query()
            ->where('name', PermissionEnum::view_orders())
            ->first();
        /** @var User */
        $user = User::factory()->create();
        $user->permissions()->sync($permission->id);
        $response = $this->actingAs($user)->get('/orders');

        $response->assertSee([
            $order->order_number,
            $order->created_at,
            $branch->name,
            $orderSource->name,
            $order->customer_name,
        ]);
    }

    /**
     * @test
     * @return void
     */
    public function shouldShowCreateOrderPage()
    {
        /** @var Permission */
        $permission = Permission::query()
            ->where('name', PermissionEnum::create_order())
            ->first();
        /** @var User */
        $user = User::factory()->create();
        $user->permissions()->sync($permission->id);
        $response = $this->actingAs($user)->get('/orders/create');

        $response->assertStatus(200);
    }

    /**
     * @test
     * @return void
     */
    public function shouldCreateOrder()
    {
        /** @var Branch */
        $branch = Branch::factory()->create();
        /** @var OrderSource */
        $orderSource = OrderSource::factory()->create();
        /** @var Product */
        $product = Product::factory()->create();
        /** @var ProductInventory */
        $productInventory = ProductInventory::factory()
            ->state([
                'quantity' => 10,
            ])
            ->for($branch)
            ->for($product)
            ->create();
        /** @var Permission */
        $permission = Permission::query()
            ->where('name', PermissionEnum::create_order())
            ->first();
        /** @var User */
        $user = User::factory()->create();
        $user->permissions()->sync($permission->id);
        $response = $this->actingAs($user)->post('/orders', [
            'created_at' => '2022-01-01 00:00:00',
            'branch_id' => $branch->id,
            'order_source_id' => $orderSource->id,
            'customer_name' => 'John Doe',
            'line_items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 2,
                ],
            ],
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('orders', [
            'created_at' => '2022-01-01 00:00:00',
            'branch_id' => $branch->id,
            'order_source_id' => $orderSource->id,
            'reseller_order' => false,
            'reseller_id' => null,
            'customer_name' => 'John Doe',
            'percentage_discount' => 0,
            'total_discount' => 0,
            'total_line_items_quantity' => 2,
            'total_line_items_price' => $product->price * 2,
            'total_price' => $product->price * 2,
        ]);

        $this->assertDatabaseHas('order_line_items', [
            'product_id' => $product->id,
            'product_name' => $product->name,
            'price' => $product->price,
            'quantity' => 2,
            'total' => $product->price * 2,
        ]);

        $this->assertDatabaseHas('branches', [
            'id' => $branch->id,
            'next_order_number' => $branch->next_order_number + 1,
        ]);

        $this->assertDatabaseHas('product_inventories', [
            'id' => $productInventory->id,
            'quantity' => 8,
        ]);
    }

    /**
     * @test
     * @return void
     */
    public function shouldCreateOrderWithResellerAndDiscount()
    {
        /** @var Branch */
        $branch = Branch::factory()->create();
        /** @var OrderSource */
        $orderSource = OrderSource::factory()->create();
        /** @var Product */
        $product = Product::factory()->create();
        ProductInventory::factory()
            ->state([
                'quantity' => 10,
            ])
            ->for($branch)
            ->for($product)
            ->create();
        /** @var Reseller */
        $reseller = Reseller::factory()
            ->state([
                'percentage_discount' => 10,
            ])
            ->create();
        /** @var Permission */
        $permission = Permission::query()
            ->where('name', PermissionEnum::create_order())
            ->first();
        /** @var User */
        $user = User::factory()->create();
        $user->permissions()->sync($permission->id);
        $response = $this->actingAs($user)->post('/orders', [
            'created_at' => '2022-01-01 00:00:00',
            'branch_id' => $branch->id,
            'order_source_id' => $orderSource->id,
            'reseller_id' => $reseller->id,
            'customer_name' => 'John Doe',
            'line_items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 2,
                ],
            ],
        ]);

        $response->assertRedirect();

        $totalLineItemsPrice = $product->price * 2;
        $totalDiscount = round($totalLineItemsPrice * ($reseller->percentage_discount / 100));

        $this->assertDatabaseHas('orders', [
            'created_at' => '2022-01-01 00:00:00',
            'branch_id' => $branch->id,
            'order_source_id' => $orderSource->id,
            'reseller_order' => true,
            'reseller_id' => $reseller->id,
            'customer_name' => 'John Doe',
            'percentage_discount' => $reseller->percentage_discount,
            'total_discount' => $totalDiscount,
            'total_line_items_quantity' => 2,
            'total_line_items_price' => $totalLineItemsPrice,
            'total_price' => $totalLineItemsPrice - $totalDiscount,
        ]);
    }

    /**
     * @test
     * @return void
     */
    public function shouldFailedCreateOrderWhenProductInventoryQuantityLessThanOrderLineItemQuantity()
    {
        /** @var Branch */
        $branch = Branch::factory()->create();
        /** @var OrderSource */
        $orderSource = OrderSource::factory()->create();
        /** @var Product */
        $product = Product::factory()->create();
        ProductInventory::factory()
            ->state([
                'quantity' => 1,
            ])
            ->for($branch)
            ->for($product)
            ->create();
        /** @var Reseller */
        $reseller = Reseller::factory()
            ->state([
                'percentage_discount' => 10,
            ])
            ->create();
        /** @var Permission */
        $permission = Permission::query()
            ->where('name', PermissionEnum::create_order())
            ->first();
        /** @var User */
        $user = User::factory()->create();
        $user->permissions()->sync($permission->id);
        $response = $this->actingAs($user)->post('/orders', [
            'created_at' => '2022-01-01 00:00:00',
            'branch_id' => $branch->id,
            'order_source_id' => $orderSource->id,
            'reseller_id' => $reseller->id,
            'customer_name' => 'John Doe',
            'line_items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 2,
                ],
            ],
        ]);

        $response->assertSessionHas(['failed']);
    }

    /**
     * @test
     * @return void
     */
    public function shouldShowOrderDetailPage()
    {
        /** @var Branch */
        $branch = Branch::factory()->create();
        /** @var OrderSource */
        $orderSource = OrderSource::factory()->create();
        /** @var Order */
        $order = Order::factory()
            ->for($branch)
            ->for($orderSource)
            ->create();
        /** @var Permission */
        $permission = Permission::query()
            ->where('name', PermissionEnum::view_orders())
            ->first();
        /** @var User */
        $user = User::factory()->create();
        $user->permissions()->sync($permission->id);
        $response = $this->actingAs($user)->get("/orders/{$order->id}");

        $response->assertStatus(200);
    }

    /**
     * @test
     * @return void
     */
    public function shouldContainsOrderDataWhenShowOrderDetailPage()
    {
        /** @var Branch */
        $branch = Branch::factory()->create();
        /** @var OrderSource */
        $orderSource = OrderSource::factory()->create();
        /** @var Order */
        $order = Order::factory()
            ->for($branch)
            ->for($orderSource)
            ->create();
        /** @var Permission */
        $permission = Permission::query()
            ->where('name', PermissionEnum::view_orders())
            ->first();
        /** @var User */
        $user = User::factory()->create();
        $user->permissions()->sync($permission->id);
        $response = $this->actingAs($user)->get("/orders/{$order->id}");

        $response->assertSee([
            $order->order_number,
            $branch->name,
            $orderSource->name,
            $order->customer_name,
        ]);
    }

    /**
     * @test
     * @return void
     */
    public function shouldDeleteOrder()
    {
        /** @var Branch */
        $branch = Branch::factory()->create();
        /** @var OrderSource */
        $orderSource = OrderSource::factory()->create();
        /** @var Product */
        $product = Product::factory()->create();
        /** @var ProductInventory */
        $productInventory = ProductInventory::factory()
            ->state([
                'quantity' => 10,
            ])
            ->for($branch)
            ->for($product)
            ->create();
        $data = [
            'created_at' => '2022-01-01 00:00:00',
            'branch_id' => $branch->id,
            'order_source_id' => $orderSource->id,
            'customer_name' => 'John Doe',
            'line_items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 2,
                ],
            ],
        ];
        $order = resolve(CreateOrderAction::class)->execute($data);
        /** @var Permission */
        $permission = Permission::query()
            ->where('name', PermissionEnum::delete_order())
            ->first();
        /** @var User */
        $user = User::factory()->create();
        $user->permissions()->sync($permission->id);
        $response = $this->actingAs($user)->delete("/orders/{$order->id}");
        $response->assertSessionHas(['success']);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'deleted_at' => Carbon::now(),
        ]);

        $this->assertDatabaseHas('product_inventories', [
            'id' => $productInventory->id,
            'quantity' => 10,
        ]);
    }
}
