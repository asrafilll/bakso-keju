<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(PermissionSeeder::class);

        /** @var User */
        $user = User::firstOrCreate([
            'email' => 'superadmin@example.com',
        ], [
            'name' => 'Super Admin',
            'password' => 'secret',
        ]);
        $permissionIDs = Permission::all()->pluck('id');
        /** @var Role */
        $role = Role::firstOrCreate([
            'name' => 'Super Admin',
        ]);
        $role->permissions()->sync($permissionIDs);
        $user->assignRole($role);
    }
}
