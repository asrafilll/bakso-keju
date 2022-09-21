<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\ItemCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemCategoryFeatureTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * @return void
     */
    public function shouldShowItemCategoryIndexPage()
    {
        /** @var User */
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/item-categories');

        $response->assertStatus(200);
    }

    /**
     * @test
     * @return void
     */
    public function shouldContainsItemCategoryOnItemCategoryIndexPage()
    {
        /** @var ItemCategory */
        $itemCategory = ItemCategory::factory()->create();;
        /** @var User */
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/item-categories');

        $response->assertSee($itemCategory->name);
    }

    /**
     * @test
     * @return void
     */
    public function shouldShowCreateItemCategoryPage()
    {
        /** @var User */
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/item-categories/create');

        $response->assertStatus(200);
    }

    /**
     * @test
     * @return void
     */
    public function shouldCreateItemCategory()
    {
        /** @var User */
        $user = User::factory()->create();

        $this->actingAs($user)->post('/item-categories', [
            'name' => 'ItemCategory #1',
        ]);

        $this->assertDatabaseHas('item_categories', [
            'name' => 'ItemCategory #1',
        ]);
    }

    /**
     * @test
     * @return void
     */
    public function shouldShowItemCategoryDetailPage()
    {
        /** @var ItemCategory */
        $itemCategory = ItemCategory::factory()->create();;
        /** @var User */
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get("/item-categories/{$itemCategory->id}");

        $response->assertStatus(200);
    }

    /**
     * @test
     * @return void
     */
    public function shouldContainsItemCategoryDataOnItemCategoryDetailPage()
    {
        /** @var ItemCategory */
        $itemCategory = ItemCategory::factory()->create();
        /** @var User */
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get("/item-categories/{$itemCategory->id}");

        $response->assertSee($itemCategory->name);
    }

    /**
     * @test
     * @return void
     */
    public function shouldUpdateItemCategory()
    {
        /** @var ItemCategory */
        $itemCategory = ItemCategory::factory()->create();
        /** @var User */
        $user = User::factory()->create();

        $this->actingAs($user)->put("/item-categories/{$itemCategory->id}", [
            'name' => 'ItemCategory #2',
        ]);

        $this->assertDatabaseHas('item_categories', [
            'id' => $itemCategory->id,
            'name' => 'ItemCategory #2',
        ]);
    }

    /**
     * @test
     * @return void
     */
    public function shouldDeleteItemCategory()
    {
        /** @var ItemCategory */
        $itemCategory = ItemCategory::factory()->create();
        /** @var User */
        $user = User::factory()->create();

        $this->actingAs($user)->delete("/item-categories/{$itemCategory->id}");

        $this->assertDatabaseMissing('item_categories', [
            'id' => $itemCategory->id,
        ]);
    }

    /**
     * @test
     * @return void
     */
    public function shouldDeleteItemCategoryWhichUsedByItems()
    {
        /** @var ItemCategory */
        $itemCategory = ItemCategory::factory()
            ->has(Item::factory())
            ->create();
        /** @var User */
        $user = User::factory()->create();

        $this->actingAs($user)->delete("/item-categories/{$itemCategory->id}");

        $this->assertDatabaseMissing('item_categories', [
            'id' => $itemCategory->id,
        ]);
    }

    /**
     * @test
     * @return void
     */
    public function shouldCreateSubItemCategory()
    {
        /** @var ItemCategory */
        $itemCategory = ItemCategory::factory()->create();
        /** @var User */
        $user = User::factory()->create();

        $this->actingAs($user)->post('/item-categories', [
            'name' => 'Sub ItemCategory #1',
            'parent_id' => $itemCategory->id,
        ]);

        $this->assertDatabaseHas('item_categories', [
            'name' => 'Sub ItemCategory #1',
            'parent_id' => $itemCategory->id,
        ]);
    }

    /**
     * @test
     * @return void
     */
    public function shouldUpdateSubItemCategory()
    {
        /** @var ItemCategory */
        $itemCategory = ItemCategory::factory()->create();
        /** @var ItemCategory */
        $subItemCategory = ItemCategory::factory()
            ->for($itemCategory, 'parentItemCategory')
            ->create();
        /** @var User */
        $user = User::factory()->create();

        $this->actingAs($user)->put('/item-categories/' . $subItemCategory->id, [
            'name' => 'Sub ItemCategory #001',
            'parent_id' => $itemCategory->id,
        ]);

        $this->assertDatabaseHas('item_categories', [
            'name' => 'Sub ItemCategory #001',
            'parent_id' => $itemCategory->id,
        ]);
    }
}
