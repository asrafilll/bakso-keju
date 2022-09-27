<?php

namespace Tests\Feature;

use App\Enums\PermissionEnum;
use App\Models\Permission;
use App\Models\Reseller;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ResellerFeatureTest extends TestCase
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
    public function shouldShowResellerIndexPage()
    {
        /** @var Permission */
        $permission = Permission::query()
            ->where('name', PermissionEnum::view_resellers())
            ->first();
        /** @var User */
        $user = User::factory()->create();
        $user->permissions()->sync($permission->id);
        $response = $this->actingAs($user)->get('/resellers');

        $response->assertStatus(200);
    }

    /**
     * @test
     * @return void
     */
    public function shouldContainsResellerOnResellerIndexPage()
    {
        /** @var Reseller */
        $reseller = Reseller::factory()->create();;
        /** @var Permission */
        $permission = Permission::query()
            ->where('name', PermissionEnum::view_resellers())
            ->first();
        /** @var User */
        $user = User::factory()->create();
        $user->permissions()->sync($permission->id);
        $response = $this->actingAs($user)->get('/resellers');

        $response->assertSee([
            $reseller->name,
            $reseller->percentage_discount,
        ]);
    }

    /**
     * @test
     * @return void
     */
    public function shouldShowCreateResellerPage()
    {
        /** @var Permission */
        $permission = Permission::query()
            ->where('name', PermissionEnum::create_reseller())
            ->first();
        /** @var User */
        $user = User::factory()->create();
        $user->permissions()->sync($permission->id);
        $response = $this->actingAs($user)->get('/resellers/create');

        $response->assertStatus(200);
    }

    /**
     * @test
     * @return void
     */
    public function shouldCreateReseller()
    {
        /** @var Permission */
        $permission = Permission::query()
            ->where('name', PermissionEnum::create_reseller())
            ->first();
        /** @var User */
        $user = User::factory()->create();
        $user->permissions()->sync($permission->id);
        $this->actingAs($user)->post('/resellers', [
            'name' => 'Reseller #1',
            'percentage_discount' => 10,
        ]);

        $this->assertDatabaseHas('resellers', [
            'name' => 'Reseller #1',
            'percentage_discount' => 10,
        ]);
    }

    /**
     * @test
     * @return void
     */
    public function shouldShowResellerDetailPage()
    {
        /** @var Reseller */
        $reseller = Reseller::factory()->create();;
        /** @var Permission */
        $permission = Permission::query()
            ->where('name', PermissionEnum::update_reseller())
            ->first();
        /** @var User */
        $user = User::factory()->create();
        $user->permissions()->sync($permission->id);
        $response = $this->actingAs($user)->get("/resellers/{$reseller->id}");

        $response->assertStatus(200);
    }

    /**
     * @test
     * @return void
     */
    public function shouldContainsResellerDataOnResellerDetailPage()
    {
        /** @var Reseller */
        $reseller = Reseller::factory()->create();
        /** @var Permission */
        $permission = Permission::query()
            ->where('name', PermissionEnum::update_reseller())
            ->first();
        /** @var User */
        $user = User::factory()->create();
        $user->permissions()->sync($permission->id);
        $response = $this->actingAs($user)->get("/resellers/{$reseller->id}");

        $response->assertSee([
            $reseller->name,
            $reseller->percentage_discount,
        ]);
    }

    /**
     * @test
     * @return void
     */
    public function shouldUpdateReseller()
    {
        /** @var Reseller */
        $reseller = Reseller::factory()->create();
        /** @var Permission */
        $permission = Permission::query()
            ->where('name', PermissionEnum::update_reseller())
            ->first();
        /** @var User */
        $user = User::factory()->create();
        $user->permissions()->sync($permission->id);
        $this->actingAs($user)->put("/resellers/{$reseller->id}", [
            'name' => 'Reseller #2',
            'percentage_discount' => 10,
        ]);

        $this->assertDatabaseHas('resellers', [
            'id' => $reseller->id,
            'name' => 'Reseller #2',
            'percentage_discount' => 10,
        ]);
    }

    /**
     * @test
     * @return void
     */
    public function shouldDeleteReseller()
    {
        /** @var Reseller */
        $reseller = Reseller::factory()->create();
        /** @var Permission */
        $permission = Permission::query()
            ->where('name', PermissionEnum::delete_reseller())
            ->first();
        /** @var User */
        $user = User::factory()->create();
        $user->permissions()->sync($permission->id);
        $this->actingAs($user)->delete("/resellers/{$reseller->id}");

        $this->assertDatabaseMissing('resellers', [
            'id' => $reseller->id,
        ]);
    }
}
