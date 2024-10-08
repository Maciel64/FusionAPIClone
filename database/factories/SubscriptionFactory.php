<?php

namespace Database\Factories;

use App\Models\Plan;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Subscription>
 */
class SubscriptionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
          'user_id'         => User::factory(),
          'plan_id'         => Plan::factory(),
          'pagarme_card_id' => $this->faker->uuid(),
          'price'           => $this->faker->randomNumber(),
          'status'          => $this->faker->word(),
        ];
    }
}
