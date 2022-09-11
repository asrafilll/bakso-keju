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

    /**
     * @test
     * @return void
     */
    public function shouldUpdateProfileData()
    {
        /** @var User */
        $user = User::factory()->create();
        $this->actingAs($user)
            ->put('/profile', [
                'name' => 'John Doe',
                'email' => 'johndoe@example.com',
            ]);
        $user->refresh();

        $this->assertEquals('John Doe', $user->name);
        $this->assertEquals('johndoe@example.com', $user->email);
    }

    /**
     * @dataProvider invalidDataForUpdateProfileData
     * @param array $data
     * @param array $expectedErrors
     * @return void
     */
    public function shouldFailedToUpdateProfileDataUsingInvalidData(array $data, array $expectedErrors)
    {
        /** @var User */
        $user = User::factory()->create();
        $response = $this->actingAs($user)
            ->put('/profile', $data);

        $response->assertSessionHasErrors($expectedErrors);
    }

    /**
     * @return array
     */
    public function invalidDataForUpdateProfileData()
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
            'email: not a email' => [
                [
                    'name' => 'John Doe',
                    'email' => 'john doe',
                ],
                [
                    'email',
                ],
            ],
        ];
    }

    /**
     * @test
     * @return void
     */
    public function shouldFailedToUpdateProfileDataWhenEmailAlreadyTaken()
    {
        /** @var User */
        $previousUser = User::factory()->create();
        /** @var User */
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->put('/profile', [
                'name' => 'John Doe',
                'email' => $previousUser->email,
            ]);

        $response->assertSessionHasErrors(['email']);
    }
}
