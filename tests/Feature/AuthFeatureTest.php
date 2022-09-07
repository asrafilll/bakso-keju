<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthFeatureTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var User
     */
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
    public function shouldShowLoginPage()
    {
        $response = $this->get('/auth/login');

        $response->assertStatus(200);
    }

    /**
     * @test
     * @return boolean
     */
    public function shouldLoginUsingValidCredential()
    {
        $this->post('/auth/login', [
            'email' => $this->user->email,
            'password' => 'secret',
        ]);

        $this->assertAuthenticatedAs($this->user);
    }

    /**
     * @test
     * @return void
     */
    public function shouldFailedToLoginUsingInvalidCredential()
    {
        $response = $this->post('/auth/login', [
            'email' => 'johndoe@example.com',
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors(['email']);
    }
}
