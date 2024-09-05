<?php

namespace Tests\Feature;

use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Tests\Mockings\PagarmeMocking;
use Tests\TestCase;

class CustomerTest extends TestCase
{
  private $mocking;

  public function setUp(): void
  {
    parent::setUp();
    $this->mocking = new PagarmeMocking();
  }

  public function test_when_register_a_new_customer_with_card_and_plan()
  {
    $this->mocking->createCustomer();
    $this->mocking->createCard();
    
    $plan = Plan::factory()->create();

    $payload = [
      'name'                  => "John Doe",
      'email'                 => "register.54545@test.com",
      'password'              => '12345678',
      'password_confirmation' => '12345678',
      'document'              => '00000000000',
      'document_type'         => 'CPF',
      'gender'                => 'male',
      'birth_date'            => '1990-01-01',
      'phones'                => [
        'mobile_phone' => [
          'country_code' => '55',
          'area_code'    => '019',
          'number'       => '999583179',
        ]
      ],
      'health_advice'    => 'COFEN',
      'advice_code'      => '555606512',
      'address'          => [
        'line_1'   => 'Rua dos Bobos, 0',
        'line_2'   => 'Apto 123',
        'city'     => 'São Paulo',
        'state'    => 'SP',
        'country'  => 'BR',
        'zip_code' => '01311-000',
      ],
      "plan_uuid"  => $plan->uuid,
      "card" => [ 
        'number'                       => '4111111111111111',
        'holder_name'                  => 'John Doe',
        'holder_document'              => '00000000000',
        'exp_month'                    => '12',
        'exp_year'                     => '2029',
        'cvv'                          => '123',
        'brand'                        => 'Elo',
        'billing_address_is_different' => false,
        'billing_address'              => [
          'line_1'   => 'Rua dos Bobos, 0',
          'line_2'   => 'Apto 123',
          'city'     => 'São Paulo',
          'state'    => 'SP',
          'country'  => 'BR',
          'zip_code' => '01311-000',
        ]
      ]
    ];

    $response = $this->post(route('customer.register'), $payload);
    $response->assertStatus(200);
    
  }

  public function test_when_register_a_new_customer()
  {
    $this->withExceptionHandling();
    $payload = [
      'name'          => 'Luiz Felipe',
      'email'         => 'luiz.customer@fusion.com',
      'document'      => '00000000000',
      'document_type' => 'CPF',
      'gender'        => 'male',
      'birth_date'    => '1993-03-11',
      'phones'         => [
        'home_phone' => [
          'country_code' => '55',
          'area_code'    => '11',
          'number'       => '999999999',
        ],
        'mobile_phone' => [
          'country_code' => '55',
          'area_code'    => '11',
          'number'       => '999999999',
        ],
      ],
      'health_advice' => 'CRM',
      'advice_code'   => '884455',
      'address'       => [
        'line_1'   => 'Rua 1',
        'line_2'   => 'test',
        'city'     => 'test',
        'state'    => 'SP',
        'country'  => 'BR',
        'zip_code' => '12345678',
      ],
    ];

    $response = $this->post(route('customer.store'), $payload);
    $response->assertStatus(200);
  }

  public function test_when_a_fusion_admin_validate_a_customer()
  {
    $this->mocking->createCustomer();
    $this->loginWithAdmin();
    $this->withExceptionHandling();
    $payload = [
      'name'          => 'Luiz Felipe',
      'email'         => 'luiz.customer@fusion.com',
      'document'      => '00000000000',
      'document_type' => 'CPF',
      'gender'        => 'male',
      'birth_date'    => '1993-03-11',

      'phones'         => [
        'home_phone' => [
          'country_code' => '55',
          'area_code'    => '11',
          'number'       => '999999999',
        ],
        'mobile_phone' => [
          'country_code' => '55',
          'area_code'    => '11',
          'number'       => '999999999',
        ],
      ],
      'health_advice' => 'CRM',
      'advice_code'   => '884455',
      'address'       => [
        'line_1'   => 'Rua 1',
        'line_2'   => 'test',
        'city'     => 'test',
        'state'    => 'SP',
        'country'  => 'BR',
        'zip_code' => '12345678',
      ],
    ];

    $response = $this->post(route('customer.store'), $payload);
    $response->assertStatus(200);

    $user = User::where('email', $payload['email'])->first();

    $payload = ['uuid' => $user->uuid];

    $response = $this->post(route('customer.check', $payload));
    $response->assertStatus(200);
    $this->assertEquals(__('response.check.success', ['resource' => 'Customer']), $response->json('message'));
  }

  public function test_when_get_all_customers_to_check()
  {
    $this->loginWithAdmin();
    $this->withExceptionHandling();
    $payload = [
      'name'          => 'Luiz Felipe',
      'email'         => 'luiz.customer@fusion.com',
      'document'      => '00000000000',
      'document_type' => 'CPF',
      'gender'        => 'male',
      'birth_date'    => '1993-03-11',
      'phones'         => [
        'home_phone' => [
          'country_code' => '55',
          'area_code'    => '11',
          'number'       => '999999999',
        ],
        'mobile_phone' => [
          'country_code' => '55',
          'area_code'    => '11',
          'number'       => '999999999',
        ],
      ],
      'health_advice' => 'CRM',
      'advice_code'   => '884455',
      'address'       => [
        'line_1'   => 'Rua 1',
        'line_2'   => 'test',
        'city'     => 'test',
        'state'    => 'SP',
        'country'  => 'BR',
        'zip_code' => '12345678',
      ],
    ];

    $response = $this->post(route('customer.store'), $payload);
    $response->assertStatus(200);

    $data = [
      'verified' => false,
    ];

    $response = $this->post(route('customer.search'), $data);
    $response->assertStatus(200);
    $response->assertJsonCount(1, 'data');
    $this->assertEquals($payload['email'], $response->json('data.0.email'));
  }

  public function test_customer_cancel_account()
  {
    $this->withoutExceptionHandling();
    $this->loginWithCustomer();
    Subscription::factory()->create(['user_id' => Auth::user()->id]);
    $params = ['uuid' => Auth::user()->uuid];
    $response = $this->post(route('customer.cancel', $params));
    $response->assertStatus(200);
    $user = User::where('email', auth()->user()->email)->first();
    $this->assertEquals('canceled', $user->subscription->status);
    $this->assertEquals(0, $user->account_active);
  }
}
