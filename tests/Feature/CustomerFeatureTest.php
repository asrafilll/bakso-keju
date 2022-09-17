<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerFeatureTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * @return void
     */
    public function shouldShowCustomerIndexPage()
    {
        /** @var User */
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/customers');

        $response->assertStatus(200);
    }

    /**
     * @test
     * @return void
     */
    public function shouldContainsCustomerOnCustomerIndexPage()
    {
        /** @var Customer */
        $customer = Customer::factory()->create();;
        /** @var User */
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/customers');

        $response->assertSee([
            $customer->name,
            $customer->percentage_discount,
        ]);
    }

    /**
     * @test
     * @return void
     */
    public function shouldShowCreateCustomerPage()
    {
        /** @var User */
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/customers/create');

        $response->assertStatus(200);
    }

    /**
     * @test
     * @return void
     */
    public function shouldCreateCustomer()
    {
        /** @var User */
        $user = User::factory()->create();

        $this->actingAs($user)->post('/customers', [
            'name' => 'Customer #1',
            'percentage_discount' => 10,
        ]);

        $this->assertDatabaseHas('customers', [
            'name' => 'Customer #1',
            'percentage_discount' => 10,
        ]);
    }

    /**
     * @test
     * @return void
     */
    public function shouldShowCustomerDetailPage()
    {
        /** @var Customer */
        $customer = Customer::factory()->create();;
        /** @var User */
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get("/customers/{$customer->id}");

        $response->assertStatus(200);
    }

    /**
     * @test
     * @return void
     */
    public function shouldContainsCustomerDataOnCustomerDetailPage()
    {
        /** @var Customer */
        $customer = Customer::factory()->create();
        /** @var User */
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get("/customers/{$customer->id}");

        $response->assertSee([
            $customer->name,
            $customer->percentage_discount,
        ]);
    }

    /**
     * @test
     * @return void
     */
    public function shouldUpdateCustomer()
    {
        /** @var Customer */
        $customer = Customer::factory()->create();
        /** @var User */
        $user = User::factory()->create();

        $this->actingAs($user)->put("/customers/{$customer->id}", [
            'name' => 'Customer #2',
            'percentage_discount' => 10,
        ]);

        $this->assertDatabaseHas('customers', [
            'id' => $customer->id,
            'name' => 'Customer #2',
            'percentage_discount' => 10,
        ]);
    }

    /**
     * @test
     * @return void
     */
    public function shouldDeleteCustomer()
    {
        /** @var Customer */
        $customer = Customer::factory()->create();
        /** @var User */
        $user = User::factory()->create();

        $this->actingAs($user)->delete("/customers/{$customer->id}");

        $this->assertDatabaseMissing('customers', [
            'id' => $customer->id,
        ]);
    }
}
