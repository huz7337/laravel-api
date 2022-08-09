<?php

namespace Tests\Feature;

use App\Models\AthleteExercise;
use App\Models\AthleteWorkout;
use App\Models\Program;
use App\Models\User;
use App\Models\Workout;
use Carbon\Carbon;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AthleteWorkoutTest extends TestCase
{


    public function test_start_program()
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
            'start_day' => 1
        ];

        Sanctum::actingAs($athlete);
        $this->put("/api/start-program/{$program->id}", $data)
            ->assertOk();
    }


    public function test_get_workouts()
    {
        // create a program and assign it to the athlete
        $this->test_start_program();

        // get the workouts for the next 7 days
        $data = [
            'start_date' => Carbon::now()->next('Monday')->toDateString(),
            'end_date' => Carbon::now()->next('Monday')->addDays(7)->toDateString()
        ];

        $this->call('GET', '/api/workouts', $data)
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) =>
            $json->whereAllType($this->_jsonStructure('0.'))
                ->etc());
    }


    public function test_update_workout()
    {
        $this->test_start_program();

        $athlete = User::findByEmail('tester+athlete1@digitalkrikits.co.uk');
        /**
         * @var AthleteWorkout $workout
         */
        $workout = $athlete->workouts()->first();
        $data = ['sets' => []];
        $exercises = [];

        if ($workout->warmup) {
            $set = [
                'id' => $workout->warmup->id,
                'exercises' => []
            ];
            foreach ($workout->warmup->exercises as $exercise) {
                $set['exercises'][] = [
                    'id' => $exercise->id,
                    'completed' => true
                ];
            }
            $data['sets'][] = $set;
        }

        if ($workout->cooldown) {
            $set = [
                'id' => $workout->cooldown->id,
                'exercises' => []
            ];
            foreach ($workout->cooldown->exercises as $exercise) {
                $set['exercises'][] = [
                    'id' => $exercise->id,
                    'completed' => true
                ];
            }
            $data['sets'][] = $set;
        }

        if ($workout->sets) {
            foreach ($workout->sets as $athleteSet) {
                $set = [
                    'id' => $athleteSet->id,
                    'exercises' => []
                ];
                foreach ($athleteSet->exercises as $exercise) {
                    $set['exercises'][] = [
                        'id' => $exercise->id,
                        'completed' => true
                    ];
                }
                $data['sets'][] = $set;
            }
        }

        $this->post("/api/workouts/{$workout->id}", $data)
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) =>
            $json->whereAllType($this->_jsonStructure())
                ->etc());

        foreach ($exercises as $exerciseId) {
            $exercise = AthleteExercise::find($exerciseId);
            $this->assertTrue($exercise->completed);
        }
    }

    public function test_show_workout()
    {
        $this->test_start_program();
        $athlete = User::findByEmail('tester+athlete1@digitalkrikits.co.uk');
        $workout = $athlete->workouts()->first();

        $this->get("/api/workouts/{$workout->id}")
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) =>
            $json->whereAllType($this->_jsonStructure())
                ->etc());
    }


    /**
     * Get the json structure for a workout
     * @param string $prefix
     * @return string[]
     */
    private function _jsonStructure(string $prefix = ''): array
    {
        return [
            $prefix . 'id' => 'integer',
            $prefix . 'title' => 'string',
            $prefix . 'date' => 'string',
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
            $prefix . 'warmup.exercises.0.exercise.name' => 'string',
            $prefix . 'warmup.exercises.0.exercise.instructions' => 'string',
            $prefix . 'warmup.exercises.0.exercise.type' => 'string',
            $prefix . 'warmup.exercises.0.exercise.video' => 'string|null',
        ];
    }

}
