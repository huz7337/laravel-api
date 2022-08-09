<?php

namespace Tests\Feature;

use App\Models\Group;
use App\Models\Profile;
use App\Models\User;
use App\Notifications\ClientInvitationEmail;
use App\Notifications\NewAccountCreatedEmail;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CoachTest extends TestCase
{

    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_list_coaches()
    {
        $this->get('/api/coaches', $this->_headers)
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) =>
            $json->whereAllType([
                'currentPage' => 'integer',
                'perPage' => 'integer',
                'lastPage' => 'integer',
                'total' => 'integer',
                'items' => 'array',
                'items.0.id' => 'integer',
                'items.0.first_name' => 'string',
                'items.0.last_name' => 'string',
                'items.0.status' => 'string',
                'items.0.program' => 'array|null',
                'items.0.number_of_athletes' => 'integer',
                'items.0.trainer' => 'array|null',
                'items.0.group' => 'array|null'
            ])->etc())
            ->assertJsonPath('currentPage', 1)
            ->assertJsonPath('perPage', 30);
    }


    public function test_search_coaches()
    {
        $athlete = User::role(User::ROLE_COACH)->first();

        // search by the first name of the first athlete
        $params = ['search' => $athlete->first_name . ' ' . $athlete->last_name];
        $this->call('GET', '/api/coaches', $params)
            ->assertOk()
            ->assertJsonPath('total', 1);
    }


    public function test_invite_coach()
    {
        $coach = User::factory()->unverified()->make(
            ['email' => 'laura+coach@digitalkrikits.co.uk']
        );

        $this->post('/api/coaches', $coach->toArray())
            ->assertOk()
            ->assertJsonPath('status', 'Invited');

        $user = User::findByEmail($coach->email);
        $this->assertTrue($user->profile()->exists());
        $this->assertTrue($user->hasRole(User::ROLE_COACH));
        $this->assertTrue($user->first_name == $coach->first_name);
        $this->assertTrue($user->last_name == $coach->last_name);
        $this->assertNotEmpty($user->invitation);
        $this->assertNotEmpty($user->invitation->code);

        Notification::assertSentTo($user, ClientInvitationEmail::class);
    }


    public function test_invite_coach_as_trainer()
    {
        $user = User::findByEmail("tester+trainer1@digitalkrikits.co.uk");
        Sanctum::actingAs($user);

        $coach = User::factory()->unverified()->make();

        $this->post('/api/coaches', $coach->toArray())
            ->assertOk()
            ->assertJsonPath('trainer.id', $user->id);
    }


    public function test_validate_invitation()
    {
        // invite a coach
        $coach = User::factory()->unverified()->make();

        $this->post('/api/coaches', $coach->toArray())
            ->assertOk();

        // get the invitation code
        $user = User::findByEmail($coach->email);
        $data = ['code' => $user->invitation->code];

        $this->post('/api/invite/validate', $data)
            ->assertOk()
            ->assertJsonPath('email', $coach->email);
    }


    public function test_accept_invitation()
    {
        // invite an athlete
        $coach = User::factory()->unverified()->make();

        $this->post('/api/coaches', $coach->toArray())
            ->assertOk();

        // get the invitation code
        $user = User::findByEmail($coach->email);
        // set password and T&Cs & whatever else is mandatory
        $data = [
            'code' => $user->invitation->code,
            'email' => $coach->email,
            'date_of_birth' => '2000-08-25',
            'password' => 'Test1234!',
            'terms_and_conditions' => 1
        ];

        $this->post('/api/invite/accept', $data)
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) =>
            $json->whereAllType([
                'id' => 'integer',
                'email' => 'string',
                'first_name' => 'string',
                'last_name' => 'string',
                'date_of_birth' => 'string|null',
                'email_verified_at' => 'string|null',
                'created_at' => 'string',
                'token' => 'string'
            ])->etc());

        $user = User::findByEmail($data['email']);
        $this->assertTrue($user->hasAcceptedTerms());
        $this->assertTrue($user->profile()->exists());
        $this->assertTrue($user->hasRole(User::ROLE_COACH));
        $this->assertEquals(User::STATUS_INACTIVE, $user->status);

        Notification::assertSentTo($user, NewAccountCreatedEmail::class);
    }


    public function test_assign_trainer()
    {
        // find a coach
        $coach = User::findByEmail('tester+coach1@digitalkrikits.co.uk');
        // find a trainer
        $trainer = User::findByEmail('tester+trainer2@digitalkrikits.co.uk');

        // assign trainer to coach
        $this->put("/api/coaches/{$coach->id}/trainers/{$trainer->id}")
            ->assertOk()
            ->assertJsonPath('message', __('Great! You have successfully assigned a trainer to the selected coach.'));

        // check if the trainer is assigned to the athlete
        $this->assertEquals($coach->trainer->id, $trainer->id);
    }


    public function test_assign_group()
    {
        // find a coach
        $coach = User::findByEmail('tester+coach1@digitalkrikits.co.uk');
        // find a group
        $group = Group::first();

        // assign coach to group
        $this->put("/api/coaches/{$coach->id}/groups/{$group->id}")
            ->assertOk()
            ->assertJsonPath('message', __('Great! You have successfully assigned the selected coach to the group.'));
    }

    public function test_get_athletes() {
        // find a coach
        $coach = User::role(User::ROLE_COACH)->first();

        // get the list of athletes
        $this->get("/api/coaches/{$coach->id}/athletes")
            ->assertOk();
    }

    public function test_get_athletes_programs() {
        $coach = User::role(User::ROLE_COACH)->first();
        $fakeCoachId = 0;

        $this->get("/api/coaches/{$coach->id}/athletes/programs")->assertOk();

        $this->get("/api/coaches/{$fakeCoachId}/athletes/programs")->assertNotFound();
    }
}
