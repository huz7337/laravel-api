<?php

namespace Tests\Feature;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class RegistrationTest extends TestCase
{

    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_register()
    {
        $data = User::factory()->unverified()->make([
            'terms_and_conditions' => 1
        ]);
        $data->makeVisible('password', 'remember_token');

        $response = $this->post('/api/register', $data->toArray());
        $response->assertOk();
        $response->assertJson(fn (AssertableJson $json) =>
            $json->whereAllType([
                'id' => 'integer',
                'email' => 'string',
                'first_name' => 'string',
                'last_name' => 'string',
                'date_of_birth' => 'string',
                'email_verified_at' => 'string|null',
                'created_at' => 'string',
                'token' => 'string'
            ])->etc()
        );

        $user = User::findByEmail($data['email']);
        $this->assertTrue($user->hasAcceptedTerms());
        $this->assertTrue($user->profile()->exists());
        $this->assertTrue($user->hasRole(User::ROLE_ATHLETE));
        
    }

    public function test_user_too_young()
    {
        $data = User::factory()->unverified()->make([
            'date_of_birth' => Carbon::now()->subYears(12)->format('Y-m-d'),
        ]);
        $data->makeVisible('password', 'remember_token');

        $response = $this->post('/api/register', $data->toArray());
        $response->assertStatus(422);
        $response->assertJsonPath('errors.date_of_birth.0', 'You must be at least 14 years old to create an account.');
    }
}
