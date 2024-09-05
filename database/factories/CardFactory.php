<?php

namespace Database\Factories;

use App\Models\Card;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserCard>
 */
class CardFactory extends Factory
{
  protected $model = Card::class;

  private $brands = [
    'elo',
    'mastercard',
    'visa',
    'amex',
    'jcb',
    'aura',
    'hipercard',
    'diners',
    'discover',
    'allele',
    'vr',
    'sodexo'
  ];

  public function definition()
  {
    return [
      'user_id'          => User::factory(),
      'customer_id'      => $this->faker->uuid,
      'address_id'       => $this->faker->uuid,
      'card_id'          => $this->faker->uuid,
      'first_six_digits' => $this->faker->randomNumber(6),
      'last_four_digits' => $this->faker->randomNumber(4),
      'brand'            => $this->faker->randomElement($this->brands),
      'holder_name'      => $this->faker->name,
      'holder_document'  => '000.000.000-00',
      'exp_month'        => $this->faker->month(),
      'exp_year'         => $this->faker->year(),
      'status'           => 'active',
      'label'            => 'visa',
    ];
  }
}
