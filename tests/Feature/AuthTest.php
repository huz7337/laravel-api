<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class AuthTest extends TestCase
{

    use DatabaseMigrations, RefreshDatabase;

    /**
     * Test successful login
     *
     * @return void
     */
    public function test_login()
    {
        $response = $this->post('/api/login', [
            'email' => 'laura@digitalkrikits.co.uk',
            'password' => 'Test1234!'
        ]);

        $response->assertOk();
        $response->assertJson(fn (AssertableJson $json) =>
        $json->whereAllType([
            'id' => 'integer',
            'email' => 'string',
            'first_name' => 'string',
            'last_name' => 'string',
            'email_verified_at' => 'string|null',
            'created_at' => 'string',
            'token' => 'string'
        ])->etc()
        );
    }

    /**
     * Test login with email that isn't registered
     */
    public function test_login_with_inexistent_email()
    {
        $response = $this->post('/api/login', [
            'email' => 'laura@digitalkrikits.com',
            'password' => 'Test1234!'
        ]);
        $response->assertStatus(422);
        $response->assertJsonPath('errors.email.0', 'Invalid credentials');
    }
}
