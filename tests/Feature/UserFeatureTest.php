<?php

namespace Tests\Feature;

use App\Enums\PermissionEnum;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Tests\TestCase;

class UserFeatureTest extends TestCase
{
    use RefreshDatabase;

    /** @var User */
    protected $user;

    /**
     * @test
     * @return void
     */
    public function shouldShowUserIndexPage()
    {
        /** @var Permission */
        $permission = Permission::query()
            ->where('name', PermissionEnum::view_users())
            ->first();
        /** @var User */
        $user = User::factory()->create();
        $user->permissions()->sync($permission->id);
        $response = $this->actingAs($user)
            ->get('/users');

        $response->assertStatus(200);
    }

    /**
     * @test
     * @return void
     */
    public function shouldContainsUserOnUserIndexPage()
    {
        /** @var Collection<User> */
        $sampleUsers = User::factory(10)->create();
        $sampleUser = $sampleUsers->first();
        /** @var Permission */
        $permission = Permission::query()
            ->where('name', PermissionEnum::view_users())
            ->first();
        /** @var User */
        $user = User::factory()->create();
        $user->permissions()->sync($permission->id);
        $response = $this->actingAs($user)
            ->get('/users');

        $response->assertSee($sampleUser->email);
    }

    /**
     * @test
     * @return void
     */
    public function shouldShowCreateUserPage()
    {
        /** @var Permission */
        $permission = Permission::query()
            ->where('name', PermissionEnum::create_user())
            ->first();
        /** @var User */
        $user = User::factory()->create();
        $user->permissions()->sync($permission->id);
        $response = $this->actingAs($user)
            ->get('/users/create');

        $response->assertStatus(200);
    }

    /**
     * @test
     * @return void
     */
    public function shouldCreateUser()
    {
        /** @var Permission */
        $permission = Permission::query()
            ->where('name', PermissionEnum::create_user())
            ->first();
        /** @var User */
        $user = User::factory()->create();
        $user->permissions()->sync($permission->id);
        $this->actingAs($user)
            ->post('/users', [
                'name' => 'John Doe',
                'email' => 'johndoe@example.com',
                'password' => 'secret',
                'password_confirmation' => 'secret',
            ]);

        $this->assertDatabaseHas('users', [
            'name' => 'John Doe',
            'email' => 'johndoe@example.com',
        ]);
    }

    /**
     * @test
     * @return void
     */
    public function shouldCreateUserWithRoles()
    {
        /** @var Role */
        $role1 = Role::create(['name' => 'Role 1']);

        /** @var Role */
        $role2 = Role::create(['name' => 'Role 2']);

        /** @var Permission */
        $permission = Permission::query()
            ->where('name', PermissionEnum::create_user())
            ->first();
        /** @var User */
        $user = User::factory()->create();
        $user->permissions()->sync($permission->id);
        $this->actingAs($user)->post('/users', [
            'name' => 'John Doe',
            'email' => 'johndoe@example.com',
            'password' => 'secret',
            'password_confirmation' => 'secret',
            'roles' => [
                $role1->id,
                $role2->id,
            ],
        ]);

        /** @var User */
        $createdUser = User::whereEmail('johndoe@example.com')->first();

        $this->assertDatabaseHas('users', [
            'name' => 'John Doe',
            'email' => 'johndoe@example.com',
        ]);

        $this->assertDatabaseHas('model_has_roles', [
            'model_id' => $createdUser->id,
            'model_type' => User::class,
            'role_id' => $role1->id,
        ]);

        $this->assertDatabaseHas('model_has_roles', [
            'model_id' => $createdUser->id,
            'model_type' => User::class,
            'role_id' => $role2->id,
        ]);
    }

    public function invalidDataForCreateUser(): array
    {
        return [
            'Null data' => [
                [],
                [
                    'name',
                    'email',
                    'password',
                ],
            ],
            'name: null, email: null, password: null, password_confirmation: null' => [
                [
                    'name' => null,
                    'email' => null,
                    'password' => null,
                    'password_confirmation' => null,
                ],
                [
                    'name',
                    'email',
                    'password',
                ],
            ],
            'password_confirmation: null' => [
                [
                    'name' => 'John Doe',
                    'email' => 'johndoe@example.com',
                    'password' => 'secret',
                ],
                [
                    'password',
                ],
            ],
            'email: not a email, password_confirmation: difference with password' => [
                [
                    'name' => 'John Doe',
                    'email' => 'john doe',
                    'password' => 'secret',
                    'password_confirmation' => 'password',
                ],
                [
                    'email',
                    'password',
                ],
            ],
        ];
    }

    /**
     * @test
     * @dataProvider invalidDataForCreateUser
     * @param array $data
     * @param array $expectedErrors
     * @return void
     */
    public function shouldFailedToCreateUserBecauseValidationError($data, $expectedErrors)
    {
        /** @var Permission */
        $permission = Permission::query()
            ->where('name', PermissionEnum::create_user())
            ->first();
        /** @var User */
        $user = User::factory()->create();
        $user->permissions()->sync($permission->id);
        $response = $this->actingAs($user)
            ->post('/users', $data);

        $response->assertSessionHasErrors($expectedErrors);
    }

    /**
     * @test
     * @return void
     */
    public function shouldShowUserDetailPage()
    {
        /** @var Permission */
        $permission = Permission::query()
            ->where('name', PermissionEnum::view_users())
            ->first();
        /** @var User */
        $createdUser = User::factory()->create();
        /** @var User */
        $user = User::factory()->create();
        $user->permissions()->sync($permission->id);
        $response = $this->actingAs($user)
            ->get("/users/{$createdUser->id}");

        $response->assertStatus(200);
    }

    /**
     * @test
     * @return void
     */
    public function shouldContainsUserDataOnUserDetailPage()
    {
        /** @var Permission */
        $permission = Permission::query()
            ->where('name', PermissionEnum::view_users())
            ->first();
        /** @var User */
        $createdUser = User::factory()->create();
        /** @var User */
        $user = User::factory()->create();
        $user->permissions()->sync($permission->id);
        $response = $this->actingAs($user)
            ->get("/users/{$createdUser->id}");

        $response->assertSee($createdUser->email);
        $response->assertSee($createdUser->name);
    }

    /**
     * @test
     * @return void
     */
    public function shouldContainsUserDataWithRolesOnUserDetailPage()
    {
        /** @var Role */
        $role1 = Role::create(['name' => 'Role 1']);

        /** @var Role */
        $role2 = Role::create(['name' => 'Role 2']);

        /** @var User */
        $createdUser = User::factory()->create();
        $createdUser->assignRole($role1, $role2);
        /** @var Permission */
        $permission = Permission::query()
            ->where('name', PermissionEnum::view_users())
            ->first();
        /** @var User */
        $user = User::factory()->create();
        $user->permissions()->sync($permission->id);
        $response = $this
            ->actingAs($user)
            ->get("/users/{$createdUser->id}");

        $response->assertSee([
            $createdUser->email,
            $createdUser->name,
            $role1->name,
            $role2->name,
        ]);
    }

    /**
     * @test
     * @return void
     */
    public function shouldErrorShowUserDetailPageWhenUserNotFound()
    {
        /** @var Permission */
        $permission = Permission::query()
            ->where('name', PermissionEnum::update_user())
            ->first();
        /** @var User */
        $user = User::factory()->create();
        $user->permissions()->sync($permission->id);
        $response = $this->actingAs($user)
            ->get('/users/some-user-id');

        $response->assertStatus(404);
    }

    /**
     * @test
     * @return void
     */
    public function shouldUpdateUserWithoutUpdatingPassword()
    {
        /** @var User */
        $createdUser = User::factory()->create();
        /** @var Permission */
        $permission = Permission::query()
            ->where('name', PermissionEnum::update_user())
            ->first();
        /** @var User */
        $user = User::factory()->create();
        $user->permissions()->sync($permission->id);
        $this->actingAs($user)
            ->put("/users/{$createdUser->id}", [
                'name' => 'John Doe',
                'email' => 'johndoe@example.com',
            ]);

        $updatedUser = User::find($createdUser->id);

        $this->assertEquals('John Doe', $updatedUser->name);
        $this->assertEquals('johndoe@example.com', $updatedUser->email);
        $this->assertEquals($createdUser->password, $updatedUser->password);
    }

    /**
     * @test
     * @return void
     */
    public function shouldUpdateUserWithUpdatingPassword()
    {
        /** @var User */
        $createdUser = User::factory()->create();
        /** @var Permission */
        $permission = Permission::query()
            ->where('name', PermissionEnum::update_user())
            ->first();
        /** @var User */
        $user = User::factory()->create();
        $user->permissions()->sync($permission->id);
        $this->actingAs($user)
            ->put("/users/{$createdUser->id}", [
                'name' => 'John Doe',
                'email' => 'johndoe@example.com',
                'password' => 'secret',
                'password_confirmation' => 'secret',
            ]);

        $updatedUser = User::find($createdUser->id);

        $this->assertEquals('John Doe', $updatedUser->name);
        $this->assertEquals('johndoe@example.com', $updatedUser->email);
        $this->assertNotEquals($createdUser->password, $updatedUser->password);
    }

    /**
     * @test
     * @return void
     */
    public function shouldUpdateUserWithUpdatingRoles()
    {
        /** @var Role */
        $role1 = Role::create(['name' => 'Role 1']);

        /** @var Role */
        $role2 = Role::create(['name' => 'Role 2']);

        /** @var User */
        $createdUser = User::factory()->create();
        $createdUser->assignRole($role1);

        /** @var Permission */
        $permission = Permission::query()
            ->where('name', PermissionEnum::update_user())
            ->first();
        /** @var User */
        $user = User::factory()->create();
        $user->permissions()->sync($permission->id);

        $this->actingAs($user)
            ->put("/users/{$createdUser->id}", [
                'name' => 'John Doe',
                'email' => 'johndoe@example.com',
                'roles' => [
                    $role2->id
                ],
            ]);

        $updatedUser = User::find($createdUser->id);

        $this->assertEquals('John Doe', $updatedUser->name);
        $this->assertEquals('johndoe@example.com', $updatedUser->email);
        $this->assertEquals($createdUser->password, $updatedUser->password);

        $this->assertDatabaseHas('model_has_roles', [
            'model_id' => $createdUser->id,
            'model_type' => User::class,
            'role_id' => $role2->id,
        ]);

        $this->assertDatabaseMissing('model_has_roles', [
            'model_id' => $createdUser->id,
            'model_type' => User::class,
            'role_id' => $role1->id,
        ]);
    }

    /**
     * @dataProvider invalidDataForUpdateUser
     * @param array $data
     * @param array $expectedErrors
     * @return void
     */
    public function shouldFailedToUpdateUserBecauseValidationError(array $data, array $expectedErrors)
    {
        /** @var User */
        $createdUser = User::factory()->create();
        /** @var Permission */
        $permission = Permission::query()
            ->where('name', PermissionEnum::update_user())
            ->first();
        /** @var User */
        $user = User::factory()->create();
        $user->permissions()->sync($permission->id);
        $response = $this->actingAs($user)
            ->put("/users/{$createdUser->id}", $data);

        $response->assertSessionHasErrors($expectedErrors);
    }

    /**
     * @return array
     */
    public function invalidDataForUpdateUser()
    {
        return [
            'Null data' => [
                [],
                [
                    'name',
                    'email',
                ],
            ],
            'name: null, email: null' => [
                [
                    'name' => null,
                    'email' => null,
                ],
                [
                    'name',
                    'email',
                ],
            ],
            'email: not a email, password_confirmation: null' => [
                [
                    'name' => 'John Doe',
                    'email' => 'john doe',
                    'password' => 'secret',
                ],
                [
                    'email',
                    'password',
                ],
            ],
            'email: not a email, password_confirmation: difference with password' => [
                [
                    'name' => 'John Doe',
                    'email' => 'john doe',
                    'password' => 'secret',
                    'password_confirmation' => 'password',
                ],
                [
                    'email',
                    'password',
                ],
            ],
        ];
    }

    /**
     * @test
     * @return void
     */
    public function shouldFailedToUpdateUserBecauseEmailAlreadyTaken()
    {
        /** @var User */
        $previousUser = User::factory()->create();
        /** @var User */
        $createdUser = User::factory()->create();
        /** @var Permission */
        $permission = Permission::query()
            ->where('name', PermissionEnum::update_user())
            ->first();
        /** @var User */
        $user = User::factory()->create();
        $user->permissions()->sync($permission->id);
        $response = $this->actingAs($user)
            ->put("/users/{$createdUser->id}", [
                'name' => 'John Doe',
                'email' => $previousUser->email,
            ]);

        $response->assertSessionHasErrors(['email']);
    }

    /**
     * @test
     * @return void
     */
    public function shouldDeleteUser()
    {
        /** @var User */
        $createdUser = User::factory()->create();
        /** @var Permission */
        $permission = Permission::query()
            ->where('name', PermissionEnum::delete_user())
            ->first();
        /** @var User */
        $user = User::factory()->create();
        $user->permissions()->sync($permission->id);
        $this->actingAs($user)
            ->delete("/users/{$createdUser->id}");

        $this->assertDatabaseMissing('users', [
            'id' => $createdUser->id,
        ]);
    }

    /**
     * @test
     * @return void
     */
    public function shouldFailedToDeleteUserWhenUserNotFound()
    {
        /** @var Permission */
        $permission = Permission::query()
            ->where('name', PermissionEnum::delete_user())
            ->first();
        /** @var User */
        $user = User::factory()->create();
        $user->permissions()->sync($permission->id);
        $response = $this->actingAs($user)
            ->delete('/users/some-id');

        $response->assertStatus(404);
    }
}
