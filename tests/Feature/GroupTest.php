<?php

namespace Tests\Feature;

use App\Models\Group;
use App\Models\User;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class GroupTest extends TestCase
{

    public function test_list_groups()
    {
        $this->get('/api/groups')
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) =>
            $json->whereAllType([
                'currentPage' => 'integer',
                'perPage' => 'integer',
                'lastPage' => 'integer',
                'total' => 'integer',
                'items' => 'array',
                'items.0.id' => 'integer',
                'items.0.name' => 'string',
            ])->etc())
            ->assertJsonPath('currentPage', 1)
            ->assertJsonPath('perPage', 30);
    }


    public function test_create_group()
    {
        $data = Group::factory()->make()->toArray();

        $this->post('/api/groups', $data)
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) =>
            $json->whereAllType([
                'id' => 'integer',
                'name' => 'string',
            ])->etc())
            ->assertJsonPath('name', $data['name']);
    }


    public function test_edit_group()
    {
        $group = Group::factory()->create();

        $this->put("/api/groups/{$group->id}", ['name' => 'edited group'])
            ->assertOk()
            ->assertJsonPath('id', $group->id)
            ->assertJsonPath('name', 'Edited group');
    }


    public function test_delete_group()
    {
        $group = Group::factory()->create();

        // assign the group to an athlete
        $athlete = User::role(User::ROLE_ATHLETE)->first();
        $athlete->assignGroup($group);

        $this->delete("/api/groups/{$group->id}")
            ->assertOk()
            ->assertJsonPath('message', __('The group :name has been removed.', ['name' => $group->name]));
    }


    public function test_assign_clients()
    {
        // select a list of athletes and coaches
        $athletes = User::role(User::ROLE_ATHLETE)->limit(10)->pluck('id');
        $coaches = User::role(User::ROLE_COACH)->limit(5)->pluck('id');

        // find a group
        $group = Group::first();

        $data = [
            'user_ids' => [...$athletes,...$coaches],
        ];

        // assign trainer to coach
        $this->put("/api/groups/{$group->id}/assign", $data)
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

}
