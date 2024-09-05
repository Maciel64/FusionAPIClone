<?php

namespace Tests\Feature;

use App\Facades\BillingFacade;
use App\Models\Appointment;
use App\Models\Billing;
use App\Models\Plan;
use App\Models\Room;
use App\Models\Schedule;
use App\Models\Subscription;
use App\Models\Transfer;
use App\Models\User;
use App\Services\AppointmentService;
use App\Services\BillingService;
use App\Services\CardService;
use App\Services\TransferService;
use App\Services\UserService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Tests\Mockings\PagarmeMocking;
use Tests\TestCase;

class TransferTest extends TestCase
{

  private PagarmeMocking $mocking;

  public function setUp(): void
  {
    parent::setUp();
    $this->mocking = new PagarmeMocking();
  }

  public function test_calculating_schedules_for_transfer_by_partner_and_generate_transfer_list()
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
      'holder_document'              => '12345678901',
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
    foreach ($appointments as $key => $appointment) {
      $appointment->update(['status' => 'finished']);
      $totalAppointments += $appointment->value_total;
      Billing::where('model_id', $appointment->id)->where('model_type', Appointment::class)->update(['created_at' => now()->subDay()]);
    }

    $service = new BillingService();
    $service->generateOrderListToPayment(Appointment::class);

    $appointmentService  = new AppointmentService();
    $appointments        = $appointmentService->getAppointmentsFinishedByPartner($partner->id, now()->month, now()->year)->get();
    $sumAppointmentsPaid = $appointmentService->getTotalValueOfAppointmentsByPartner($partner->id, now()->month, now()->year);
    $this->assertEquals($totalAppointments, $sumAppointmentsPaid['amount']);
    $this->assertCount(3, $appointments);

    $userService     = new UserService();
    $transferService = new TransferService();
    
    $transferService->generateTransfers();
    $transfers = Transfer::where('partner_id', $partner->id)->get();
    $this->assertEquals($totalAppointments, $transfers[0]->amount);
  }

  public function test_when_admin_send_receipt_upload()
  {
    $this->loginWithAdmin();
    $this->withExceptionHandling();

    $transfer  = Transfer::factory()->create();
    $file      = UploadedFile::fake()->image('receipt.jpg');
    $params = ['uuid'  => $transfer->uuid];

    $response = $this->post(route('transfer.upload.receipt', $params), [
      'receipt' => $file,
    ]);

    $response->assertStatus(200);
    $this->assertTrue(Storage::disk('receipts')->exists($response->json('data.receipt_name')));
  }

  public function test_when_a_partner_download_receipt()
  {
    $this->loginWithPartner();

    $transfer = Transfer::factory()->create([
      'partner_id' => auth()->user()->id
    ]);

    $file     = UploadedFile::fake()->image('receipt.jpg');
    $params   = ['uuid'  => $transfer->uuid];
    $response = $this->post(route('transfer.upload.receipt', $params), ['receipt' => $file]);
    $params   = ['uuid' => $transfer->uuid];
    $response = $this->get(route('transfer.download.receipt', $params));
    $response->assertStatus(200);
  }

  public function test_update_transfer()
  {
    $this->loginWithPartner();

    $transfer = Transfer::factory()->create([
      'partner_id' => auth()->user()->id
    ]);

    $params = ['uuid' => $transfer->uuid];

    $payload = [
      'status' => 'paid',
      'notes'  => 'test',
      'discount' => 10,
    ];

    $response = $this->put(route('transfer.update', $params), $payload);
    $response->assertStatus(200);
    $this->assertEquals($payload['status'], $response->json('data.status'));
  }

  public function test_show_transfer()
  {
    $this->loginWithPartner();

    $transfer = Transfer::factory()->create([
      'partner_id' => auth()->user()->id
    ]);

    $params   = ['uuid' => $transfer->uuid];
    $response = $this->get(route('transfer.show', $params));
    $response->assertStatus(200);
    $this->assertEquals($transfer->uuid, $response->json('data.uuid'));
  }

  public function test_search_transfers()
  {
    $this->loginWithPartner();

    $transfer = Transfer::factory(3)->create([
      'partner_id' => auth()->user()->id
    ]);

    $params = ['month' => now()->month, 'year' => now()->year];

    $response = $this->post(route('fusion.transfer.search', $params));
    $response->assertStatus(200);
    $this->assertCount(3, $response->json('data'));
  }

  public function test_index_transfers()
  {
    $this->loginWithPartner();

    $transfer = Transfer::factory(3)->create([
      'partner_id' => auth()->user()->id
    ]);

    $payload = [
      'month' => now()->month,
      'year'  => now()->year,
    ];

    $response = $this->post(route('partner.transfer.search'), $payload);
    $response->assertStatus(200);
    $this->assertCount(3, $response->json('data'));
  }


}
