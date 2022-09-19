<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Order;
use App\Models\OrderSource;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderFeatureTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * @return void
     */
    public function shouldShowOrderIndexPage()
    {
        /** @var User */
        $user = User::factory()->create();
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
        /** @var User */
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/orders');

        $response->assertSee([
            $order->order_number,
            $order->created_at,
            $branch->name,
            $orderSource->name,
            $order->customer_name,
            $order->percentage_discount,
            $order->total_discount,
            $order->total_line_items_quantity,
            $order->total_line_items_price,
            $order->total_price,
        ]);
    }

    /**
     * @test
     * @return void
     */
    public function shouldShowCreateOrderPage()
    {
        /** @var User */
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/orders/create');

        $response->assertStatus(200);
    }
}
