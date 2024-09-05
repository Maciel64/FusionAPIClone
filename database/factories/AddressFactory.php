<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Address>
 */
class AddressFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
          'line_1'       => $this->faker->streetName,
          'line_2'       => $this->faker->secondaryAddress,
          'city'         => $this->faker->city,
          'state'        => $this->faker->stateAbbr,
          'country'      => $this->faker->countryCode(),
          'neighborhood' => $this->faker->name,
          'zip_code'     => $this->faker->postcode,
        ];
    }
}
