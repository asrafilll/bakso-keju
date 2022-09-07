<?php

namespace Tests\Feature;

use Tests\TestCase;

class AuthFeatureTest extends TestCase
{
    /**
     * @test
     * @return void
     */
    public function shouldShowLoginPage()
    {
        $response = $this->get('/auth/login');

        $response->assertStatus(200);
    }
}
