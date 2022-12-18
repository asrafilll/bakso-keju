<?php

namespace Database\Seeders;

use App\Enums\PermissionEnum;
use App\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Collection::make(PermissionEnum::toValues())
            ->tap(function () {
                if (!App::environment('testing')) {
                    Schema::disableForeignKeyConstraints();
                    app(PermissionRegistrar::class)->forgetCachedPermissions();
                    Permission::truncate();
                }
            })
            ->each(function ($permission) {
                Permission::create([
                    'name' => $permission,
                ]);
            })
            ->tap(function () {
                if (!App::environment('testing')) {
                    Schema::enableForeignKeyConstraints();
                }
            });
    }
}
