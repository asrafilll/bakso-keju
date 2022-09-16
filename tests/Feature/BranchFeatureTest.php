<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BranchFeatureTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * @return void
     */
    public function shouldShowBranchIndexPage()
    {
        /** @var User */
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/branches');

        $response->assertStatus(200);
    }

    /**
     * @test
     * @return void
     */
    public function shouldContainsBranchOnBranchIndexPage()
    {
        /** @var Branch */
        $branch = Branch::factory()->create();;
        /** @var User */
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/branches');

        $response->assertSee([
            $branch->name,
            $branch->order_number_prefix,
            $branch->next_order_number,
        ]);
    }

    /**
     * @test
     * @return void
     */
    public function shouldShowCreateBranchPage()
    {
        /** @var User */
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/branches/create');

        $response->assertStatus(200);
    }

    /**
     * @test
     * @return void
     */
    public function shouldCreateBranch()
    {
        /** @var User */
        $user = User::factory()->create();

        $this->actingAs($user)->post('/branches', [
            'name' => 'Branch #1',
            'order_number_prefix' => 'B',
            'next_order_number' => 1,
        ]);

        $this->assertDatabaseHas('branches', [
            'name' => 'Branch #1',
            'order_number_prefix' => 'B',
            'next_order_number' => 1,
        ]);
    }

    /**
     * @test
     * @return void
     */
    public function shouldShowBranchDetailPage()
    {
        /** @var Branch */
        $branch = Branch::factory()->create();;
        /** @var User */
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get("/branches/{$branch->id}");

        $response->assertStatus(200);
    }

    /**
     * @test
     * @return void
     */
    public function shouldContainsBranchDataOnBranchDetailPage()
    {
        /** @var Branch */
        $branch = Branch::factory()->create();
        /** @var User */
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get("/branches/{$branch->id}");

        $response->assertSee([
            $branch->name,
            $branch->order_number_prefix,
            $branch->next_order_number,
        ]);
    }

    /**
     * @test
     * @return void
     */
    public function shouldUpdateBranch()
    {
        /** @var Branch */
        $branch = Branch::factory()->create();
        /** @var User */
        $user = User::factory()->create();

        $this->actingAs($user)->put("/branches/{$branch->id}", [
            'name' => 'Branch #2',
            'order_number_prefix' => 'B',
            'next_order_number' => 1,
        ]);

        $this->assertDatabaseHas('branches', [
            'id' => $branch->id,
            'name' => 'Branch #2',
            'order_number_prefix' => 'B',
            'next_order_number' => 1,
        ]);
    }

    /**
     * @test
     * @return void
     */
    public function shouldDeleteBranch()
    {
        /** @var Branch */
        $branch = Branch::factory()->create();
        /** @var User */
        $user = User::factory()->create();

        $this->actingAs($user)->delete("/branches/{$branch->id}");

        $this->assertDatabaseMissing('branches', [
            'id' => $branch->id,
        ]);
    }
}
