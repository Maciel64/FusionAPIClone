<?php

namespace Tests\Feature;

use App\Models\Card;
use App\Models\Subscription;
use App\Models\User;
use App\Services\CardService;
use App\Services\UserService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Tests\Mockings\PagarmeMocking;
use Tests\TestCase;

class CardTest extends TestCase
{

  private $mocking;

  public function setUp(): void
  {
    parent::setUp();
    $this->mocking = new PagarmeMocking();
  }

  public function test_card_store()
  {
    $this->loginWithAdmin();
    $this->withExceptionHandling();

    $this->mocking->createCustomer();
    $this->mocking->createCard();
    $this->mocking->createTokenCard();

    $payload = [
      'name'          => 'Luiz Felipe',
      'email'         => 'lf.system@outlook.com',
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

    $userService = new UserService();
    $cardService = new CardService();

    $customer = $userService->customerStore($payload);
    $customer = User::where('email', $payload['email'])->first();
    $userService->customerCheck($customer->uuid);
    Auth::login($customer->fresh());

    $payload = [
      'number'                       => '4111111111111111',
      'holder_name'                  => 'John Doe',
      'holder_document'              => '12345678901',
      'exp_month'                    => '12',
      'exp_year'                     => '2028',
      'cvv'                          => '123',
      'brand'                        => 'visa',
      'billing_address_is_different' => false,
    ];

    $params = [
      'uuid' => $customer->uuid,
    ];

    $response = $this->post(route('card.store', $params), $payload);
    $response->assertStatus(200);

    $card = $customer->card()->first();
    $this->assertNotNull($card->customer_id);
    $this->assertNotNull($card->card_id);
  }


  public function test_card_update()
  {
    $this->loginWithAdmin();
    $this->withExceptionHandling();

    $this->mocking->createCustomer();
    $this->mocking->createCard();
    $this->mocking->createTokenCard();
    $this->mocking->updateCardSubscription();

    $payload = [
      'name'          => 'Luiz Felipe',
      'email'         => 'lf.system@outlook.com',
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

    $userService = new UserService();
    $cardService = new CardService();

    $customer = $userService->customerStore($payload);
    $customer = User::where('email', $payload['email'])->first();
    $userService->customerCheck($customer->uuid);
    Auth::login($customer->fresh());

    $payload = [
      'number'                       => '4111111111111111',
      'holder_name'                  => 'John Doe',
      'holder_document'              => '41084696070',
      'exp_month'                    => '12',
      'exp_year'                     => '2028',
      'cvv'                          => '123',
      'brand'                        => 'visa',
      'billing_address_is_different' => false,
    ];

    $params = [
      'uuid' => $customer->uuid,
    ];

    Subscription::factory()->create([
      'user_id' => $customer->id,
    ]);

    $response = $this->post(route('card.store', $params), $payload);
    $response->assertStatus(200);

    $card = $customer->card()->first();
    $this->assertNotNull($card->customer_id);
    $this->assertNotNull($card->card_id);

    $payload = [
      'number'                       => '4000000000000010',
      'holder_name'                  => 'Luiz Felipe',
      'holder_document'              => '45967382038',
      'exp_month'                    => '10',
      'exp_year'                     => '2030',
      'cvv'                          => '123',
      'brand'                        => 'visa',
      'billing_address_is_different' => false,
    ];

    $params['card_uuid'] = $card->uuid;
    $response = $this->put(route('card.update', $params), $payload);
    $response->assertStatus(200);

    $card = $customer->card()->first();
    $this->assertNotNull($card->customer_id);
    $this->assertNotNull($card->card_id);
    $this->assertEquals($card->holder_name, $payload['holder_name']);
    $this->assertEquals($card->exp_month, $payload['exp_month']);
    $this->assertEquals($card->exp_year, $payload['exp_year']);
    $this->assertEquals($card->holder_document, $payload['holder_document']);
  }


  public function test_card_show()
  {
    $this->loginWithCustomer();

    $card = Card::factory()->create([
      'user_id' => Auth::user()->id,
    ]);

    $params = [
      'uuid' => Auth::user()->uuid,
      'card_uuid' => $card->uuid,
    ];

    $response = $this->get(route('card.show', $params));
    $response->assertStatus(200);
  }

  public function test_card_destroy()
  {
    $this->mocking->createCustomer();
    $this->mocking->createCard();
    $this->mocking->createTokenCard();
    $this->mocking->deleteCard();

    $this->loginWithAdmin();
    $this->withExceptionHandling();
    $payload = [
      'name'          => 'Luiz Felipe',
      'email'         => 'lf.system@outlook.com',
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

    $userService = new UserService();
    $cardService = new CardService();

    $customer = $userService->customerStore($payload);
    $customer = User::where('email', $payload['email'])->first();
    $userService->customerCheck($customer->uuid);
    Auth::login($customer->fresh());

    $payload = [
      'number'                       => '4111111111111111',
      'holder_name'                  => 'John Doe',
      'holder_document'              => '12345678901',
      'exp_month'                    => '12',
      'exp_year'                     => '2028',
      'cvv'                          => '123',
      'brand'                        => 'visa',
      'billing_address_is_different' => false,
    ];

    $params = [
      'uuid' => $customer->uuid,
    ];

    $response = $this->post(route('card.store', $params), $payload);
    $response->assertStatus(200);

    $card = $customer->card()->first();
    $this->assertNotNull($card->customer_id);
    $this->assertNotNull($card->card_id);
    
    $params['card_uuid'] = $card->uuid;

    $response = $this->delete(route('card.destroy', $params));
    $response->assertStatus(200);

    $this->assertNull($card->fresh());
  }
}
