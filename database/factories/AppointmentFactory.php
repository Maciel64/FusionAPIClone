<?php

namespace Database\Factories;

use App\Models\Room;
use App\Models\Schedule;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Appointment>
 */
class AppointmentFactory extends Factory
{
    public function definition()
    {

      $time           = Carbon::createFromTime(8)->day(19)->month(11)->year(2022);
      $timeInit       = $time->format('Y-m-d H:i');
      $timeEnd        = $time->addMinutes(75)->format('Y-m-d H:i');
      $timeTotal      = $this->diffMinutes($timeInit, $timeEnd);
      $valuePerMinute = $this->faker->randomFloat(2, 0.5, 1.5);

      return [
        'patient_name'     => $this->faker->name,
        'patient_phone'    => $this->faker->phoneNumber,
        'schedule_id'      => Schedule::factory(),
        'customer_id'      => User::factory(),
        'room_id'          => Room::factory(),
        'time_init'        => $timeInit,
        'time_end'         => $timeEnd,
        'time_total'       => $timeTotal,
        'status'           => 'scheduled',
        'value_per_minute' => $valuePerMinute,
        'value_total'      => $timeTotal * $valuePerMinute
      ];
    }


    private function diffMinutes($timeInit, $timeEnd)
    {
      $timeInit = Carbon::createFromFormat('Y-m-d H:i', $timeInit);
      $timeEnd  = Carbon::createFromFormat('Y-m-d H:i', $timeEnd);
      return $timeEnd->diffInMinutes($timeInit);
    }
}
