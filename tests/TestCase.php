<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Notification;
use Laravel\Sanctum\Sanctum;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, DatabaseMigrations, DatabaseTransactions;

    protected string $_email = 'laura@digitalkrikits.co.uk';
    protected $_token;
    protected $_headers;

    public function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate');
        $this->seed();
        Notification::fake();

        $user = User::findByEmail($this->_email);
        $this->_token = Sanctum::actingAs($user)->currentAccessToken();
        $this->_headers = ['Authorization' => "Bearer {$this->_token}'"];
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }
}
