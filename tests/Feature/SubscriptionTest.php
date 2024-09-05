<?php

namespace Tests\Feature;

use App\Models\Billing;
use App\Models\Card;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\SubscriptionUpdateRequest;
use App\Models\User;
use App\Repositories\SubscriptionUpdateRequestRepository;
use App\Services\BillingService;
use App\Services\CardService;
use App\Services\PlanService;
use App\Services\SubscriptionService;
use Database\Seeders\BillingSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Tests\Mockings\PagarmeMocking;
use Tests\TestCase;

class SubscriptionTest extends TestCase
{
  use refreshDatabase;

  private $mocking;

  public function setUp(): void
  {
    parent::setUp();
    $this->mocking = new PagarmeMocking();
  }

  public function test_subscription_store()
  {
    $this->withoutExceptionHandling();
    $this->loginWithCustomer();
    $this->mocking->createPlanSubscription();

    Card::factory()->create([
      'user_id' => Auth::user()->id,
    ]);

    $plan    = Plan::factory()->create();
    $payload = ['plan_uuid' => $plan->uuid];
    $params  = ['uuid' => Auth::user()->uuid];

    $response = $this->post(route('subscription.store', $params), $payload);
    $response->assertStatus(200);
  }

  public function test_subscription_update()
  {
    // $this->withoutExceptionHandling();
    $this->loginWithCustomer();

    $subscription = Subscription::factory()->create([
      'user_id' => Auth::user()->id,
    ]);

    // $currentPlan = Plan::find($subscription->plan_id);
    $plan    = Plan::factory()->create();
    $payload = ['plan_uuid' => $plan->uuid];
    $params  = [
      'uuid'              => Auth::user()->uuid,
      'subscription_uuid' => $subscription->uuid,
    ];
    $response = $this->put(route('subscription.update', $params), $payload);
    $response->assertStatus(200);

    $subscriptionUpdateRequest = new SubscriptionUpdateRequestRepository();
    $subRequest = $subscriptionUpdateRequest->findBySubscriptionId($subscription->id);

    $this->assertEquals($subscription->id, $subRequest->subscription_id);
    $this->assertEquals($plan->id, $subRequest->plan_id);
    $this->assertNotEquals($subscription->plan_id, $subRequest->plan_id);
  }

  public function test_subscription_cancel()
  {
    $this->mocking->cancelSubscription();
    $this->withoutExceptionHandling();  
    $subscription         = Subscription::factory()->create();
    $service              = new SubscriptionService();
    $subscriptionCanceled = $service->cancel($subscription->uuid);
    $this->assertEquals('canceled', $subscriptionCanceled->status);
  }

  private function customerPayload()
  {
    return [
      'name'          => 'Luiz F',
      'email'         => 'lf.system@outlook.com',
      'document'      => '42520197889',
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

  }

  private function cardPayload()
  {
    return [
      'number'                       => '4000000000000077',
      'holder_name'                  => 'John Doe',
      'holder_document'              => '42520197889',
      'exp_month'                    => '12',
      'exp_year'                     => '2028',
      'cvv'                          => '612',
      'brand'                        => 'visa',
      'billing_address_is_different' => false,
    ];  
  }

  public function test_create_subscription()
  {

    // $value[] = number_format(80.45, 2, '', '');
    // $value[] = number_format(80.00, 2, '', '');
    // $value[] = number_format(75, 2, '', '');
    // $value[] = number_format(80.45, 2, '', '');
    // $value[] = number_format(8078.0, 2, '', '');


    // dd($value);

    $this->withoutExceptionHandling();
    $this->loginWithAdmin();

    $admin       = User::find(auth()->user()->id);
    $planService = new PlanService();
    $plan        = $planService->store([
      'name'        => 'Test Plan',
      'price'       => 80.00,
      'description' => 'Test Plan Description'
    ]);

    $customerPayload  = $this->customerPayload();
    $customerResponse = $this->post(route('customer.store'), $customerPayload);
    $payload          = ['uuid' => $customerResponse->json('data.uuid')];
    $response         = $this->post(route('customer.check'), $payload);
    
    $customer = User::where('uuid', $customerResponse->json('data.uuid'))->first();
    // $planService->attach($plan->uuid, $customer->uuid);
    $customer = $this->customerCheck($customer);

    $cardPayload = $this->cardPayload();
    $cardService = new CardService();
    $card = $cardService->store($cardPayload, $customer->uuid);

    $cardPayload['number'] = '4000000000000010';
    $cardPayload['cvv'] = '123';
    $cardPayload['holder_name'] = 'Luiz F Lima';

    $cardUpdate = $cardService->update($card->uuid, $cardPayload);

    $subscriptionService = new SubscriptionService();
    $subscription = $subscriptionService->store($customer->fresh()->uuid, $plan->uuid);

    $this->assertEquals($plan->id, $subscription->plan_id);
    
  }

  private function getDayByBillingConfig()
  {
    switch (config('settings.billing_type')) {
      case 'daily':
        return now()->subDay();
        break;
      
      case 'monthly':
        return  now()->day(20)->subMonth();
        break;
    }

  }
}
