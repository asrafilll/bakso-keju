<?php

namespace Tests\Feature;

use App\Models\Reseller;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ResellerFeatureTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * @return void
     */
    public function shouldShowResellerIndexPage()
    {
        /** @var User */
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/resellers');

        $response->assertStatus(200);
    }

    /**
     * @test
     * @return void
     */
    public function shouldContainsResellerOnResellerIndexPage()
    {
        /** @var Reseller */
        $reseller = Reseller::factory()->create();;
        /** @var User */
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/resellers');

        $response->assertSee([
            $reseller->name,
            $reseller->percentage_discount,
        ]);
    }

    /**
     * @test
     * @return void
     */
    public function shouldShowCreateResellerPage()
    {
        /** @var User */
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/resellers/create');

        $response->assertStatus(200);
    }

    /**
     * @test
     * @return void
     */
    public function shouldCreateReseller()
    {
        /** @var User */
        $user = User::factory()->create();

        $this->actingAs($user)->post('/resellers', [
            'name' => 'Reseller #1',
            'percentage_discount' => 10,
        ]);

        $this->assertDatabaseHas('resellers', [
            'name' => 'Reseller #1',
            'percentage_discount' => 10,
        ]);
    }

    /**
     * @test
     * @return void
     */
    public function shouldShowResellerDetailPage()
    {
        /** @var Reseller */
        $reseller = Reseller::factory()->create();;
        /** @var User */
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get("/resellers/{$reseller->id}");

        $response->assertStatus(200);
    }

    /**
     * @test
     * @return void
     */
    public function shouldContainsResellerDataOnResellerDetailPage()
    {
        /** @var Reseller */
        $reseller = Reseller::factory()->create();
        /** @var User */
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get("/resellers/{$reseller->id}");

        $response->assertSee([
            $reseller->name,
            $reseller->percentage_discount,
        ]);
    }

    /**
     * @test
     * @return void
     */
    public function shouldUpdateReseller()
    {
        /** @var Reseller */
        $reseller = Reseller::factory()->create();
        /** @var User */
        $user = User::factory()->create();

        $this->actingAs($user)->put("/resellers/{$reseller->id}", [
            'name' => 'Reseller #2',
            'percentage_discount' => 10,
        ]);

        $this->assertDatabaseHas('resellers', [
            'id' => $reseller->id,
            'name' => 'Reseller #2',
            'percentage_discount' => 10,
        ]);
    }

    /**
     * @test
     * @return void
     */
    public function shouldDeleteReseller()
    {
        /** @var Reseller */
        $reseller = Reseller::factory()->create();
        /** @var User */
        $user = User::factory()->create();

        $this->actingAs($user)->delete("/resellers/{$reseller->id}");

        $this->assertDatabaseMissing('resellers', [
            'id' => $reseller->id,
        ]);
    }
}
