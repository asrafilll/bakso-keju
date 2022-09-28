<?php

namespace Tests\Feature;

use App\Enums\PermissionEnum;
use App\Models\Branch;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BranchFeatureTest extends TestCase
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
    public function shouldShowBranchIndexPage()
    {
        /** @var Permission */
        $permission = Permission::query()
            ->where('name', PermissionEnum::view_branches())
            ->first();
        /** @var User */
        $user = User::factory()->create();
        $user->permissions()->sync($permission->id);
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
        /** @var Permission */
        $permission = Permission::query()
            ->where('name', PermissionEnum::view_branches())
            ->first();
        /** @var User */
        $user = User::factory()->create();
        $user->permissions()->sync($permission->id);
        $response = $this->actingAs($user)->get('/branches');

        $response->assertSee([
            $branch->name,
            $branch->phone,
            $branch->order_number_prefix,
            $branch->next_order_number,
            $branch->purchase_number_prefix,
            $branch->next_purchase_number,
        ]);
    }

    /**
     * @test
     * @return void
     */
    public function shouldShowCreateBranchPage()
    {
        /** @var Permission */
        $permission = Permission::query()
            ->where('name', PermissionEnum::create_branch())
            ->first();
        /** @var User */
        $user = User::factory()->create();
        $user->permissions()->sync($permission->id);
        $response = $this->actingAs($user)->get('/branches/create');

        $response->assertStatus(200);
    }

    /**
     * @test
     * @return void
     */
    public function shouldCreateBranch()
    {
        /** @var Permission */
        $permission = Permission::query()
            ->where('name', PermissionEnum::create_branch())
            ->first();
        /** @var User */
        $user = User::factory()->create();
        $user->permissions()->sync($permission->id);

        $this->actingAs($user)->post('/branches', [
            'name' => 'Branch #1',
            'phone' => '111222333444',
            'order_number_prefix' => 'B',
            'next_order_number' => 1,
            'purchase_number_prefix' => 'B',
            'next_purchase_number' => 1,
        ]);

        $this->assertDatabaseHas('branches', [
            'name' => 'Branch #1',
            'phone' => '111222333444',
            'order_number_prefix' => 'B',
            'next_order_number' => 1,
            'purchase_number_prefix' => 'B',
            'next_purchase_number' => 1,
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
        /** @var Permission */
        $permission = Permission::query()
            ->where('name', PermissionEnum::view_branches())
            ->first();
        /** @var User */
        $user = User::factory()->create();
        $user->permissions()->sync($permission->id);
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
        /** @var Permission */
        $permission = Permission::query()
            ->where('name', PermissionEnum::view_branches())
            ->first();
        /** @var User */
        $user = User::factory()->create();
        $user->permissions()->sync($permission->id);
        $response = $this->actingAs($user)->get("/branches/{$branch->id}");

        $response->assertSee([
            $branch->name,
            $branch->phone,
            $branch->order_number_prefix,
            $branch->next_order_number,
            $branch->purchase_number_prefix,
            $branch->next_purchase_number,
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
        /** @var Permission */
        $permission = Permission::query()
            ->where('name', PermissionEnum::update_branch())
            ->first();
        /** @var User */
        $user = User::factory()->create();
        $user->permissions()->sync($permission->id);
        $this->actingAs($user)->put("/branches/{$branch->id}", [
            'name' => 'Branch #2',
            'phone' => '111222333444',
            'order_number_prefix' => 'B',
            'next_order_number' => 2,
            'purchase_number_prefix' => 'P',
            'next_purchase_number' => 2,
        ]);

        $this->assertDatabaseHas('branches', [
            'id' => $branch->id,
            'phone' => '111222333444',
            'name' => 'Branch #2',
            'order_number_prefix' => 'B',
            'next_order_number' => 2,
            'purchase_number_prefix' => 'P',
            'next_purchase_number' => 2,
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
        /** @var Permission */
        $permission = Permission::query()
            ->where('name', PermissionEnum::delete_branch())
            ->first();
        /** @var User */
        $user = User::factory()->create();
        $user->permissions()->sync($permission->id);
        $this->actingAs($user)->delete("/branches/{$branch->id}");

        $this->assertDatabaseMissing('branches', [
            'id' => $branch->id,
        ]);
    }
}
