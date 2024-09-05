<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Contact>
 */
class ContactFactory extends Factory
{

    private $typesPhone = [
      'mobile_phone',
      'home_phone',
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
          'model_id'     => User::factory(),
          'model_type'   => User::class,
          'type'         => $this->faker->randomElement($this->typesPhone),
          'country_code' => $this->faker->randomNumber(2),
          'area_code'    => $this->faker->randomNumber(2),
          'number'       => $this->faker->randomNumber(8),
        ];
    }
}
