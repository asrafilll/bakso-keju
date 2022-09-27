<?php

namespace Tests\Feature;

use App\Models\Permission;
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
        $this->seed();

        /** @var User */
        $user = User::factory()->create();
        /** @var Permission */
        $permission = Permission::first();

        $this->actingAs($user)->post('/roles', [
            'name' => 'example role',
            'permissions' => [
                $permission->id,
            ],
        ]);

        $this->assertDatabaseHas('roles', [
            'name' => 'example role',
        ]);

        $this->assertDatabaseHas('role_has_permissions', [
            'permission_id' => $permission->id,
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

    /**
     * @test
     * @return void
     */
    public function shouldUpdateRole()
    {
        $this->seed();

        /** @var Permission */
        $permission = Permission::inRandomOrder()->first();
        /** @var Role */
        $role = Role::create(['name' => 'super admin']);
        /** @var User */
        $user = User::factory()->create();

        $this->actingAs($user)->put("/roles/{$role->id}", [
            'name' => 'admin',
            'permissions' => [
                $permission->id,
            ]
        ]);

        $this->assertDatabaseHas('roles', [
            'id' => $role->id,
            'name' => 'admin',
        ]);

        $this->assertDatabaseHas('role_has_permissions', [
            'permission_id' => $permission->id,
        ]);
    }

    /**
     * @test
     * @return void
     */
    public function shouldDeleteRole()
    {
        /** @var Role */
        $role = Role::create(['name' => 'super admin']);
        /** @var User */
        $user = User::factory()->create();

        $this->actingAs($user)->delete("/roles/{$role->id}");

        $this->assertDatabaseMissing('roles', [
            'name' => $role->name,
        ]);
    }
}
