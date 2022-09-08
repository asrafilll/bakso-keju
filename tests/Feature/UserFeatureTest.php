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

    /**
     * @test
     * @return void
     */
    public function shouldCreateUser()
    {
        $this->actingAs($this->user)
            ->post('/users', [
                'name' => 'John Doe',
                'email' => 'johndoe@example.com',
                'password' => 'secret',
                'password_confirmation' => 'secret',
            ]);

        $this->assertDatabaseHas('users', [
            'name' => 'John Doe',
            'email' => 'johndoe@example.com',
        ]);
    }

    public function invalidDataForCreateUser(): array
    {
        return [
            'Null data' => [
                [],
                [
                    'name',
                    'email',
                    'password',
                ],
            ],
            'name: null, email: null, password: null, password_confirmation: null' => [
                [
                    'name' => null,
                    'email' => null,
                    'password' => null,
                    'password_confirmation' => null,
                ],
                [
                    'name',
                    'email',
                    'password',
                ],
            ],
            'password_confirmation: null' => [
                [
                    'name' => 'John Doe',
                    'email' => 'johndoe@example.com',
                    'password' => 'secret',
                ],
                [
                    'password',
                ],
            ],
            'email: not a email, password_confirmation: difference with password' => [
                [
                    'name' => 'John Doe',
                    'email' => 'john doe',
                    'password' => 'secret',
                    'password_confirmation' => 'password',
                ],
                [
                    'email',
                    'password',
                ],
            ],
        ];
    }

    /**
     * @test
     * @dataProvider invalidDataForCreateUser
     * @param array $data
     * @param array $expectedErrors
     * @return void
     */
    public function shouldFailedToCreateUserBecauseValidationError($data, $expectedErrors)
    {
        $response = $this->actingAs($this->user)
            ->post('/users', $data);

        $response->assertSessionHasErrors($expectedErrors);
    }

    /**
     * @test
     * @return void
     */
    public function shouldShowUserDetailPage()
    {
        /** @var User */
        $user = User::factory()->create();

        $response = $this->actingAs($this->user)
            ->get("/users/{$user->id}");

        $response->assertStatus(200);
    }

    /**
     * @test
     * @return void
     */
    public function shouldContainsUserDataOnUserDetailPage()
    {
        /** @var User */
        $user = User::factory()->create();

        $response = $this->actingAs($this->user)
            ->get("/users/{$user->id}");

        $response->assertSee($user->email);
        $response->assertSee($user->name);
    }

    /**
     * @test
     * @return void
     */
    public function shouldErrorShowUserDetailPageWhenUserNotFound()
    {
        $response = $this->actingAs($this->user)
            ->get('/users/some-user-id');

        $response->assertStatus(404);
    }

    /**
     * @test
     * @return void
     */
    public function shouldUpdateUserWithoutUpdatingPassword()
    {
        /** @var User */
        $user = User::factory()->create();

        $this->actingAs($this->user)
            ->put("/users/{$user->id}", [
                'name' => 'John Doe',
                'email' => 'johndoe@example.com',
            ]);

        $updatedUser = User::find($user->id);

        $this->assertEquals('John Doe', $updatedUser->name);
        $this->assertEquals('johndoe@example.com', $updatedUser->email);
        $this->assertEquals($user->password, $updatedUser->password);
    }

    /**
     * @test
     * @return void
     */
    public function shouldUpdateUserWithUpdatingPassword()
    {
        /** @var User */
        $user = User::factory()->create();

        $this->actingAs($this->user)
            ->put("/users/{$user->id}", [
                'name' => 'John Doe',
                'email' => 'johndoe@example.com',
                'password' => 'secret',
                'password_confirmation' => 'secret',
            ]);

        $updatedUser = User::find($user->id);

        $this->assertEquals('John Doe', $updatedUser->name);
        $this->assertEquals('johndoe@example.com', $updatedUser->email);
        $this->assertNotEquals($user->password, $updatedUser->password);
    }

    /**
     * @dataProvider invalidDataForUpdateUser
     * @param array $data
     * @param array $expectedErrors
     * @return void
     */
    public function shouldFailedToUpdateUserBecauseValidationError(array $data, array $expectedErrors)
    {
        /** @var User */
        $user = User::factory()->create();

        $response = $this->actingAs($this->user)
            ->put("/users/{$user->id}", $data);

        $response->assertSessionHasErrors($expectedErrors);
    }

    /**
     * @return array
     */
    public function invalidDataForUpdateUser()
    {
        return [
            'Null data' => [
                [],
                [
                    'name',
                    'email',
                ],
            ],
            'name: null, email: null' => [
                [
                    'name' => null,
                    'email' => null,
                ],
                [
                    'name',
                    'email',
                ],
            ],
            'email: not a email, password_confirmation: null' => [
                [
                    'name' => 'John Doe',
                    'email' => 'john doe',
                    'password' => 'secret',
                ],
                [
                    'email',
                    'password',
                ],
            ],
            'email: not a email, password_confirmation: difference with password' => [
                [
                    'name' => 'John Doe',
                    'email' => 'john doe',
                    'password' => 'secret',
                    'password_confirmation' => 'password',
                ],
                [
                    'email',
                    'password',
                ],
            ],
        ];
    }

    /**
     * @test
     * @return void
     */
    public function shouldFailedToUpdateUserBecauseEmailAlreadyTaken()
    {
        /** @var User */
        $previousUser = User::factory()->create();
        /** @var User */
        $user = User::factory()->create();

        $response = $this->actingAs($this->user)
            ->put("/users/{$user->id}", [
                'name' => 'John Doe',
                'email' => $previousUser->email,
            ]);

        $response->assertSessionHasErrors(['email']);
    }
}
