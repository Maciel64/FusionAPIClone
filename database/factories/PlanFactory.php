<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Plan>
 */
class PlanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [        
          'name'              => $this->faker->name,
          'price'             => $this->faker->randomNumber(2),
          'description'       => $this->faker->text,
          'trial_period_days' => $this->faker->randomNumber(2),
        ];
    }
}
