<?php

namespace Tests\Feature;

use App\Models\Group;
use App\Models\Profile;
use App\Models\Program;
use App\Models\User;
use App\Models\Workout;
use App\Notifications\ClientInvitationEmail;
use App\Notifications\NewAccountCreatedEmail;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AthleteTest extends TestCase
{

    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_list_athletes()
    {
        $this->get('/api/athletes', $this->_headers)
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
                'items.0.latest_workout' => 'array|null',
                'items.0.trainer' => 'array|null',
                'items.0.group' => 'array|null'
            ])->etc())
            ->assertJsonPath('currentPage', 1)
            ->assertJsonPath('perPage', 30);
    }


    public function test_search_athletes()
    {
        $athlete = User::role(User::ROLE_ATHLETE)->first();

        // search by the first name of the first athlete
        $params = ['search' => $athlete->first_name . ' ' . $athlete->last_name];
        $this->call('GET', '/api/athletes', $params)
            ->assertOk()
            ->assertJsonPath('total', 1);
    }


    public function test_invite_athlete()
    {
        $athlete = User::factory()->unverified()->make(
            ['email' => 'laura+athlete@digitalkrikits.co.uk']
        );
        $athlete->profile = Profile::factory()->us()->make();
        $data = $athlete->toArray();
        Storage::fake('avatars');
        $file = UploadedFile::fake()->image('avatar.jpg');
        $data['photo'] = $file;

        $this->post('/api/athletes', $data)
            ->assertOk()
            ->assertJsonPath('status', 'Invited');

        $user = User::findByEmail($athlete->email);
        $this->assertTrue($user->profile()->exists());
        $this->assertTrue($user->hasRole(User::ROLE_ATHLETE));
        $this->assertTrue($user->first_name == $athlete->first_name);
        $this->assertTrue($user->last_name == $athlete->last_name);
        $this->assertTrue($user->date_of_birth == $athlete->date_of_birth);
        $this->assertTrue($user->profile->phone_number == $athlete->profile->phone_number);
        $this->assertTrue($user->profile->location == $athlete->profile->location);
        $this->assertTrue($user->profile->weight_lbs == $athlete->profile->weight_lbs);
        $this->assertTrue($user->profile->height_ft == $athlete->profile->height_ft);
        $this->assertTrue($user->profile->height_in == $athlete->profile->height_in);
        $this->assertTrue($user->profile->gender == $athlete->profile->gender);
        $this->assertTrue($user->profile->preferred_unit == $athlete->profile->preferred_unit);
        $this->assertNotEmpty($user->profile->photo);
        $this->assertNotEmpty($user->invitation);
        $this->assertNotEmpty($user->invitation->code);

        // delete the photo from S3 to keep things clean
        if ($user->profile && $user->profile->photo) {
            Storage::disk('s3')->delete($user->profile->photo);
        }

        Notification::assertSentTo($user, ClientInvitationEmail::class);
    }


    public function test_invite_athlete_as_trainer()
    {
        $user = User::findByEmail("tester+trainer1@digitalkrikits.co.uk");
        Sanctum::actingAs($user);

        $athlete = User::factory()->unverified()->make();

        $this->post('/api/athletes', $athlete->toArray())
            ->assertOk()
            ->assertJsonPath('trainer.id', $user->id);
    }


    public function test_validate_invitation()
    {
        // invite an athlete
        $athlete = User::factory()->unverified()->make();

        $this->post('/api/athletes', $athlete->toArray())
            ->assertOk();

        // get the invitation code
        $athleteUser = User::findByEmail($athlete->email);
        $data = ['code' => $athleteUser->invitation->code];

        $this->post('/api/invite/validate', $data)
            ->assertOk()
            ->assertJsonPath('email', $athlete->email);
    }


    public function test_accept_invitation()
    {
        // invite an athlete
        $athlete = User::factory()->unverified()->make();

        $this->post('/api/athletes', $athlete->toArray())
            ->assertOk();

        // get the invitation code
        $athleteUser = User::findByEmail($athlete->email);
        // set password and T&Cs & whatever else is mandatory
        $data = [
            'code' => $athleteUser->invitation->code,
            'email' => $athlete->email,
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
                'date_of_birth' => 'string',
                'email_verified_at' => 'string|null',
                'created_at' => 'string',
                'token' => 'string'
            ])->etc());

        $user = User::findByEmail($data['email']);
        $this->assertTrue($user->hasAcceptedTerms());
        $this->assertTrue($user->profile()->exists());
        $this->assertTrue($user->hasRole(User::ROLE_ATHLETE));
        $this->assertEquals(User::STATUS_INACTIVE, $user->status);

        Notification::assertSentTo($user, NewAccountCreatedEmail::class);
    }


    public function test_assign_trainer()
    {
        // find an athlete
        $athlete = User::findByEmail('tester+athlete1@digitalkrikits.co.uk');
        // find a trainer
        $trainer = User::findByEmail('tester+trainer1@digitalkrikits.co.uk');

        // assign trainer to athlete
        $this->put("/api/athletes/{$athlete->id}/trainers/{$trainer->id}")
            ->assertOk()
            ->assertJsonPath('message', __('Great! You have successfully assigned a trainer to the selected athlete.'));

        // check if the trainer is assigned to the athlete
        $this->assertEquals($athlete->trainer->id, $trainer->id);
    }


    public function test_assign_group()
    {
        // find an athlete
        $athlete = User::findByEmail('tester+athlete1@digitalkrikits.co.uk');
        // find a group
        $group = Group::first();

        // assign athlete to group
        $this->put("/api/athletes/{$athlete->id}/groups/{$group->id}")
            ->assertOk()
            ->assertJsonPath('message', __('Great! You have successfully assigned the selected athlete to the group.'));
    }


    public function test_assign_coach()
    {
        // find an athlete
        $athlete = User::findByEmail('tester+athlete1@digitalkrikits.co.uk');
        // find a coach
        $coach = User::findByEmail('tester+coach1@digitalkrikits.co.uk');

        // assign trainer to athlete
        $this->put("/api/athletes/{$athlete->id}/coaches/{$coach->id}")
            ->assertOk()
            ->assertJsonPath('message', __('Great! You have successfully assigned a coach to the selected athlete.'));

        // check if the trainer is assigned to the athlete
        $this->assertEquals($athlete->coach->id, $coach->id);
    }


    public function test_assign_program()
    {
        // find an athlete
        $athlete = User::findByEmail('tester+athlete1@digitalkrikits.co.uk');
        // create a 21-day program
        $program = Program::factory()->create();

        for ($i = 0; $i < 7; $i++) {
            $workouts = Workout::factory()->count(6)->active()->make()->toArray();
            $workouts[] = Workout::factory()->recovery()->make()->toArray();

            foreach ($workouts as $workout) {
                $this->post("/api/programs/{$program->id}/workouts", $workout)
                    ->assertOk();
            }
        }

        // set the dates
        $data = [
            'start_date' => Carbon::now()->next('Monday'),
            'start_day' => 1,
            'notes' => ''
        ];

        $this->put("/api/athletes/{$athlete->id}/programs/{$program->id}", $data)
            ->assertOk();
    }

}
