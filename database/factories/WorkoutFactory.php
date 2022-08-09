<?php

namespace Database\Factories;

use App\Models\Exercise;
use App\Models\Set;
use App\Models\Workout;
use Illuminate\Database\Eloquent\Factories\Factory;

class WorkoutFactory extends Factory
{

    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Workout::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->dayOfWeek,
            'type' => Workout::TYPE_ACTIVE
        ];
    }

    /**
     * @return WorkoutFactory
     */
    public function active(): WorkoutFactory
    {
        return $this->state(function (array $attributes) {
            $data = [];
            $setOrder = 0;
            $warmups = Exercise::factory()->count(5)->warmup()->create();
            $data['warmup'] =  [
                'order' => $setOrder++,
                'title' => $this->faker->name,
                'exercises' => []
            ];
            $exerciseOrder = 0;
            foreach ($warmups as $warmup) {
                $data['warmup']['exercises'][] = [
                    'exercise_id' => $warmup->id,
                    'order' => $exerciseOrder++,
                    'reps' => 10,
                    'sets' => 1,
                    'rest' => 10,
                    'notes' => $this->faker->realText(400),
                ];
            }

            $exercise = Exercise::factory()->create();
            $data['sets'] = [];
            $data['sets'][] = [
                'order' => $setOrder++,
                'type' => Set::TYPE_EXERCISE,
                'title' => $this->faker->name,
                'exercises' => [[
                    'exercise_id' => $exercise->id,
                    'order' => 0,
                    'reps' => 30,
                    'sets' => 3,
                    'rest' => 15,
                    'notes' => $this->faker->realText(400),
                ]]
            ];

            $superset = Exercise::factory()->count(2)->create();
            $exerciseOrder = 0;
            $exercises = [];
            foreach ($superset as $exercise) {
                $exercises[] = [
                    'exercise_id' => $exercise->id,
                    'order' => $exerciseOrder++,
                    'reps' => 10,
                    'sets' => 3,
                    'rest' => 15,
                    'notes' => $this->faker->realText(400),
                ];
            }
            $data['sets'][] = [
                'order' => $setOrder++,
                'type' => Set::TYPE_SUPERSET,
                'title' => $this->faker->name,
                'exercises' => $exercises
            ];

            $circuit = Exercise::factory()->count(3)->create();
            $exerciseOrder = 0;
            $exercises = [];
            foreach ($circuit as $exercise) {
                $exercises[] = [
                    'exercise_id' => $exercise->id,
                    'order' => $exerciseOrder++,
                    'reps' => 10,
                    'sets' => 2,
                    'rest' => 15,
                    'notes' => $this->faker->realText(400),
                ];
            }
            $data['sets'][] = [
                'order' => $setOrder++,
                'type' => Set::TYPE_CIRCUIT,
                'title' => $this->faker->name,
                'exercises' => $exercises
            ];

            $cooldowns = Exercise::factory()->count(3)->cooldown()->create();
            $data['cooldown'] =  [
                'order' => $setOrder++,
                'title' => $this->faker->name,
                'exercises' => []
            ];
            $exerciseOrder = 0;
            foreach ($cooldowns as $cooldown) {
                $data['cooldown']['exercises'][] = [
                    'exercise_id' => $cooldown->id,
                    'order' => $exerciseOrder++,
                    'reps' => 10,
                    'sets' => 1,
                    'rest' => 10,
                    'notes' => $this->faker->realText(400),
                ];
            }

            return $data;
        });
    }

    /**
     * @return WorkoutFactory
     */
    public function recovery(): WorkoutFactory
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => Workout::TYPE_RECOVERY,
                'notes' => $this->faker->realText(500)
            ];
        });
    }
}
