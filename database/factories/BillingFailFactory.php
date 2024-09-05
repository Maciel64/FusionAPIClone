<?php

namespace Database\Factories;

use App\Models\Appointment;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BillingFail>
 */
class BillingFailFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
      return [
        'user_id' => User::factory(),
        'reference_date' => $this->faker->date(),
        'reference_type' => $this->faker->randomElement(['monthly', 'daily']),
        'attempts'       => $this->faker->randomNumber(),
        'status'         => $this->faker->randomElement(['failed', 'defaulter', 'paid']),
        'description'    => $this->faker->text(),
        'model_type'     => $this->faker->randomElement([Appointment::class, Subscription::class]),
      ];
    }
}
