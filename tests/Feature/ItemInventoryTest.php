<?php

namespace Tests\Feature;

use App\Enums\PermissionEnum;
use App\Models\Branch;
use App\Models\Item;
use App\Models\ItemInventory;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemInventoryTest extends TestCase
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
    public function shouldShowItemInventoryIndexPage()
    {
        /** @var Permission */
        $permission = Permission::query()
            ->where('name', PermissionEnum::view_item_inventories())
            ->first();
        /** @var User */
        $user = User::factory()->create();
        $user->permissions()->sync($permission->id);
        $response = $this->actingAs($user)->get('/item-inventories');

        $response->assertStatus(200);
    }

    /**
     * @test
     * @return void
     */
    public function shouldContainsItemInventoryOnItemInventoryIndexPage()
    {
        /** @var Branch */
        $branch = Branch::factory()->create();
        /** @var Item */
        $item = Item::factory()->create();
        /** @var ItemInventory */
        $itemInventory = ItemInventory::factory()
            ->for($branch)
            ->for($item)
            ->create();
        /** @var Permission */
        $permission = Permission::query()
            ->where('name', PermissionEnum::view_item_inventories())
            ->first();
        /** @var User */
        $user = User::factory()->create();
        $user->permissions()->sync($permission->id);
        $response = $this->actingAs($user)->get('/item-inventories');

        $response->assertSee([
            $branch->name,
            $item->name,
            $itemInventory->quantity,
        ]);
    }
}
