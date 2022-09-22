<?php

namespace Tests\Feature;

use App\Actions\CreatePurchaseAction;
use App\Models\Branch;
use App\Models\Purchase;
use App\Models\Item;
use App\Models\ItemInventory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class PurchaseFeatureTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * @return void
     */
    public function shouldShowPurchaseIndexPage()
    {
        /** @var User */
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/purchases');

        $response->assertStatus(200);
    }

    /**
     * @test
     * @return void
     */
    public function shouldContainsPurchaseOnPurchaseIndexPage()
    {
        /** @var Branch */
        $branch = Branch::factory()->create();
        /** @var Purchase */
        $purchase = Purchase::factory()
            ->for($branch)
            ->create();
        /** @var User */
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/purchases');

        $response->assertSee([
            $purchase->purchase_number,
            $purchase->created_at,
            $branch->name,
            $purchase->customer_name,
        ]);
    }

    /**
     * @test
     * @return void
     */
    public function shouldShowCreatePurchasePage()
    {
        /** @var User */
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/purchases/create');

        $response->assertStatus(200);
    }

    /**
     * @test
     * @return void
     */
    public function shouldCreatePurchase()
    {
        /** @var Branch */
        $branch = Branch::factory()->create();
        /** @var Item */
        $item = Item::factory()->create();
        /** @var User */
        $user = User::factory()->create();
        $response = $this->actingAs($user)->post('/purchases', [
            'created_at' => '2022-01-01 00:00:00',
            'branch_id' => $branch->id,
            'customer_name' => 'John Doe',
            'line_items' => [
                [
                    'item_id' => $item->id,
                    'quantity' => 2,
                ],
            ],
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('purchases', [
            'created_at' => '2022-01-01 00:00:00',
            'branch_id' => $branch->id,
            'customer_name' => 'John Doe',
            'total_line_items_quantity' => 2,
            'total_line_items_price' => $item->price * 2,
            'total_price' => $item->price * 2,
        ]);

        $this->assertDatabaseHas('purchase_line_items', [
            'item_id' => $item->id,
            'item_name' => $item->name,
            'price' => $item->price,
            'quantity' => 2,
            'total' => $item->price * 2,
        ]);

        $this->assertDatabaseHas('branches', [
            'id' => $branch->id,
            'next_purchase_number' => $branch->next_purchase_number + 1,
        ]);

        $this->assertDatabaseHas('item_inventories', [
            'branch_id' => $branch->id,
            'item_id' => $item->id,
            'quantity' => 2,
        ]);
    }

    /**
     * @test
     * @return void
     */
    public function shouldCreatePurchaseAndIncreaseItemInventoryQuantity()
    {
        /** @var Branch */
        $branch = Branch::factory()->create();
        /** @var Item */
        $item = Item::factory()->create();
        /** @var ItemInventory */
        $itemInventory = ItemInventory::factory()
            ->state([
                'quantity' => 5,
            ])
            ->for($branch)
            ->for($item)
            ->create();
        /** @var User */
        $user = User::factory()->create();
        $response = $this->actingAs($user)->post('/purchases', [
            'created_at' => '2022-01-01 00:00:00',
            'branch_id' => $branch->id,
            'customer_name' => 'John Doe',
            'line_items' => [
                [
                    'item_id' => $item->id,
                    'quantity' => 2,
                ],
            ],
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('item_inventories', [
            'id' => $itemInventory->id,
            'quantity' => 7,
        ]);
    }

    /**
     * @test
     * @return void
     */
    public function shouldShowPurchaseDetailPage()
    {
        /** @var Branch */
        $branch = Branch::factory()->create();
        /** @var Purchase */
        $purchase = Purchase::factory()
            ->for($branch)
            ->create();
        /** @var User */
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get("/purchases/{$purchase->id}");

        $response->assertStatus(200);
    }

    /**
     * @test
     * @return void
     */
    public function shouldContainsPurchaseDataWhenShowPurchaseDetailPage()
    {
        /** @var Branch */
        $branch = Branch::factory()->create();
        /** @var Purchase */
        $purchase = Purchase::factory()
            ->for($branch)
            ->create();
        /** @var User */
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get("/purchases/{$purchase->id}");

        $response->assertSee([
            $purchase->purchase_number,
            $branch->name,
            $purchase->customer_name,
        ]);
    }

    /**
     * @test
     * @return void
     */
    public function shouldDeletePurchase()
    {
        /** @var Branch */
        $branch = Branch::factory()->create();
        /** @var Item */
        $item = Item::factory()->create();
        /** @var ItemInventory */
        $itemInventory = ItemInventory::factory()
            ->state([
                'quantity' => 5,
            ])
            ->for($branch)
            ->for($item)
            ->create();
        $data = [
            'created_at' => '2022-01-01 00:00:00',
            'branch_id' => $branch->id,
            'customer_name' => 'John Doe',
            'line_items' => [
                [
                    'item_id' => $item->id,
                    'quantity' => 2,
                ],
            ],
        ];
        $purchase = resolve(CreatePurchaseAction::class)->execute($data);
        /** @var User */
        $user = User::factory()->create();
        $response = $this->actingAs($user)->delete("/purchases/{$purchase->id}");
        $response->assertSessionHas(['success']);

        $this->assertDatabaseHas('purchases', [
            'id' => $purchase->id,
            'deleted_at' => Carbon::now(),
        ]);

        $this->assertDatabaseHas('item_inventories', [
            'id' => $itemInventory->id,
            'quantity' => 5,
        ]);
    }
}
