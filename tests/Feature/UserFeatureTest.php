<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Tests\TestCase;

class UserFeatureTest extends TestCase
{
    use RefreshDatabase;

    /** @var User */
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }


    /**
     * @test
     * @return void
     */
    public function shouldShowUserIndexPage()
    {
        $response = $this->actingAs($this->user)
            ->get('/users');

        $response->assertStatus(200);
    }

    /**
     * @test
     * @return void
     */
    public function shouldContainsUserOnUserIndexPage()
    {
        /** @var Collection<User> */
        $sampleUsers = User::factory(10)->create();
        $sampleUser = $sampleUsers->first();

        $response = $this->actingAs($this->user)
            ->get('/users');

        $response->assertSee($sampleUser->email);
    }

    /**
     * @test
     * @return void
     */
    public function shouldShowCreateUserPage()
    {
        $response = $this->actingAs($this->user)
            ->get('/users/create');

        $response->assertStatus(200);
    }
}
