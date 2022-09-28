<?php

namespace Tests\Feature;

use App\Enums\PermissionEnum;
use App\Models\OrderSource;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderSourceFeatureTest extends TestCase
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
    public function shouldShowOrderSourceIndexPage()
    {
        /** @var Permission */
        $permission = Permission::query()
            ->where('name', PermissionEnum::view_order_sources())
            ->first();
        /** @var User */
        $user = User::factory()->create();
        $user->permissions()->sync($permission->id);
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
        /** @var Permission */
        $permission = Permission::query()
            ->where('name', PermissionEnum::view_order_sources())
            ->first();
        /** @var User */
        $user = User::factory()->create();
        $user->permissions()->sync($permission->id);
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
        /** @var Permission */
        $permission = Permission::query()
            ->where('name', PermissionEnum::create_order_source())
            ->first();
        /** @var User */
        $user = User::factory()->create();
        $user->permissions()->sync($permission->id);
        $response = $this->actingAs($user)->get('/order-sources/create');

        $response->assertStatus(200);
    }

    /**
     * @test
     * @return void
     */
    public function shouldCreateOrderSource()
    {
        /** @var Permission */
        $permission = Permission::query()
            ->where('name', PermissionEnum::create_order_source())
            ->first();
        /** @var User */
        $user = User::factory()->create();
        $user->permissions()->sync($permission->id);
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
        /** @var Permission */
        $permission = Permission::query()
            ->where('name', PermissionEnum::view_order_sources())
            ->first();
        /** @var User */
        $user = User::factory()->create();
        $user->permissions()->sync($permission->id);
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
        /** @var Permission */
        $permission = Permission::query()
            ->where('name', PermissionEnum::view_order_sources())
            ->first();
        /** @var User */
        $user = User::factory()->create();
        $user->permissions()->sync($permission->id);
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
        /** @var Permission */
        $permission = Permission::query()
            ->where('name', PermissionEnum::update_order_source())
            ->first();
        /** @var User */
        $user = User::factory()->create();
        $user->permissions()->sync($permission->id);
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
        /** @var Permission */
        $permission = Permission::query()
            ->where('name', PermissionEnum::delete_order_source())
            ->first();
        /** @var User */
        $user = User::factory()->create();
        $user->permissions()->sync($permission->id);
        $this->actingAs($user)->delete("/order-sources/{$orderSource->id}");

        $this->assertDatabaseMissing('order_sources', [
            'id' => $orderSource->id,
        ]);
    }
}
