<?php

namespace Tests\Feature;

use App\Models\Profile;
use App\Models\Program;
use App\Models\User;
use App\Models\Workout;
use App\Notifications\PasswordChangedEmail;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\Fluent\AssertableJson;
use Illuminate\Testing\TestResponse;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UserTest extends TestCase
{

    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_get_own_profile()
    {
        $this->get('/api/me', $this->_headers)
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) =>
            $json->whereAllType([
                'id' => 'integer',
                'email' => 'string',
                'first_name' => 'string',
                'last_name' => 'string',
                'date_of_birth' => 'string|null',
                'email_verified_at' => 'string|null',
                'created_at' => 'string'
            ])->etc())
            ->assertJsonPath('email', $this->_email);
        ;
    }


    /**
     * Test update profile with metric measurements
     */
    public function test_update_own_profile()
    {
        $data = [
            'first_name' => 'This is',
            'last_name' => 'a test',
            'email' => $this->_email,
            'date_of_birth' => Carbon::now()->subYears('25')->format('Y-m-d'),
            'profile' => Profile::factory()->make()->toArray()
        ];

        $response = $this->post('/api/me', $data, $this->_headers);
        $this->_checkSuccessfulProfileUpdate($response, $data);
    }

    public function test_update_own_profile_us()
    {
        $data = [
            'first_name' => 'This is',
            'last_name' => 'a US test',
            'email' => $this->_email,
            'date_of_birth' => Carbon::now()->subYears('25')->format('Y-m-d'),
            'profile' => Profile::factory()->us()->make()->toArray()
        ];

        $response = $this->post('/api/me', $data, $this->_headers);
        $this->_checkSuccessfulProfileUpdate($response, $data);
    }


    public function test_change_own_password()
    {
        $user = User::findByEmail($this->_email);
        $data = [
            'current_password' => 'Test1234!',
            'password' => 'Test2345!',
            'password_confirmation' => 'Test2345!'
        ];

        $this->post('/api/change-password', $data, $this->_headers)
            ->assertOk()
            ->assertJsonPath('message', 'The password has been changed successfully.');

        Notification::assertSentTo($user, PasswordChangedEmail::class);
    }


    public function test_update_profile_photo()
    {
        Storage::fake('avatars');
        $file = UploadedFile::fake()->image('avatar.jpg');
        $data = ['photo' => $file];
        $this->post('/api/me/photo', $data, $this->_headers)
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) =>
            $json->whereAllType([
                'photo' => 'string'
            ])->etc());

        $user = User::findByEmail($this->_email);
        $this->assertNotEmpty($user->profile->photo);

        // delete the photo from S3 to keep things clean
        if ($user->profile && $user->profile->photo) {
            Storage::disk('s3')->delete($user->profile->photo);
        }
    }


    public function test_invalid_data()
    {
        $data = [
            'first_name' => 'This is',
            'last_name' => 'a test',
            'date_of_birth' => Carbon::now()->subYears('25')->format('Y-m-d'),
            'profile' => Profile::factory()->invalid()->make()->toArray()
        ];

        $this->post('/api/me', $data, $this->_headers)
            ->assertStatus(422);

    }


    /**
     * Assertions for successful profile creation
     *
     * @param TestResponse $response
     * @param array $data
     */
    private function _checkSuccessfulProfileUpdate(TestResponse $response, array $data)
    {
        $response->assertOk()
        ->assertJson(fn (AssertableJson $json) =>
        $json->whereAllType([
            'id' => 'integer',
            'email' => 'string',
            'first_name' => 'string',
            'last_name' => 'string',
            'date_of_birth' => 'string',
            'email_verified_at' => 'string|null',
            'created_at' => 'string'
        ])->etc());

        if (isset($data['email'])) {
            $response->assertJsonPath('email', $data['email']);
        }

        if (isset($data['first_name'])) {
            $response->assertJsonPath('first_name', $data['first_name']);
        }

        if (isset($data['last_name'])) {
            $response->assertJsonPath('last_name', $data['last_name']);
        }

        if (isset($data['date_of_birth'])) {
            $response->assertJsonPath('date_of_birth', $data['date_of_birth']);
        }

        if (isset($data['profile']['weight_kg'])) {
            $response->assertJsonPath('profile.weight_kg', $data['profile']['weight_kg']);
        }

        if (isset($data['profile']['weight_lbs'])) {
            $response->assertJsonPath('profile.weight_lbs', $data['profile']['weight_lbs']);
        }

        if (isset($data['profile']['height_cm'])) {
            $response->assertJsonPath('profile.height_cm', $data['profile']['height_cm']);
        }

        if (isset($data['profile']['height_ft'])) {
            $response->assertJsonPath('profile.height_ft', $data['profile']['height_ft']);
        }

        if (isset($data['profile']['height_in'])) {
            $response->assertJsonPath('profile.height_in', $data['profile']['height_in']);
        }

        if (isset($data['profile']['gender'])) {
            $response->assertJsonPath('profile.gender', $data['profile']['gender']);
        }

        if (isset($data['profile']['preferred_unit'])) {
            $response->assertJsonPath('profile.preferred_unit', $data['profile']['preferred_unit']);
        }
    }


    public function test_get_athlete_profile()
    {
        $athlete = User::role(User::ROLE_ATHLETE)->first();

        $this->get("/api/users/{$athlete->id}")
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) => $json->whereAllType([
                'id' => 'integer',
                'email' => 'string',
                'first_name' => 'string',
                'last_name' => 'string',
                'date_of_birth' => 'string|null',
                'email_verified_at' => 'string|null',
                'created_at' => 'string'
            ])->etc())->assertJsonPath('email', $athlete->email);
    }


    public function test_update_athlete_profile()
    {
        $athlete = User::role(User::ROLE_ATHLETE)->first();

        $data = User::factory()->make()->toArray();
        $data['profile'] = Profile::factory()->make()->toArray();
        $data['date_of_birth'] = Carbon::now()->subYears('25')->format('Y-m-d');

        $response = $this->patch("/api/athletes/{$athlete->id}", $data);
        $this->_checkSuccessfulProfileUpdate($response, $data);
    }


    public function test_update_athlete_photo()
    {
        $athlete = User::role(User::ROLE_ATHLETE)->first();

        Storage::fake('avatars');
        $file = UploadedFile::fake()->image('avatar.jpg');
        $data = ['photo' => $file];

        $this->post("/api/athletes/{$athlete->id}/photo", $data)
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) =>
            $json->whereAllType([
                'photo' => 'string'
            ])->etc());

        $athlete->refresh();
        $this->assertNotEmpty($athlete->profile->photo);

        // delete the photo from S3 to keep things clean
        if ($athlete->profile && $athlete->profile->photo) {
            Storage::disk('s3')->delete($athlete->profile->photo);
        }
    }


    public function test_get_coach_profile()
    {
        $coach = User::role(User::ROLE_COACH)->first();

        $this->get("/api/coaches/{$coach->id}")
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) =>
            $json->whereAllType([
                'id' => 'integer',
                'email' => 'string',
                'first_name' => 'string',
                'last_name' => 'string',
                'date_of_birth' => 'string|null',
                'email_verified_at' => 'string|null',
                'created_at' => 'string'
            ])->etc())
            ->assertJsonPath('email', $coach->email);
        ;
    }


    public function test_update_coach_profile()
    {
        $coach = User::role(User::ROLE_COACH)->first();

        $data = User::factory()->make()->toArray();
        $data['date_of_birth'] = Carbon::now()->subYears('25')->format('Y-m-d');

        $response = $this->patch("/api/coaches/{$coach->id}", $data);
        $this->_checkSuccessfulProfileUpdate($response, $data);
    }


    public function test_update_coach_photo()
    {
        $coach = User::role(User::ROLE_COACH)->first();

        Storage::fake('avatars');
        $file = UploadedFile::fake()->image('avatar.jpg');
        $data = ['photo' => $file];

        $this->post("/api/coaches/{$coach->id}/photo", $data)
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) =>
            $json->whereAllType([
                'photo' => 'string'
            ])->etc());

        $coach->refresh();
        $this->assertNotEmpty($coach->profile->photo);

        // delete the photo from S3 to keep things clean
        if ($coach->profile && $coach->profile->photo) {
            Storage::disk('s3')->delete($coach->profile->photo);
        }
    }


}
