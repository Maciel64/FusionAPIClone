<?php

namespace Database\Factories;

use App\Models\Plan;
use App\Models\PlanHasUser;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PlanHasUser>
 */
class PlanHasUserFactory extends Factory
{
  protected $model = PlanHasUser::class;
  
  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition()
  {
    return [
      'plan_id' => Plan::factory(),
      'user_id' => User::factory(),
      'start_date' => now(),
      'end_date' => null,
      'active' => true,
    ];
  }
}
