<?php

namespace Database\Factories;

use App\Models\Document;
use Illuminate\Database\Eloquent\Factories\Factory;

class DocumentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Document::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'type' => Document::DOCUMENT_TYPE_TNC,
            'title' => 'Terms & Conditions',
            'content' => $this->faker->realText(10000),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return Factory
     */
    public function withPrivacy()
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => Document::DOCUMENT_TYPE_PP,
                'title' => 'Privacy Policy'
            ];
        });
    }
}
