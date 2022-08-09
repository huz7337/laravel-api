<?php

namespace Database\Factories;

use App\Models\InfluencerPost;
use Illuminate\Database\Eloquent\Factories\Factory;

class InfluencerPostFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = InfluencerPost::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'description' => $this->faker->realText(400),
        ];
    }
}
