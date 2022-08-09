<?php

namespace Database\Factories;

use App\Models\InfluencerPostComment;
use Illuminate\Database\Eloquent\Factories\Factory;

class InfluencerPostCommentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = InfluencerPostComment::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'body' => $this->faker->realText(100),
        ];
    }
}
