<?php

namespace Tests\Feature;

use App\Enums\PermissionEnum;
use App\Models\Branch;
use App\Models\Item;
use App\Models\ItemCategory;
use App\Models\ItemInventory;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemFeatureTest extends TestCase
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
    public function shouldShowItemIndexPage()
    {
        /** @var Permission */
        $permission = Permission::query()
            ->where('name', PermissionEnum::view_items())
            ->first();
        /** @var User */
        $user = User::factory()->create();
        $user->permissions()->sync($permission->id);
        $response = $this->actingAs($user)->get('/items');

        $response->assertStatus(200);
    }

    /**
     * @test
     * @return void
     */
    public function shouldContainsItemDataOnItemIndexPage()
    {
        /** @var Collection<Item> */
        $items = Item::factory(3)->create();
        /** @var Item */
        $sampleItem = $items->first();
        /** @var Permission */
        $permission = Permission::query()
            ->where('name', PermissionEnum::view_items())
            ->first();
        /** @var User */
        $user = User::factory()->create();
        $user->permissions()->sync($permission->id);
        $response = $this->actingAs($user)->get('/items');

        $response->assertSee($sampleItem->name);
    }

    /**
     * @test
     * @return void
     */
    public function shouldShowItemCreatePage()
    {
        /** @var Permission */
        $permission = Permission::query()
            ->where('name', PermissionEnum::create_item())
            ->first();
        /** @var User */
        $user = User::factory()->create();
        $user->permissions()->sync($permission->id);
        $response = $this->actingAs($user)->get('/items/create');

        $response->assertStatus(200);
        $response->assertViewHas('itemCategories');
    }

    /**
     * @test
     * @return void
     */
    public function shouldCreateItem()
    {
        /** @var Permission */
        $permission = Permission::query()
            ->where('name', PermissionEnum::create_item())
            ->first();
        /** @var User */
        $user = User::factory()->create();
        $user->permissions()->sync($permission->id);
        /** @var ItemCategory */
        $itemCategory = ItemCategory::factory()
            ->for(Item::factory(), 'parentItemCategory')
            ->create();
        $this->actingAs($user)->post('/items', [
            'name' => 'Item 1',
            'price' => 10000,
            'item_category_id' => $itemCategory->id,
        ]);

        $this->assertDatabaseHas('items', [
            'name' => 'Item 1',
            'price' => 10000,
            'item_category_id' => $itemCategory->id,
        ]);
    }

    /**
     * @test
     * @return void
     */
    public function shouldShowItemDetailPage()
    {
        /** @var ItemCategory */
        $itemCategory = ItemCategory::factory()
            ->for(Item::factory(), 'parentItemCategory')
            ->create();
        /** @var Item */
        $item = Item::factory()
            ->for($itemCategory)
            ->create();
        /** @var Permission */
        $permission = Permission::query()
            ->where('name', PermissionEnum::view_items())
            ->first();
        /** @var User */
        $user = User::factory()->create();
        $user->permissions()->sync($permission->id);
        $response = $this->actingAs($user)->get("/items/{$item->id}");

        $response->assertSee([
            $item->name,
            $item->price,
            $itemCategory->id,
        ]);
        $response->assertViewHas('itemCategories');
    }

    /**
     * @test
     * @return void
     */
    public function shouldUpdateItem()
    {
        /** @var ItemCategory */
        $itemCategory = ItemCategory::factory()
            ->for(Item::factory(), 'parentItemCategory')
            ->create();
        /** @var Item */
        $item = Item::factory()
            ->state([
                'item_category_id' => $itemCategory->id,
            ])
            ->create();
        /** @var Permission */
        $permission = Permission::query()
            ->where('name', PermissionEnum::update_item())
            ->first();
        /** @var User */
        $user = User::factory()->create();
        $user->permissions()->sync($permission->id);
        $this->actingAs($user)->put("/items/{$item->id}", [
            'name' => 'Item #1',
            'price' => 10000,
            'item_category_id' => $itemCategory->id,
        ]);

        $this->assertDatabaseHas('items', [
            'name' => 'Item #1',
            'price' => 10000,
            'item_category_id' => $itemCategory->id,
        ]);
    }

    /**
     * @test
     * @return void
     */
    public function shouldDeleteItem()
    {
        /** @var Item */
        $item = Item::factory()->create();
        /** @var Permission */
        $permission = Permission::query()
            ->where('name', PermissionEnum::delete_item())
            ->first();
        /** @var User */
        $user = User::factory()->create();
        $user->permissions()->sync($permission->id);
        $this->actingAs($user)->delete("/items/{$item->id}");

        $this->assertDatabaseMissing('items', [
            'id' => $item->id,
        ]);
    }

    /**
     * @test
     * @return void
     */
    public function shouldShowItemDetailPageWithItemInventoryData()
    {
        /** @var Branch */
        $branch = Branch::factory()->create();
        /** @var ItemCategory */
        $itemCategory = ItemCategory::factory()
            ->for(Item::factory(), 'parentItemCategory')
            ->create();
        /** @var Item */
        $item = Item::factory()
            ->for($itemCategory)
            ->has(
                ItemInventory::factory()
                    ->state([
                        'quantity' => 10,
                    ])
                    ->for($branch)
            )
            ->create();
        /** @var Permission */
        $permission = Permission::query()
            ->where('name', PermissionEnum::view_items())
            ->first();
        /** @var User */
        $user = User::factory()->create();
        $user->permissions()->sync($permission->id);
        $response = $this->actingAs($user)->get("/items/{$item->id}");

        $response->assertSee([
            $item->name,
            $item->price,
            $itemCategory->id,
            $branch->name,
            10,
        ]);
        $response->assertViewHas('itemCategories');
    }

    /**
     * @test
     * @return void
     */
    public function shouldFailedToDeleteItemWhenAlreadyHaveItemInventories()
    {
        /** @var Branch */
        $branch = Branch::factory()->create();
        /** @var ItemCategory */
        $itemCategory = ItemCategory::factory()
            ->for(Item::factory(), 'parentItemCategory')
            ->create();
        /** @var Item */
        $item = Item::factory()
            ->for($itemCategory)
            ->has(
                ItemInventory::factory()
                    ->state([
                        'quantity' => 10,
                    ])
                    ->for($branch)
            )
            ->create();
        /** @var Permission */
        $permission = Permission::query()
            ->where('name', PermissionEnum::delete_item())
            ->first();
        /** @var User */
        $user = User::factory()->create();
        $user->permissions()->sync($permission->id);
        $response = $this->actingAs($user)->delete("/items/{$item->id}");

        $response->assertSessionHas('failed');
    }
}
