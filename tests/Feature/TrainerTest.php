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

class TrainerTest extends TestCase
{

    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_list_coaches()
    {
        $this->get('/api/trainers', $this->_headers)
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
                'items.0.role' => 'string'
            ])->etc())
            ->assertJsonPath('currentPage', 1)
            ->assertJsonPath('perPage', 30);
    }


    public function test_search_trainers()
    {
        $trainer = User::role(User::ROLE_TRAINER)->first();

        // search by the first name of the first athlete
        $params = ['search' => $trainer->first_name . ' ' . $trainer->last_name];
        $this->call('GET', '/api/trainers', $params)
            ->assertOk()
            ->assertJsonPath('total', 1);
    }

    public function test_assign_clients()
    {
        // select a list of athletes and coaches
        $athletes = User::role(User::ROLE_ATHLETE)->limit(10)->pluck('id');
        $coaches = User::role(User::ROLE_COACH)->limit(5)->pluck('id');

        // find a trainer
        $trainer = User::findByEmail('tester+trainer1@digitalkrikits.co.uk');

        $data = [
            'user_ids' => [...$athletes,...$coaches],
        ];

        // assign trainer to coach
        $this->put("/api/trainers/{$trainer->id}/assign", $data)
            ->assertOk()
            ->assertJsonPath('message', __('Great! You have successfully assigned the trainer to the selected clients.'));

        // check if the trainer is assigned to the athletes & coaches
        foreach ($athletes as $athleteId) {
            $athlete = User::find($athleteId);
            $this->assertEquals($athlete->trainer->id, $trainer->id);
        }
        foreach ($coaches as $coachId) {
            $coach = User::find($coachId);
            $this->assertEquals($coach->trainer->id, $trainer->id);
        }
    }

}
