<?php

namespace Database\Factories;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'email' => $this->faker->unique()->freeEmail,
            'password' => Hash::make('Test1234!'), // password
            'date_of_birth' => Carbon::now()->subYears(15)->format('Y-m-d'),
            'remember_token' => Str::random(10),
            'email_verified_at' => now(),
            'account_type' => User::ACCOUNT_TYPE_CLIENT,
            'status' => User::STATUS_ACTIVE,
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return Factory
     */
    public function unverified()
    {
        return $this->state(function (array $attributes) {
            return [
                'email_verified_at' => null,
            ];
        });
    }


    /**
     * Create provider accounts
     * @return UserFactory
     */
    public function provider()
    {
        return $this->state(function (array $attributes) {
            return [
                'account_type' => User::ACCOUNT_TYPE_PROVIDER
            ];
        });
    }
}
