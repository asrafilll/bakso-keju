<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleFeatureTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * @return void
     */
    public function shouldShowRoleIndexPage()
    {
        /** @var User */
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/roles');

        $response->assertStatus(200);
    }

    /**
     * @test
     * @return void
     */
    public function shouldContainsRoleOnRoleIndexPage()
    {
        /** @var Role */
        $role = Role::create(['name' => 'super admin']);
        /** @var User */
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/roles');

        $response->assertSee($role->name);
    }

    /**
     * @test
     * @return void
     */
    public function shouldShowCreateRolePage()
    {
        /** @var User */
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/roles/create');

        $response->assertStatus(200);
    }

    /**
     * @test
     * @return void
     */
    public function shouldCreateRole()
    {
        /** @var User */
        $user = User::factory()->create();

        $this->actingAs($user)->post('/roles', [
            'name' => 'example role',
        ]);

        $this->assertDatabaseHas('roles', [
            'name' => 'example role',
        ]);
    }

    /**
     * @test
     * @return void
     */
    public function shouldShowRoleDetailPage()
    {
        /** @var Role */
        $role = Role::create(['name' => 'super admin']);
        /** @var User */
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get("/roles/{$role->id}");

        $response->assertStatus(200);
    }

    /**
     * @test
     * @return void
     */
    public function shouldContainsRoleDataOnRoleDetailPage()
    {
        /** @var Role */
        $role = Role::create(['name' => 'super admin']);
        /** @var User */
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get("/roles/{$role->id}");

        $response->assertSee($role->name);
    }

    /**
     * @test
     * @return void
     */
    public function shouldFailedToShowRoleDetailPageWhenRoleNotFound()
    {
        /** @var User */
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get("/roles/stub-role-id");

        $response->assertStatus(404);
    }
}
