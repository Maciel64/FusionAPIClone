<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Coworking;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class AddressTest extends TestCase
{

  public function setUp(): void
  {
    parent::setUp();
  }

  public function test_when_crate_a_address_related_with_user()
  {
    // $this->withoutExceptionHandling();
     $this->login();
      $payload = [
        'uuid' => Auth::user()->uuid,
        'type' => 'user',
        'line_1'     => 'Rua 1',
        'line_2' => 'Casa 1',
        'city'       => 'Cidade 1',
        'state'      => 'SP',
        'country'   => 'BR',
        'neighborhood' => 'Bairro 1',
        'zip_code'   => '12345678',
      ];

      $user = User::find(Auth::user()->id);

      $response = $this->post(route('address.store'), $payload);
      $response->assertStatus(200);
      $response->assertJsonStructure(['data' => ['uuid','line_1','line_2','country','neighborhood','city','state','zip_code',],]);
      $userAddress = $user->addresses()->first()->toArray();
      if(isset($userAddress['uuid']))
        unset($userAddress['uuid']);

      unset($payload['uuid']);
      unset($payload['type']);
      $this->assertEquals($payload, $userAddress);
  }

  public function test_when_crate_a_address_related_with_coworking()
  {
    $this->login();
    $coworking = Coworking::factory()->create(['user_id' => Auth::user()->id]);
    
    $payload = [
      'uuid'     => $coworking->uuid,
      'type'     => 'coworking',
      'line_1'   => 'Rua 1',
      'line_2'   => 'Casa 1',
      'country'  => 'BR',
      'city'     => 'Cidade 1',
      'neighborhood' => 'Bairro 1',
      'state'    => 'SP',
      'zip_code' => '12345678',
    ];

    $response = $this->post(route('address.store'), $payload);
    $response->assertStatus(200);
    $response->assertJsonStructure(['data' => ['uuid','line_1','line_2','country','city','state','neighborhood','zip_code',],]);

    $coworkingAddress = $coworking->addresses()->first()->toArray();
    unset($coworkingAddress['uuid'], $payload['uuid'], $payload['type']);
    $this->assertEquals($payload, $coworkingAddress);
  }

  public function test_when_destroy_a_addresses()
  {
    $this->login();
    $user = Auth::user();
    $user = User::find($user->id);
    $address = Address::factory()->create([
      'model_type' => User::class,
      'model_id'   => $user->id,
    ]);
    $response = $this->delete(route('address.destroy', ['uuid' => $address->uuid]));
    $response->assertStatus(200);
    $this->assertNull($user->fresh()->addresses()->first());
  }

  public function test_when_user_update_your_addresses()
  {
    $this->withoutExceptionHandling();
    $this->login();
    $user = Auth::user();
    $address = Address::factory()->create([
      'model_type' => get_class($user),
      'model_id'   => $user->id,
    ]);
    $payload = [
      'line_1'     => 'Rua 1',
      'line_2' => 'Casa 1',
      'city'       => 'Cidade 1',
      'state'      => 'SP',
      'country'   => 'Bairro 1',
      'neighborhood' => 'Bairro 1',
      'zip_code'   => '12345678',
    ];
    $response = $this->put(route('address.update', ['uuid' => $address->uuid]), $payload);
    $response->assertStatus(200);
    $response->assertJsonStructure(['data' => ['uuid','line_1','line_2','country','neighborhood','city','state','zip_code']]);
    $user = User::find(Auth::user()->id);
    $userAddress = $user->addresses()->first()->toArray();
    if(isset($userAddress['uuid']))
      unset($userAddress['uuid']);
    $this->assertEquals($payload, $userAddress);
  }
}
