<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\UserVerifyCode;
use App\Services\UserService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\Mockings\SendgridMocking;
use Tests\TestCase;

class UserTest extends TestCase
{

  use RefreshDatabase;

  public function setUp(): void
  {
    parent::setUp();
  }

  public function test_when_creating_a_new_user_and_respective_user_check_your_email()
  {
    $payload = [
      'name'     => 'New admin',
      'email'    => 'lf.system@outlook.com',
      'user_type' => 'partner',
    ];

    $service = new UserService();


    $user = $service->store($payload);

    $userVerify = UserVerifyCode::where('email', $user->email)->first();

    $service->verifyEmail($user->email, $userVerify->code);
    
    $this->assertNotNull($user->fresh()->email_verified_at);
    $this->assertDatabaseMissing('user_verify_codes', ['email' => $user->email]);
  }


  public function test_when_a_user_tries_to_access_a_route_without_being_verified()
  {
    $user = User::factory()->create(['email_verified_at' => null]);
    $user->assignRole('customer');
    Auth::login($user);

    $payload = [
      'type' => 'city',
      'value' => 'itaqua',
    ];

    $response = $this->post(route('room.search'), $payload);
    
    $response->assertStatus(400);
    $this->assertEquals('Your account is not active', $response->json('message'));
  }

  public function test_when_defaulter_user_tries_to_access_a_route()
  {
    $this->loginWithCustomer();

    $user = Auth::user();
    $user->status = 'inadimplente';
    $user->save();
    $response = $this->get(route('appointment.index'));
    $response->assertStatus(400);
    $this->assertEquals('Você está inadimplente, consulte o administrador do sistema', $response->json('message'));
  }
}
