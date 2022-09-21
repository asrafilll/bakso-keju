<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Item;
use App\Models\ItemInventory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemInventoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * @return void
     */
    public function shouldShowItemInventoryIndexPage()
    {
        /** @var User */
        $user = User::factory()->create();

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
        /** @var User */
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/item-inventories');

        $response->assertSee([
            $branch->name,
            $item->name,
            $itemInventory->quantity,
        ]);
    }
}
