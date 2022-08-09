<?php

namespace Tests\Unit;

use App\Models\Profile;
use App\Models\User;
use Tests\TestCase;

class UserTest extends TestCase
{

    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_user_exists()
    {
        $this->assertTrue(User::where('email', 'laura@digitalkrikits.co.uk')->exists());
    }


    public function test_user_has_accepted_terms()
    {
        /**
         * @var User
         */
        $user = User::factory()->create();
        $user->acceptTerms();
        $this->assertTrue($user->hasAcceptedTerms());
    }


    public function test_user_profile()
    {
        /**
         * @var User
         */
        $user = User::factory()->create();
        $user->profile()->create(Profile::factory()->make()->toArray());
        $this->assertTrue($user->profile()->exists());
    }
}
