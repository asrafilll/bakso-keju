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
            'is_main' => true,
        ]);

        $this->assertDatabaseHas('branches', [
            'name' => 'Branch #1',
            'phone' => '111222333444',
            'order_number_prefix' => 'B',
            'next_order_number' => 1,
            'purchase_number_prefix' => 'B',
            'next_purchase_number' => 1,
            'is_main' => true,
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
        $branch = Branch::factory()->main()->create();
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
            'is_main' => false,
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

    /**
     * @test
     * @return void
     */
    public function shouldUnsetOtherBranchesIsMainToFalseWhenCreateBranchWithIsMainIsTrue()
    {
        /** @var Permission */
        $permission = Permission::query()
            ->where('name', PermissionEnum::create_branch())
            ->first();
        /** @var User */
        $user = User::factory()->create();
        $user->permissions()->sync($permission->id);
        /** @var Branch */
        $existingBranch = Branch::factory()
            ->main()
            ->create();

        $this->actingAs($user)->post('/branches', [
            'name' => 'Main Branch #1',
            'phone' => '111222333444',
            'order_number_prefix' => 'B',
            'next_order_number' => 1,
            'purchase_number_prefix' => 'B',
            'next_purchase_number' => 1,
            'is_main' => true,
        ]);

        $this->assertDatabaseHas('branches', [
            'name' => 'Main Branch #1',
            'phone' => '111222333444',
            'order_number_prefix' => 'B',
            'next_order_number' => 1,
            'purchase_number_prefix' => 'B',
            'next_purchase_number' => 1,
            'is_main' => true,
        ]);

        $this->assertDatabaseHas('branches', [
            'id' => $existingBranch->id,
            'is_main' => false,
        ]);
    }

    /**
     * @test
     * @return void
     */
    public function shouldUnsetOtherBranchesIsMainToFalseWhenUpdateBranchWithIsMainIsTrue()
    {
        /** @var Permission */
        $permission = Permission::query()
            ->where('name', PermissionEnum::update_branch())
            ->first();
        /** @var User */
        $user = User::factory()->create();
        $user->permissions()->sync($permission->id);
        /** @var Branch */
        $existingBranch = Branch::factory()
            ->main()
            ->create();
        /** @var Branch */
        $branch = Branch::factory()->create();

        $this->actingAs($user)->put("/branches/{$branch->id}", [
            'name' => 'Main Branch #1',
            'phone' => '111222333444',
            'order_number_prefix' => 'B',
            'next_order_number' => 1,
            'purchase_number_prefix' => 'B',
            'next_purchase_number' => 1,
            'is_main' => true,
        ]);

        $this->assertDatabaseHas('branches', [
            'id' => $branch->id,
            'name' => 'Main Branch #1',
            'phone' => '111222333444',
            'order_number_prefix' => 'B',
            'next_order_number' => 1,
            'purchase_number_prefix' => 'B',
            'next_purchase_number' => 1,
            'is_main' => true,
        ]);

        $this->assertDatabaseHas('branches', [
            'id' => $existingBranch->id,
            'is_main' => false,
        ]);
    }

    /**
     * @test
     * @return void
     */
    public function shouldCreateBranchWithUsers()
    {
        /** @var Permission */
        $permission = Permission::query()
            ->where('name', PermissionEnum::create_branch())
            ->first();
        /** @var User */
        $user = User::factory()->create();
        $user->permissions()->sync($permission->id);
        /** @var User */
        $user1 = User::factory()->create();

        $this->actingAs($user)->post('/branches', [
            'name' => 'Branch #1',
            'phone' => '111222333444',
            'order_number_prefix' => 'B',
            'next_order_number' => 1,
            'purchase_number_prefix' => 'B',
            'next_purchase_number' => 1,
            'is_main' => true,
            'user_ids' => [$user1->id],
        ]);

        $branch = Branch::query()
            ->where('name', 'Branch #1')
            ->first();

        $this->assertDatabaseHas('branches', [
            'name' => 'Branch #1',
            'phone' => '111222333444',
            'order_number_prefix' => 'B',
            'next_order_number' => 1,
            'purchase_number_prefix' => 'B',
            'next_purchase_number' => 1,
            'is_main' => true,
        ]);

        $this->assertDatabaseHas('branch_users', [
            'branch_id' => $branch->id,
            'user_id' => $user1->id,
        ]);
    }

    /**
     * @test
     * @return void
     */
    public function shouldUpdateBranchWithUsers()
    {
        /** @var Branch */
        $branch = Branch::factory()
            ->main()
            ->create();
        /** @var User */
        $user1 = User::factory()->create();
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
            'user_ids' => [$user1->id],
        ]);

        $this->assertDatabaseHas('branches', [
            'id' => $branch->id,
            'phone' => '111222333444',
            'name' => 'Branch #2',
            'order_number_prefix' => 'B',
            'next_order_number' => 2,
            'purchase_number_prefix' => 'P',
            'next_purchase_number' => 2,
            'is_main' => false,
        ]);

        $this->assertDatabaseHas('branch_users', [
            'branch_id' => $branch->id,
            'user_id' => $user1->id,
        ]);
    }
}
