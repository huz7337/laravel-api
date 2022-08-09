<?php

namespace Database\Factories;

use App\Models\Exercise;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExerciseFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Exercise::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->firstName,
            'instructions' => $this->faker->realText(400),
            'type' => Exercise::TYPE_EXERCISE,
        ];
    }

    /**
     * Generate invalid data
     *
     * @return ExerciseFactory
     */
    public function warmup(): ExerciseFactory
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => Exercise::TYPE_WARMUP,
            ];
        });
    }

    /**
     * Generate invalid data
     *
     * @return ExerciseFactory
     */
    public function cooldown(): ExerciseFactory
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => Exercise::TYPE_COOLDOWN,
            ];
        });
    }
}
