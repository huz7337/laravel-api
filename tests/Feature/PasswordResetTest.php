<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\PasswordChangedEmail;
use App\Notifications\ResetPasswordEmail;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{

    use WithFaker;

    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_request_password_reset()
    {
        $user = User::findByEmail($this->_email);
        $response = $this->post('/api/forgot-password', [
            'email' => $this->_email,
        ]);
        $response->assertOk();
        $response->assertJsonPath('message', 'Please check your email account for a link to reset your password.');

        Notification::assertSentTo($user, ResetPasswordEmail::class);
    }

    public function test_change_password()
    {
        $data = ['email' => $this->_email];
        $user = User::findByEmail($this->_email);
        $data['token'] = Password::createToken($user);
        $data['password'] = $data['password_confirmation'] = 'Test1234!';

        $response = $this->post('/api/reset-password', $data);
        $response->assertOk()
            ->assertJsonPath('message', 'The password has been changed successfully.');

        Notification::assertSentTo($user, PasswordChangedEmail::class);
    }
}
