<?php

namespace Database\Factories;

use App\Models\Metric;
use App\Models\MetricSet;
use Illuminate\Database\Eloquent\Factories\Factory;

class MetricSetFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = MetricSet::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->unique()->firstName,
            'description' => $this->faker->realText(400),
        ];
    }

    /**
     * Metric set without any metrics
     *
     * @return Factory
     */
    public function metrics()
    {
        return $this->state(function (array $attributes) {
            return [
                'metrics' => Metric::factory()->count(5)->make()->toArray()
            ];
        });
    }
}
