<?php

namespace Database\Factories;

use App\Models\Coworking;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class CoworkingOpeningHoursFactory extends Factory
{
  private $daysOfWeek = [
    'monday',
    'tuesday',
    'wednesday',
    'thursday',
    'friday',
    'saturday',
    'sunday',
  ];

  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition()
  {
      return [
        'coworking_id' => Coworking::factory(),
        'day_of_week' => $this->faker->randomElement($this->daysOfWeek),
        'opening' => Carbon::createFromTime(8, 0, 0)->format('H:i'),
        'closing' => Carbon::createFromTime(18, 0, 0)->format('H:i'),
      ];
  }
}
