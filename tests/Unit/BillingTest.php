<?php

namespace Tests\Unit;

use App\Facades\AppointmentFacade;
use App\Facades\BillingFacade;
use App\Jobs\GenerateOrderToPaymentByUserJob;
use App\Models\Appointment;
use App\Models\Billing;
use App\Models\BillingFail;
use App\Models\Card;
use App\Models\FailedCharges;
use App\Models\Plan;
use App\Models\Room;
use App\Models\Schedule;
use App\Models\Subscription;
use App\Models\User;
use App\Repositories\BillingFailRepository;
use App\Repositories\FailedChargesRepository;
use App\Services\AppointmentService;
use App\Services\BillingService;
use App\Services\CardService;
use App\Services\UserService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use Tests\Mockings\PagarmeMocking;
use Tests\TestCase;

class BillingTest extends TestCase
{
  use RefreshDatabase;

  private PagarmeMocking $mocking;

  public function setUp(): void
  {
    parent::setUp();
    $this->mocking = new PagarmeMocking();
  }

  public function test_when_system_create_a_billing_register()
  {
    $this->withoutExceptionHandling();
    
    $payload = [
      'name'          => 'Luiz Felipe',
      'email'         => 'lf.system@outlook.com',
      'document'      => '11111111111',
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
    Auth::login($customer);

    $cardService = new CardService();

    $payload = [
      'number'                       => '4000000000000010',
      'holder_name'                  => 'John Doe',
      'holder_document'              => '11111111111',
      'exp_month'                    => '12',
      'exp_year'                     => '2028',
      'cvv'                          => '123',
      'brand'                        => 'visa',
      'billing_address_is_different' => false,
    ];
   
    $cardService->store($payload, $customer->uuid);

    Plan::factory()->create();
    $appointment = $this->createAppointmentToCustomer($customer->id);

    $service = new BillingService();
    $billing     = $service->store($customer->id, Appointment::class, $appointment->id, $appointment->value_total);
    $this->assertNotNull($billing);
    $this->assertInstanceOf(Billing::class, $billing);
  }

  public function test_when_system_update_a_billing_register()
  {
    $this->loginWithCustomer();
    $customer       = Auth::user();
    $plan           = Plan::factory()->create();
    $appointment    = $this->createAppointmentToCustomer($customer->id);
    $className      = get_class($appointment);
    $billing        = BillingFacade::store($customer->id, $className, $appointment->id, $appointment->value_total);
    $billingUpdated = BillingFacade::update($billing, 'order-id-test', 'order-code-test');

    $this->assertNotNull($billingUpdated);
    $this->assertInstanceOf(Billing::class, $billingUpdated);
    $this->assertEquals('order-id-test', $billingUpdated->order_id);
    $this->assertEquals('order-code-test', $billingUpdated->order_code);
  }

  public function test_when_system_generate_a_billing_report()
  {
    $this->loginWithCustomer();
    $customer    = Auth::user();
    $plan        = Plan::factory()->create();
    $appointment = $this->createAppointmentToCustomer($customer->id);
    
    $billingAppointment = BillingFacade::store($customer->id, Appointment::class, $appointment->id, $appointment->value_total);
    $billingPlan        = BillingFacade::store($customer->id, Plan::class, $plan->id, $plan->price);
    
    BillingFacade::update($billingAppointment, 'order-id-test', 'order-code-test');
    BillingFacade::update($billingPlan, 'order-id-test', 'order-code-test');

    $report = BillingFacade::generateReport($customer->id, now()->format('Y-m-d'), now()->addDays(1)->format('Y-m-d'));
    $this->assertNotNull($report);
  }

  public function test_when_system_remove_a_billing_register()
  {

    
    $this->loginWithCustomer();
    $customer    = Auth::user();
    $plan        = Plan::factory()->create();
    $appointment = $this->createAppointmentToCustomer($customer->id);
    
    $service = new BillingService();
    $billingAppointment = $service->store($customer->id, Appointment::class, $appointment->id, $appointment->value_total);
    $billingPlan        = $service->store($customer->id, Plan::class, $plan->id, $plan->price);
    
    $service->update($billingAppointment, 'order-id-test', 'order-code-test');
    $service->update($billingPlan, 'order-id-test', 'order-code-test');

    $billingDeleted = $service->destroy($billingAppointment);

    $this->assertNotNull($billingDeleted);
    $this->assertDatabaseMissing('billings', ['id' => $billingAppointment->id]);
  }

  public function test_when_system_create_a_order_to_payment()
  {
    $this->loginWithAdmin();
    $this->withExceptionHandling();

    $this->mocking->createCustomer();
    $this->mocking->createCard();
    $this->mocking->createTokenCard();
    $this->mocking->createOrder();

    $payload = [
      'name'          => 'Luiz Felipe',
      'email'         => 'lf.system@outlook.com',
      'document'      => '11111111111',
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
    Auth::login($customer);

    $payload = [
      'number'                       => '4000000000000010',
      'holder_name'                  => 'John Doe',
      'holder_document'              => '11111111111',
      'exp_month'                    => '12',
      'exp_year'                     => '2028',
      'cvv'                          => '123',
      'brand'                        => 'visa',
      'billing_address_is_different' => false,
    ];
   
    $cardService->store($payload, $customer->uuid);
    $plan = Plan::factory()->create();
    Subscription::factory()->create([
      'user_id'         => $customer->id,
      'plan_id'         => $plan->id,
      'pagarme_card_id' => $customer->fresh()->card()->card_id,
      'price'           => $plan->price,
    ]);

    $appointment = $this->createAppointmentToCustomer($customer->id);
    $service = new BillingService();
    $billingAppointment = $service->store($customer->id, Appointment::class, $appointment->id, $appointment->value_total);
    Billing::where('id', $billingAppointment->id)->update(['created_at' => now()->subDay()]);

    $order = $service->generateOrderToPayment($customer->id, Appointment::class);

    if(BillingFail::where('user_id', $customer->id)->exists()) {
      $this->artisan("billing:fail appointment");
      $this->artisan("billing:fail appointment");
    }
    
    $this->assertIsArray($order);
    $this->assertArrayHasKey('id', $order);
    $this->assertArrayHasKey('status', $order);
    $this->assertArrayHasKey('code', $order);
    $this->assertEquals('paid', $order['status']);
  }

  public function test_when_system_create_a_order_to_payment_and_failed_charge()
  {
    $this->loginWithAdmin();
    $this->withExceptionHandling();

    $this->mocking->createCustomer();
    $this->mocking->createCard();
    $this->mocking->createTokenCard();
    $this->mocking->createOrderFail();

    $payload = [
      'name'          => 'Luiz Felipe',
      'email'         => 'lf.system@outlook.com',
      'document'      => '11111111111',
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
    Auth::login($customer);

    $payload = [
      'number'                       => '4000000000000010',
      'holder_name'                  => 'John Doe',
      'holder_document'              => '11111111111',
      'exp_month'                    => '12',
      'exp_year'                     => '2028',
      'cvv'                          => '123',
      'brand'                        => 'visa',
      'billing_address_is_different' => false,
    ];
   
    $cardService->store($payload, $customer->uuid);
    $plan = Plan::factory()->create();
    Subscription::factory()->create([
      'user_id'         => $customer->id,
      'plan_id'         => $plan->id,
      'pagarme_card_id' => $customer->fresh()->card()->card_id,
      'price'           => $plan->price,
    ]);

    $appointment = $this->createAppointmentToCustomer($customer->id);
    $service = new BillingService();
    $billingAppointment = $service->store($customer->id, Appointment::class, $appointment->id, $appointment->value_total);
    Billing::where('id', $billingAppointment->id)->update(['created_at' => now()->subDay()]);

    $order = $service->generateOrderToPayment($customer->id, Appointment::class);

    if(BillingFail::where('user_id', $customer->id)->exists()) {
      $this->artisan("billing:fail appointment");
      $this->artisan("billing:fail appointment");
    }
    
    $this->assertIsArray($order);
    $this->assertArrayHasKey('id', $order);
    $this->assertArrayHasKey('status', $order);
    $this->assertArrayHasKey('code', $order);
    $this->assertEquals('failed', $order['status']);
    $this->assertEquals($customer->fresh()->status, 'inadimplente');
  }

  public function test_job_when_system_create_a_order_to_payment_and_failed_charge()
  {
    $this->loginWithAdmin();
    $this->withExceptionHandling();

    $this->mocking->createCustomer();
    $this->mocking->createCard();
    $this->mocking->createTokenCard();
    $this->mocking->createOrderFail();

    $payload = [
      'name'          => 'Luiz Felipe',
      'email'         => 'lf.system@outlook.com',
      'document'      => '11111111111',
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
    Auth::login($customer);

    $payload = [
      'number'                       => '4000000000000010',
      'holder_name'                  => 'John Doe',
      'holder_document'              => '11111111111',
      'exp_month'                    => '12',
      'exp_year'                     => '2028',
      'cvv'                          => '123',
      'brand'                        => 'visa',
      'billing_address_is_different' => false,
    ];
   
    $cardService->store($payload, $customer->uuid);
    $plan = Plan::factory()->create();
    Subscription::factory()->create([
      'user_id'         => $customer->id,
      'plan_id'         => $plan->id,
      'pagarme_card_id' => $customer->fresh()->card()->card_id,
      'price'           => $plan->price,
    ]);

    $appointment = $this->createAppointmentToCustomer($customer->id);
    $service = new BillingService();
    $billingAppointment = $service->store($customer->id, get_class($appointment), $appointment->id, $appointment->value_total);
    Billing::where('id', $billingAppointment->id)->update(['created_at' => now()->subDay()]);
    // $service->update($billingAppointment, 'order-id-test', 'order-code-test');

    $order = $service->generateOrderToPayment($customer->id, Appointment::class);

    if(BillingFail::where('user_id', $customer->id)->exists()) {
      $this->artisan("billing:fail appointment");
      $this->artisan("billing:fail appointment");
    }

    $this->assertIsArray($order);
    $this->assertArrayHasKey('id', $order);
    $this->assertArrayHasKey('status', $order);
    $this->assertArrayHasKey('code', $order);
    $this->assertEquals('failed', $order['status']);
    $this->assertEquals($customer->fresh()->status, 'inadimplente');
  }

  public function test_job_when_system_create_a_order_to_payment_and_remove_failed_charge()
  {
    
    $this->loginWithAdmin();
    $this->withExceptionHandling();

    $this->mocking->createCustomer();
    $this->mocking->createCard();
    $this->mocking->createTokenCard();
    $this->mocking->createOrderFail();

    $payload = [
      'name'          => 'Luiz Felipe',
      'email'         => 'lf.system@outlook.com',
      'document'      => '11111111111',
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
    Auth::login($customer);

    $payload = [
      'number'                       => '4000000000000010',
      'holder_name'                  => 'John Doe',
      'holder_document'              => '11111111111',
      'exp_month'                    => '12',
      'exp_year'                     => '2028',
      'cvv'                          => '123',
      'brand'                        => 'visa',
      'billing_address_is_different' => false,
    ];
   
    $cardService->store($payload, $customer->uuid);
    $plan = Plan::factory()->create();
    Subscription::factory()->create([
      'user_id'         => $customer->id,
      'plan_id'         => $plan->id,
      'pagarme_card_id' => $customer->fresh()->card()->card_id,
      'price'           => $plan->price,
    ]);

    $appointment = $this->createAppointmentToCustomer($customer->id);
    $service = new BillingService();
    $billingAppointment = $service->store($customer->id, get_class($appointment), $appointment->id, $appointment->value_total);
    Billing::where('id', $billingAppointment->id)->update(['created_at' => now()->subDay()]);
    // $service->update($billingAppointment, 'order-id-test', 'order-code-test');

    $order = $service->generateOrderToPayment($customer->id, Appointment::class);

    
    $this->assertIsArray($order);
    $this->assertArrayHasKey('id', $order);
    $this->assertArrayHasKey('status', $order);
    $this->assertArrayHasKey('code', $order);
    $this->assertEquals('failed', $order['status']);
  }

  public function test_calculating_schedules_for_transfer_by_partner()
  {
    $this->loginWithAdmin();
    $this->withExceptionHandling();

    $this->mocking->createCustomer();
    $this->mocking->createCard();
    $this->mocking->createTokenCard();
    $this->mocking->createOrder();

    $payload = [
      'name'          => 'Luiz Felipe',
      'email'         => 'lf.system@outlook.com',
      'document'      => '11111111111',
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
    Auth::login($customer);

    $payload = [
      'number'                       => '4000000000000010',
      'holder_name'                  => 'John Doe',
      'holder_document'              => '11111111111',
      'exp_month'                    => '12',
      'exp_year'                     => '2028',
      'cvv'                          => '123',
      'brand'                        => 'visa',
      'billing_address_is_different' => false,
    ];
   
    $cardService->store($payload, $customer->uuid);
    $plan = Plan::factory()->create();
    Subscription::factory()->create([
      'user_id'         => $customer->id,
      'plan_id'         => $plan->id,
      'pagarme_card_id' => $customer->fresh()->card()->card_id,
      'price'           => $plan->price,
    ]);

    $coworking = $this->createCoworkingWithOpeningHoursAndRoom();
    $schedule  = Schedule::factory()->create(['user_id' => $coworking->user_id]);
    $rooms     = Room::where('coworking_id', $coworking->id)->get();

    $appointments[] = $this->createAppointmentToCustomerByScheduleAndRoomId($customer->id, $rooms->first()->id, $schedule->id);
    $appointments[] = $this->createAppointmentToCustomerByScheduleAndRoomId($customer->id, $rooms->first()->id, $schedule->id);
    $appointments[] = $this->createAppointmentToCustomerByScheduleAndRoomId($customer->id, $rooms->first()->id, $schedule->id);
    $partner = User::where('id', $schedule->user_id)->first();

    $totalAppointments = 0;
    foreach ($appointments as $appointment) {
      $appointment->update(['status' => 'finished']);
      $totalAppointments += $appointment->value_total;
      Billing::where('model_id', $appointment->id)->where('model_type', Appointment::class)->update(['created_at' => now()->subDay()]);
    }

    $service = new BillingService();

    $service->generateOrderListToPayment(Appointment::class);
    Queue::after(function (JobProcessed $event) {
      $this->assertEquals(GenerateOrderToPaymentByUserJob::class, $event->job->resolveName());
    });

    $appointmentService  = new AppointmentService();
    $appointments        = $appointmentService->getAppointmentsFinishedByPartner($partner->id, now()->month, now()->year)->get();
    $sumAppointmentsPaid = $appointmentService->getTotalValueOfAppointmentsByPartner($partner->id, now()->month, now()->year);
    $this->assertEquals($totalAppointments, $sumAppointmentsPaid['amount']);
    $this->assertCount(3, $appointments);
  }

  public function test_when_customer_get_billing_historic()
  {

    $this->loginWithAdmin();
    $this->withExceptionHandling();

    $this->mocking->createCustomer();
    $this->mocking->createCard();
    $this->mocking->createTokenCard();
    $this->mocking->createOrder();

    $payload = [
      'name'          => 'Luiz Felipe',
      'email'         => 'lf.system@outlook.com',
      'document'      => '11111111111',
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
    Auth::login($customer);

    $payload = [
      'number'                       => '4000000000000010',
      'holder_name'                  => 'John Doe',
      'holder_document'              => '11111111111',
      'exp_month'                    => '12',
      'exp_year'                     => '2028',
      'cvv'                          => '123',
      'brand'                        => 'visa',
      'billing_address_is_different' => false,
    ];
   
    $cardService->store($payload, $customer->uuid);
    $plan = Plan::factory()->create();
    Subscription::factory()->create([
      'user_id'         => $customer->id,
      'plan_id'         => $plan->id,
      'pagarme_card_id' => $customer->fresh()->card()->card_id,
      'price'           => $plan->price,
    ]);

    $appointment = $this->createAppointmentToCustomer($customer->id);
    $service = new BillingService();
    $billingAppointment = $service->store($customer->id, get_class($appointment), $appointment->id, $appointment->value_total);
    Billing::where('id', $billingAppointment->id)->update(['created_at' => now()->subDay()]);
    // $service->update($billingAppointment, 'order-id-test', 'order-code-test');

    $order = $service->generateOrderToPayment($customer->id, Appointment::class);

    $this->assertIsArray($order);
    $this->assertArrayHasKey('id', $order);
    $this->assertArrayHasKey('status', $order);
    $this->assertArrayHasKey('code', $order);
    $this->assertEquals('paid', $order['status']);

    $customer->email_verified_at = now();
    $customer->save();
    Auth::login($customer->fresh());
    $responseBilling = $this->get(route('billing.index'));
    $this->assertCount(1, $responseBilling->json('data'));
    foreach ($responseBilling->json('data') as $billing) {
      $this->assertInstanceOf(Appointment::class, new $billing['model_type']());
    }
  }

  public function test_when_customer_get_a_billing_specific()
  {

    $this->loginWithAdmin();
    $this->withExceptionHandling();

    $this->mocking->createCustomer();
    $this->mocking->createCard();
    $this->mocking->createTokenCard();
    $this->mocking->createOrder();

    $payload = [
      'name'          => 'Luiz Felipe',
      'email'         => 'lf.system@outlook.com',
      'document'      => '11111111111',
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
    Auth::login($customer);

    $payload = [
      'number'                       => '4000000000000010',
      'holder_name'                  => 'John Doe',
      'holder_document'              => '11111111111',
      'exp_month'                    => '12',
      'exp_year'                     => '2028',
      'cvv'                          => '123',
      'brand'                        => 'visa',
      'billing_address_is_different' => false,
    ];
   
    $cardService->store($payload, $customer->uuid);
    $plan = Plan::factory()->create();
    Subscription::factory()->create([
      'user_id'         => $customer->id,
      'plan_id'         => $plan->id,
      'pagarme_card_id' => $customer->fresh()->card()->card_id,
      'price'           => $plan->price,
    ]);

    $appointment = $this->createAppointmentToCustomer($customer->id);
    $service = new BillingService();
    $billingAppointment = $service->store($customer->id, get_class($appointment), $appointment->id, $appointment->value_total);
    Billing::where('id', $billingAppointment->id)->update(['created_at' => now()->subDay()]);
    // $service->update($billingAppointment, 'order-id-test', 'order-code-test');

    $order = $service->generateOrderToPayment($customer->id, Appointment::class);

    $this->assertIsArray($order);
    $this->assertArrayHasKey('id', $order);
    $this->assertArrayHasKey('status', $order);
    $this->assertArrayHasKey('code', $order);
    $this->assertEquals('paid', $order['status']);

    $customer->email_verified_at = now();
    $customer->save();
    Auth::login($customer->fresh());
    $responseBilling = $this->get(route('billing.show', ["uuid" => $billingAppointment->uuid]));
    $responseBilling->assertStatus(200);
  }

  public function plan_charge()
  {
    $this->loginWithAdmin();
    $this->withExceptionHandling();

    // $this->mocking->createCustomer();
    // $this->mocking->createCard();
    // $this->mocking->createTokenCard();
    // $this->mocking->createOrder();

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
    Auth::login($customer);

    $payload = [
      'number'                       => '4111111111111111',
      'holder_name'                  => 'John Doe',
      // 'holder_document'              => '12345678901',
      'holder_document'              => '11111111111',
      'exp_month'                    => '12',
      'exp_year'                     => '2028',
      'cvv'                          => '123',
      'brand'                        => 'visa',
      'billing_address_is_different' => false,
    ];
   
    $card = $cardService->store($payload, $customer->uuid);
    $plan = Plan::factory()->create();
    $subscription = Subscription::factory()->create([
      'user_id'         => $customer->id,
      'plan_id'         => $plan->id,
      'pagarme_card_id' => $card->card_id,
      'price'           => $plan->price,
    ]);

    $appointment = $this->createAppointmentToCustomer($customer->id);
    $billingService = new BillingService();
    $billingAppointment1 = $billingService->store($customer->id, get_class($appointment), $appointment->id, $appointment->value_total);
    $billingAppointment2 = $billingService->store($customer->id, get_class($appointment), $appointment->id, $appointment->value_total);
    // $billingAppointment = BillingFacade::store($customer->id, Subscription::class, $appointment->id, $appointment->value_total);


    Billing::where('id', $billingAppointment1->id)->update(['created_at' => now()->subDay()]);
    Billing::where('id', $billingAppointment2->id)->update(['created_at' => now()->subDay()]);
    
    // BillingFacade::update($billingAppointment, 'order-id-test', 'order-code-test');
    $billingService->generateOrderToPayment($customer->id, Subscription::class);
    $billingService->generateOrderToPayment($customer->id, Appointment::class);
    $billingService->generateOrderToPayment($customer->id, Appointment::class);
    $billingService->generateOrderToPayment($customer->id, Subscription::class);

    // dd($order);
    $billings = Billing::where('user_id', $customer->id)->get();
    $billingsFailed = BillingFail::where('user_id', $customer->id)->get();

    $billingFailRepository = new BillingFailRepository();
    $billingSubscriptionFails = $billingFailRepository->getFailByUserAndModelType($customer->id, Subscription::class);
    $billingAppointmentFails = $billingFailRepository->getFailByUserAndModelType($customer->id, Appointment::class);

    $this->httpFakerReset();


    // $this->mocking->createOrder();
    // run command
    $this->artisan("billing:fail appointment");
    $this->artisan("billing:fail appointment");
    $this->artisan("billing:fail subscription");

    // dd($billingsFailed->fresh()->toArray(), $billings->fresh()->toArray(), $customer->fresh()->toArray());

  }
}
