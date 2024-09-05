<?php

namespace Tests\Unit;

use App\Models\Address;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class AddressTest extends TestCase
{

  /** @test */
  public function when_create_address_related_with_user()
  {
    $this->withoutExceptionHandling();
    $user = User::factory()->create();
    $address = Address::factory()->create([
      'model_type' => User::class,
      'model_id'   => $user->id,
    ]);
    $this->assertEquals($address->id, $user->addresses()->first()->id);
  }
  
  /** @test */
  public function when_deleting_a_user_related_to_the_address_and_verifying_that_the_relationship_was_actually_removed()
  {
      $this->withoutExceptionHandling();
      $user = User::factory()->create();
      $address = Address::factory()->create([
        'model_type' => User::class,
        'model_id'   => $user->id,
      ]);
      $user->addresses()->delete();
      $user->delete();
      $this->assertNull($address->fresh());
  }

  /** @test */
  public function when_deleting_a_address_related_to_the_user_and_verifying_that_the_relationship_was_actually_removed()
  {
      $this->withoutExceptionHandling();
      $user = User::factory()->create();
      $address = Address::factory()->create([
        'model_type' => User::class,
        'model_id'   => $user->id,
      ]);
      $addressUser = $user->addresses()->first();
      $this->assertEquals($address->id, $addressUser->id);
      $address->delete();
      $this->assertNull($user->fresh()->addresses()->first());
  }
}
