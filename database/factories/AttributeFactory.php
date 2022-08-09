<?php

namespace Database\Factories;

use App\Models\Attribute;
use Illuminate\Database\Eloquent\Factories\Factory;

class AttributeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Attribute::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->firstName,
            'type' => Attribute::TYPE_PATTERN,
        ];
    }


    /**
     *
     * @return AttributeFactory
     */
    public function type(): AttributeFactory
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => Attribute::TYPE_TYPE
            ];
        });
    }


    /**
     *
     * @return AttributeFactory
     */
    public function muscle(): AttributeFactory
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => Attribute::TYPE_MUSCLE
            ];
        });
    }


    /**
     *
     * @return AttributeFactory
     */
    public function plane(): AttributeFactory
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => Attribute::TYPE_PLANE
            ];
        });
    }
}
