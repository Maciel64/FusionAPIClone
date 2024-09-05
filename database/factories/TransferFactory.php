<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transfer>
 */
class TransferFactory extends Factory
{
    private $statuses = [
      'pending',
      'paid',
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
      return [
        'partner_id'   => User::factory(),
        'order_id'     => $this->faker->uuid,
        'status'       => $this->faker->randomElement($this->statuses),
        'note'         => $this->faker->text,
        'amount'       => $this->faker->randomFloat(2, 0, 1000),
        'discount'     => $this->faker->randomFloat(2, 0, 1000),
        'total'        => $this->faker->randomFloat(2, 0, 1000),
        'receipt_name' => $this->faker->name,
        'receipt_url'  => $this->faker->imageUrl,
      ];
    }
}
