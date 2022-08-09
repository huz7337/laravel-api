<?php

namespace Tests\Feature;

use App\Models\Attribute;
use App\Models\Exercise;
use App\Models\User;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class DirectoryTest extends TestCase
{

    public function test_trainer_directory()
    {
        $this->get('/api/directories/trainers')
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) =>
            $json->whereAllType([
                '0.id' => 'integer',
                '0.name' => 'string'
            ])->etc());
    }


    public function test_user_status_directory()
    {
        $this->get('/api/directories/user-statuses')
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) =>
            $json->whereAllType([
                '0.id' => 'string',
                '0.name' => 'string'
            ])->etc());
    }


    public function test_group_directory()
    {
        $this->get('/api/directories/groups')
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) =>
            $json->whereAllType([
                '0.id' => 'integer',
                '0.name' => 'string'
            ])->etc());
    }

    public function test_client_roles_directory()
    {
        $this->get('/api/directories/client-roles')
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) =>
            $json->whereAllType([
                '0.id' => 'string',
                '0.name' => 'string'
            ])->etc());
    }


    public function test_measure_units_directory()
    {
        $this->get('/api/directories/measure-units')
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) =>
            $json->whereAllType([
                '0.id' => 'integer',
                '0.name' => 'string'
            ])->etc());
    }


    public function test_patterns_directory()
    {
        Attribute::factory()->create();

        $this->get('/api/directories/exercise-patterns')
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) =>
            $json->whereAllType([
                '0.id' => 'integer',
                '0.name' => 'string'
            ])->etc());
    }


    public function test_muscles_directory()
    {
        Attribute::factory()->muscle()->create();

        $this->get('/api/directories/exercise-muscles')
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) =>
            $json->whereAllType([
                '0.id' => 'integer',
                '0.name' => 'string'
            ])->etc());
    }


    public function test_types_directory()
    {
        Attribute::factory()->type()->create();

        $this->get('/api/directories/exercise-types')
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) =>
            $json->whereAllType([
                '0.id' => 'integer',
                '0.name' => 'string'
            ])->etc());
    }


    public function test_planes_directory()
    {
        Attribute::factory()->plane()->create();

        $this->get('/api/directories/exercise-planes')
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) =>
            $json->whereAllType([
                '0.id' => 'integer',
                '0.name' => 'string'
            ])->etc());
    }

    public function test_program_statuses_directory()
    {
        $this->get('/api/directories/program-statuses')
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) =>
            $json->whereAllType([
                '0.id' => 'string',
                '0.name' => 'string'
            ])->etc());
    }


    public function test_exercises_directory()
    {
        Exercise::factory()->count(20)->create();

        $this->get('/api/directories/exercises')
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) =>
            $json->whereAllType([
                '0.id' => 'integer',
                '0.name' => 'string'
            ])->etc());
    }


    public function test_warmups_directory()
    {
        Exercise::factory()->count(10)->warmup()->create();

        $this->get('/api/directories/warmups')
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) =>
            $json->whereAllType([
                '0.id' => 'integer',
                '0.name' => 'string'
            ])->etc());
    }


    public function test_cooldowns_directory()
    {
        Exercise::factory()->count(10)->cooldown()->create();

        $this->get('/api/directories/cooldowns')
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) =>
            $json->whereAllType([
                '0.id' => 'integer',
                '0.name' => 'string'
            ])->etc());
    }

}
