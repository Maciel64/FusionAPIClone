<?php

namespace Database\Factories;

use App\Models\HealthAdviceHasUser;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\HealthAdviceHasUser>
 */
class HealthAdviceHasUserFactory extends Factory
{
    protected $model = HealthAdviceHasUser::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
          'advice_code' => $this->faker->randomNumber(5), 
          'health_advice' => $this->faker->randomElement(config('health_advice.br')),
          'user_id' => User::factory(),
        ];
    }
}
