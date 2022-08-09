<?php

namespace Tests\Feature;

use App\Models\Group;
use App\Models\User;
use Tests\TestCase;

class BulkTest extends TestCase
{

    public function test_assign_trainer()
    {
        // select a list of athletes and coaches
        $athletes = User::role(User::ROLE_ATHLETE)->limit(10)->pluck('id');
        $coaches = User::role(User::ROLE_COACH)->limit(5)->pluck('id');

        // find a trainer
        $trainer = User::findByEmail('tester+trainer1@digitalkrikits.co.uk');

        $data = [
            'user_ids' => [...$athletes,...$coaches],
            'trainer_id' => $trainer->id
        ];

        // assign trainer to coach
        $this->post("/api/users/trainers", $data)
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


    public function test_assign_group()
    {
        // select a list of athletes and coaches
        $athletes = User::role(User::ROLE_ATHLETE)->limit(10)->pluck('id');
        $coaches = User::role(User::ROLE_COACH)->limit(5)->pluck('id');

        // find a group
        $group = Group::first();

        $data = [
            'user_ids' => [...$athletes,...$coaches],
            'group_id' => $group->id
        ];

        // assign trainer to coach
        $this->post("/api/users/groups", $data)
            ->assertOk()
            ->assertJsonPath('message', __('Great! You have successfully assigned all the selected clients to the group.'));

        // check if the trainer is assigned to the athletes & coaches
        foreach ($athletes as $athleteId) {
            $athlete = User::find($athleteId);
            $this->assertEquals($athlete->groups()->first()->id, $group->id);
        }
        foreach ($coaches as $coachId) {
            $coach = User::find($coachId);
            $this->assertEquals($coach->groups()->first()->id, $group->id);
        }
    }

    public function test_invite_users()
    {
        // create 5 coaches
        $coaches = User::factory()->count(5)->make(['role' => User::ROLE_COACH]);
        // create 10 athletes
        $athletes = User::factory()->count(10)->make(['role' => User::ROLE_ATHLETE]);

        $data = [
            'clients' => [...$coaches->toArray(), ...$athletes->toArray()]
        ];

        $this->post('/api/users/invite', $data)
            ->assertOk()
            ->assertJsonPath('message', __('The clients have been invited.'));
    }

}
