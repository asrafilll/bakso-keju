<?php

namespace Database\Seeders;

use App\Enums\PermissionEnum;
use App\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

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
                Schema::disableForeignKeyConstraints();
                Permission::truncate();
            })
            ->each(function ($permission) {
                Permission::create([
                    'name' => $permission,
                ]);
            })
            ->tap(function () {
                Schema::enableForeignKeyConstraints();
            });
    }
}
