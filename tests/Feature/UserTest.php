<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Photo;
use App\Models\User;
use App\Models\UserVerifyCode;
use App\Services\UserService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class UserTest extends TestCase
{
  use RefreshDatabase;

  public function setUp(): void
  {
    parent::setUp();
  }

  public function test_when_create_a_user()
  {
    $this->withExceptionHandling();
    $this->login();
    $payload = [
      'name'     => 'New admin',
      'email'    => 'lf.system@outlook.com',
      'user_type' => 'partner',
    ];

    $response = $this->post(route('user.store'), $payload);
    $response->assertStatus(200);
    $response->assertJsonStructure([
      'data' => [
        'uuid',
        'name',
        'email',
      ],
    ]);

    $this->assertEquals(__('response.store.success', ['resource' => 'User']), $response->json('message'));
  }

  public function test_when_create_a_user_and_send_email_to_verify_code()
  {
    $this->login();
    $payload = [
      'name'     => 'New admin',
      'email'    => 'lf.system@outlook.com',
      'user_type' => 'partner',
    ];
    $response = $this->post(route('user.store'), $payload);
    $response->assertStatus(200);

    $user     = User::where('email', $payload['email'])->first();
    $userCode = UserVerifyCode::where('email', $payload['email'])->first();
    $payload = ['email' => $user->email, 'code' => $userCode->code];
    auth()->guard('web')->logout();
    $verified = $this->withHeader('X-Internal-Token', config('settings.token.internal'))
    ->post(route('user.verify'), $payload);

    $verified->assertStatus(200);
    $this->assertNotNull($user->fresh()->email_verified_at);
    $this->assertDatabaseMissing('user_verify_codes', ['email' => $user->email]);
  }

  public function test_when_update_a_user()
  {
    $this->login();
    $user = User::factory()->create();
    $params = ['uuid' => $user->uuid];
    $payload = [
      'name'     => 'John Doe Updated',
    ];
    $response = $this->put(route('user.update', $params), $payload);
    $response->assertStatus(200);

    $this->assertEquals(__('response.update.success', ['resource' => 'User']), $response->json('message'));
  }

  public function test_when_delete_a_user()
  {
    $this->login();
    $user = User::factory()->create();
    $params = ['uuid' => $user->uuid];
    $response = $this->delete(route('user.destroy', $params));
    $response->assertStatus(200);
    $this->assertEquals(__('response.destroy.success', ['resource' => 'User']), $response->json('message'));
  }

  public function test_when_get_a_user()
  {
    $this->login();
    $user = User::factory()->create();
    $params = ['uuid' => $user->uuid];
    $response = $this->get(route('user.show', $params));
    $response->assertStatus(200);
    $response->assertJsonStructure([
      'data' => [
        'uuid',
        'name',
        'email',
      ],
    ]);

    $this->assertEquals(__('response.show.success', ['resource' => 'User']), $response->json('message'));
  }

  public function test_when_get_all_users()
  {
    $this->login();

    auth()->user();
    $users = User::factory(3)->create();

    foreach ($users as $user) {
      Address::factory()->create([
        'model_type' => User::class,
        'model_id'   => $user->id,
      ]);

      Photo::factory()->create([
        'model_type' => User::class,
        'model_id'   => $user->id,
      ]);

    }


    $response = $this->get(route('user.index', ['role' => 'admin']));
    $response->assertStatus(200);
    $response->assertJsonStructure([
      'data' => [
        '*' => [
          'uuid',
          'name',
          'email',
        ],
      ],
    ]);
  }

  public function test_when_user_login_and_register_their_access()
  {
    $this->withoutExceptionHandling();
    $this->login();
    $user = auth()->user();
    $payload = [
      'email'    => $user->email,
      'password' => 'password',
      'role'     => 'admin',
    ];

    $response = $this->post(route('login'), $payload);
    $response->assertStatus(200);
    $this->assertNotNull($response->json('data.last_access'));
    $this->assertEquals(__('auth.success'), $response->json('message'));
  }

  public function test_when_customer_user_login_their_access()
  {
    $this->withoutExceptionHandling();
    
    $user = User::factory()->create();
    $user->assignRole('customer');

    $payload = [
      'email'    => $user->email,
      'password' => 'password',
      'role'     => 'customer',
    ];

    $response = $this->post(route('login'), $payload);
    $response->assertStatus(200);
    $this->assertNotNull($response->json('data.last_access'));
    $this->assertEquals(__('auth.success'), $response->json('message'));
  }

  public function test_when_partner_user_login_their_access()
  {
    $this->withoutExceptionHandling();
    
    $user = User::factory()->create();
    $user->assignRole('partner');

    $payload = [
      'email'    => $user->email,
      'password' => 'password',
    ];

    $response = $this->post(route('login'), $payload);
    $response->assertStatus(200);
    $this->assertNotNull($response->json('data.last_access'));
    $this->assertEquals(__('auth.success'), $response->json('message'));
  }

  public function test_when_create_a_partner_and_schedule()
  {
    $this->withExceptionHandling();
    $this->login();
    $payload = [
      'name'     => 'New admin',
      'email'    => 'lf.system@outlook.com',
      'user_type' => 'partner',
    ];

    $response = $this->post(route('user.store'), $payload);
    $response->assertStatus(200);
    $response->assertJsonStructure([
      'data' => [
        'uuid',
        'name',
        'email',
      ],
    ]);

    $this->assertEquals(__('response.store.success', ['resource' => 'User']), $response->json('message'));
    $this->assertArrayHasKey('uuid', $response->json('data.schedule'));
  }

}
