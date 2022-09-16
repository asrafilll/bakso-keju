<?php

namespace Tests\Feature;

use App\Models\OrderSource;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderSourceFeatureTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * @return void
     */
    public function shouldShowOrderSourceIndexPage()
    {
        /** @var User */
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/order-sources');

        $response->assertStatus(200);
    }

    /**
     * @test
     * @return void
     */
    public function shouldContainsOrderSourceOnOrderSourceIndexPage()
    {
        /** @var OrderSource */
        $orderSource = OrderSource::factory()->create();;
        /** @var User */
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/order-sources');

        $response->assertSee([
            $orderSource->name,
        ]);
    }

    /**
     * @test
     * @return void
     */
    public function shouldShowCreateOrderSourcePage()
    {
        /** @var User */
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/order-sources/create');

        $response->assertStatus(200);
    }

    /**
     * @test
     * @return void
     */
    public function shouldCreateOrderSource()
    {
        /** @var User */
        $user = User::factory()->create();

        $this->actingAs($user)->post('/order-sources', [
            'name' => 'OrderSource #1',
        ]);

        $this->assertDatabaseHas('order_sources', [
            'name' => 'OrderSource #1',
        ]);
    }

    /**
     * @test
     * @return void
     */
    public function shouldShowOrderSourceDetailPage()
    {
        /** @var OrderSource */
        $orderSource = OrderSource::factory()->create();;
        /** @var User */
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get("/order-sources/{$orderSource->id}");

        $response->assertStatus(200);
    }

    /**
     * @test
     * @return void
     */
    public function shouldContainsOrderSourceDataOnOrderSourceDetailPage()
    {
        /** @var OrderSource */
        $orderSource = OrderSource::factory()->create();
        /** @var User */
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get("/order-sources/{$orderSource->id}");

        $response->assertSee([
            $orderSource->name,
        ]);
    }

    /**
     * @test
     * @return void
     */
    public function shouldUpdateOrderSource()
    {
        /** @var OrderSource */
        $orderSource = OrderSource::factory()->create();
        /** @var User */
        $user = User::factory()->create();

        $this->actingAs($user)->put("/order-sources/{$orderSource->id}", [
            'name' => 'OrderSource #2',
        ]);

        $this->assertDatabaseHas('order_sources', [
            'id' => $orderSource->id,
            'name' => 'OrderSource #2',
        ]);
    }

    /**
     * @test
     * @return void
     */
    public function shouldDeleteOrderSource()
    {
        /** @var OrderSource */
        $orderSource = OrderSource::factory()->create();
        /** @var User */
        $user = User::factory()->create();

        $this->actingAs($user)->delete("/order-sources/{$orderSource->id}");

        $this->assertDatabaseMissing('order_sources', [
            'id' => $orderSource->id,
        ]);
    }
}
