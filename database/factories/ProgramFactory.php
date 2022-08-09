<?php

namespace Database\Factories;

use App\Models\Program;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProgramFactory extends Factory
{

    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Program::class;


    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->firstName,
            'description' => $this->faker->realText(400),
            'status' => 'offline'
        ];
    }

    /**
     * Generate invalid data
     *
     * @return ProgramFactory
     */
    public function live(): ProgramFactory
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'live'
            ];
        });
    }
}
