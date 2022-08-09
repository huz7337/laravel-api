<?php

namespace Tests\Feature;

use App\Models\Exercise;
use App\Models\Program;
use App\Models\Set;
use App\Models\User;
use App\Models\Workout;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class WorkoutTest extends TestCase
{


    public function test_create_workout()
    {
        $program = Program::factory()->create();
        $data = Workout::factory()->active()->make()->toArray();

        $this->post("/api/programs/{$program->id}/workouts", $data)
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) =>
            $json->whereAllType($this->_jsonStructure())->etc())
            ->assertJsonPath('title', $data['title'])
            ->assertJsonPath('program_id', $program->id);
    }


    public function test_list_program_workouts()
    {
        $program = Program::factory()->create();
        $workouts = Workout::factory()->count(6)->active()->make()->toArray();
        $workouts[] = Workout::factory()->recovery()->make()->toArray();

        foreach ($workouts as $workout) {
            $this->post("/api/programs/{$program->id}/workouts", $workout)
                ->assertOk();
        }


        $this->get("/api/programs/{$program->id}/workouts")
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) =>
            $json->whereAllType($this->_jsonStructure('0.'))->etc());
    }

    public function test_get_athletes_program_workouts() {
        Program::factory()->count(1)->live()->create();

        $coach = User::role(User::ROLE_COACH)->first();
        $program = Program::first();

        $this->get("/api/coaches/{$coach->id}/athletes/programs/{$program->id}/workouts")->assertForbidden();
        $this->get("/api/coaches/{$coach->id}/athletes/programs/0/workouts")->assertNotFound();
    }

    public function test_delete_last_workout()
    {
        $program = Program::factory()->create()->first();
        Workout::factory()->create([
            'program_id' => $program->id,
            'day' => $program->workouts()->max('day') + 1
        ]);

        $this->delete("/api/programs/{$program->id}/workouts/last")->assertOk();
    }

    /**
     * Json structure of a workout
     * @param string $prefix
     * @return string[]
     */
    private function _jsonStructure(string $prefix = ''): array
    {
        return [
            $prefix . 'id' => 'integer',
            $prefix . 'title' => 'string',
            $prefix . 'program_id' => 'integer',
            $prefix . 'day' => 'integer',
            $prefix . 'type' => 'string',
            $prefix . 'warmup' => 'array',
            $prefix . 'warmup.id' => 'integer',
            $prefix . 'warmup.type' => 'string',
            $prefix . 'warmup.title' => 'string',
            $prefix . 'warmup.order' => 'integer',
            $prefix . 'warmup.exercises' => 'array',
            $prefix . 'warmup.exercises.0.id' => 'integer',
            $prefix . 'warmup.exercises.0.order' => 'integer',
            $prefix . 'warmup.exercises.0.reps' => 'integer',
            $prefix . 'warmup.exercises.0.sets' => 'integer',
            $prefix . 'warmup.exercises.0.rest' => 'integer',
            $prefix . 'warmup.exercises.0.notes' => 'string',
            $prefix . 'warmup.exercises.0.exercise.name' => 'string',
            $prefix . 'warmup.exercises.0.exercise.instructions' => 'string',
            $prefix . 'warmup.exercises.0.exercise.type' => 'string',
            $prefix . 'warmup.exercises.0.exercise.video' => 'string|null',
        ];
    }

}
