<?php

namespace Database\Factories;

use App\Models\Profile;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProfileFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Profile::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'phone_number' => '0755798354',
            'location' => $this->faker->address,
            'height_cm' => 180,
            'weight_kg' => 90,
            'gender' => 'female',
            'preferred_unit' => 'US'
        ];
    }

    /**
     * Generate invalid data
     *
     * @return ProfileFactory
     */
    public function invalid()
    {
        return $this->state(function (array $attributes) {
            return [
                'phone_number' => '07557983540000',
                'location' => 1,
                'height_cm' => 250,
                'height_ft' => 8,
                'height_in' => 12,
                'weight_kg' => 300,
                'weight_lbs' => 600,
                'gender' => 'test',
                'preferred_unit' => 'test'
            ];
        });
    }

    /**
     * Generate US measurements
     *
     * @return ProfileFactory
     */
    public function us()
    {
        return $this->state(function (array $attributes) {
            return [
                'phone_number' => '0755798354',
                'location' => $this->faker->address,
                'height_ft' => 5,
                'height_in' => 5,
                'weight_lbs' => 225,
                'gender' => 'male',
                'preferred_unit' => 'US'
            ];
        });
    }
}
