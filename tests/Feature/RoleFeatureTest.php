<?php

namespace Tests\Feature;

use App\Enums\PermissionEnum;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleFeatureTest extends TestCase
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
    public function shouldShowRoleIndexPage()
    {
        /** @var Permission */
        $permission = Permission::query()
            ->where('name', PermissionEnum::view_roles())
            ->first();
        /** @var User */
        $user = User::factory()->create();
        $user->permissions()->sync($permission->id);
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
        /** @var Permission */
        $permission = Permission::query()
            ->where('name', PermissionEnum::view_roles())
            ->first();
        /** @var User */
        $user = User::factory()->create();
        $user->permissions()->sync($permission->id);
        $response = $this->actingAs($user)->get('/roles');

        $response->assertSee($role->name);
    }

    /**
     * @test
     * @return void
     */
    public function shouldShowCreateRolePage()
    {
        /** @var Permission */
        $permission = Permission::query()
            ->where('name', PermissionEnum::create_role())
            ->first();
        /** @var User */
        $user = User::factory()->create();
        $user->permissions()->sync($permission->id);
        $response = $this->actingAs($user)->get('/roles/create');

        $response->assertStatus(200);
    }

    /**
     * @test
     * @return void
     */
    public function shouldCreateRole()
    {
        /** @var Permission */
        $permission = Permission::query()
            ->where('name', PermissionEnum::create_role())
            ->first();
        /** @var User */
        $user = User::factory()->create();
        $user->permissions()->sync($permission->id);
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
        /** @var Permission */
        $permission = Permission::query()
            ->where('name', PermissionEnum::update_role())
            ->first();
        /** @var User */
        $user = User::factory()->create();
        $user->permissions()->sync($permission->id);
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
        /** @var Permission */
        $permission = Permission::query()
            ->where('name', PermissionEnum::update_role())
            ->first();
        /** @var User */
        $user = User::factory()->create();
        $user->permissions()->sync($permission->id);
        $response = $this->actingAs($user)->get("/roles/{$role->id}");

        $response->assertSee($role->name);
    }

    /**
     * @test
     * @return void
     */
    public function shouldFailedToShowRoleDetailPageWhenRoleNotFound()
    {
        /** @var Permission */
        $permission = Permission::query()
            ->where('name', PermissionEnum::update_role())
            ->first();
        /** @var User */
        $user = User::factory()->create();
        $user->permissions()->sync($permission->id);
        $response = $this->actingAs($user)->get("/roles/stub-role-id");

        $response->assertStatus(404);
    }

    /**
     * @test
     * @return void
     */
    public function shouldUpdateRole()
    {
        /** @var Permission */
        $permission = Permission::inRandomOrder()->first();
        /** @var Role */
        $role = Role::create(['name' => 'super admin']);
        /** @var Permission */
        $permission = Permission::query()
            ->where('name', PermissionEnum::update_role())
            ->first();
        /** @var User */
        $user = User::factory()->create();
        $user->permissions()->sync($permission->id);
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
        /** @var Permission */
        $permission = Permission::query()
            ->where('name', PermissionEnum::delete_role())
            ->first();
        /** @var User */
        $user = User::factory()->create();
        $user->permissions()->sync($permission->id);
        $this->actingAs($user)->delete("/roles/{$role->id}");

        $this->assertDatabaseMissing('roles', [
            'name' => $role->name,
        ]);
    }
}
