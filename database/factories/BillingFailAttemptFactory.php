<?php

namespace Database\Factories;

use App\Models\BillingFail;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BillingAttempt>
 */
class BillingFailAttemptFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
          'billing_fail_id' => BillingFail::factory(),
          'status' => $this->faker->randomElement(['failed', 'defaulter', 'paid']),
        ];
    }
}
