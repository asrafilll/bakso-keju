<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileFeatureTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * @return void
     */
    public function shouldShowProfilePage()
    {
        /** @var User */
        $user = User::factory()->create();
        $response = $this->actingAs($user)
            ->get('/profile');

        $response->assertStatus(200);
    }

    /**
     * @test
     * @return void
     */
    public function shouldContainProfileDataOnProfilePage()
    {
        /** @var User */
        $user = User::factory()->create();
        $response = $this->actingAs($user)
            ->get('/profile');

        $response->assertSee([
            $user->name,
            $user->email,
        ]);
    }
}
