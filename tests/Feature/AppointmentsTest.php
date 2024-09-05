<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Billing;
use App\Models\Room;
use App\Models\Schedule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class AppointmentsTest extends TestCase
{

  use RefreshDatabase;

  public function setUp():void
  {
    parent::setUp();
  }

  public function test_example()
  {
    $this->assertTrue(true);
  }

  public function test_app_appointments_index()
  {
    $coworking = $this->createCoworkingWithOpeningHoursAndRoom();
    $partner = Auth::user();
    $schedule = Schedule::factory()->create(['user_id' => $partner->id]);
    $rooms = Room::where('coworking_id', $coworking->id)->get();
    $this->loginWithCustomer();

    Appointment::factory(2)->create([
      'customer_id' => Auth::user()->id,
      'room_id' => $rooms->first()->id,
      'schedule_id' => $schedule->id,
    ]);

    $params = [
      'dateInit' => '2022-11-19',
      'dateEnd' => '2022-11-20',
    ];

    $response = $this->get(route('appointment.index', $params));
    $response->assertStatus(200);
    $response->assertJsonCount(2, 'data');
  }

  // public function test_partner_appointments_index()
  // {

  //   $coworking = $this->createCoworkingWithOpeningHoursAndRoom();
  //   $partner = Auth::user();
  //   $schedule = Schedule::factory()->create(['user_id' => $partner->id]);
  //   $rooms = Room::where('coworking_id', $coworking->id)->get();
  //   $this->loginWithCustomer();

  //   Appointment::factory(2)->create([
  //     'customer_id' => Auth::user()->id,
  //     'room_id' => $rooms->first()->id,
  //     'schedule_id' => $schedule->id,
  //   ]);

  //   $params = [
  //     'schedule_uuid' => $partner->schedule->uuid,
  //     'date' => '2022-11-19',
  //   ];

  //   Auth::login($partner);
  //   $response = $this->get(route('appointment.index', $params));
  //   $response->assertStatus(200);
  // }
  
  public function test_app_appointments_show()
  {

    $coworking = $this->createCoworkingWithOpeningHoursAndRoom();
    $partner = Auth::user();
    $schedule = Schedule::factory()->create(['user_id' => $partner->id]);
    $rooms = Room::where('coworking_id', $coworking->id)->get();
    $this->loginWithCustomer();

    $appointment = Appointment::factory(2)->create([
      'customer_id' => Auth::user()->id,
      'room_id' => $rooms->first()->id,
      'schedule_id' => $schedule->id,
    ]);

    $params = [
      'customer_uuid' => Auth::user()->uuid,
      'uuid' => $appointment[0]->uuid
    ];

    $response = $this->get(route('appointment.show', $params));
    $response->assertStatus(200);
    $response->assertJsonFragment([
      'uuid' => $appointment[0]->uuid
    ]);
  }

  public function test_app_appointments_store()
  {

    $coworking = $this->createCoworkingWithOpeningHoursAndRoom();
    $partner   = Auth::user();
    $schedule  = Schedule::factory()->create(['user_id' => $partner->id]);
    $rooms     = Room::where('coworking_id', $coworking->id)->get();
    $this->loginWithCustomer();

    Appointment::factory()->create([
      'schedule_id' => $schedule->id,
      'customer_id' => Auth::user()->id,
      'room_id'     => $rooms->first()->id,
      'time_init'   => '2022-11-20 12:00:00',
      'time_end'    => '2022-11-20 14:00:00',
    ]);

    Appointment::factory()->create([
      'schedule_id' => $schedule->id,
      'customer_id' => Auth::user()->id,
      'room_id'     => $rooms->first()->id,
      'time_init'   => '2022-11-20 09:00:00',
      'time_end'    => '2022-11-20 12:00:00',
    ]);

    $payload = [
      'patient_name' => 'Teste',
      'patient_phone' => '999999999',
      'room_uuid' => $rooms->first()->uuid,
      'time_init' => '2022-11-20 14:00:00',
      'time_end'  => '2022-11-20 17:00:00',
    ];

    $params = ['customer_uuid' => Auth::user()->uuid];
    $response = $this->post(route('appointment.store', $params), $payload);

    $response->assertStatus(200);
    $response->assertJsonFragment([
      'time_init' => '2022-11-20 14:00',
      'time_end'  => '2022-11-20 17:00',
    ]);
  }

  // public function test_appointments_update()
  // {
  //   $response = $this->put(route('appointments.update', ['uuid' => 'uuid']), []);
  //   $response->assertStatus(200);
  // }

  public function test_app_appointments_destroy()
  {
    $coworking = $this->createCoworkingWithOpeningHoursAndRoom();
    $partner   = Auth::user();
    $schedule  = Schedule::factory()->create(['user_id' => $partner->id]);
    $rooms     = Room::where('coworking_id', $coworking->id)->get();
    $this->loginWithCustomer();

    Appointment::factory()->create([
      'schedule_id' => $schedule->id,
      'customer_id' => Auth::user()->id,
      'room_id'     => $rooms->first()->id,
      'time_init'   => '2022-11-20 12:00:00',
      'time_end'    => '2022-11-20 14:00:00',
    ]);

    $appointment = Appointment::factory()->create([
      'schedule_id' => $schedule->id,
      'customer_id' => Auth::user()->id,
      'room_id'     => $rooms->first()->id,
      'time_init'   => '2022-11-20 09:00:00',
      'time_end'    => '2022-11-20 12:00:00',
    ]);

    $params = [
      'uuid' => $appointment->uuid
    ];

    $response = $this->delete(route('appointment.destroy', $params));
    $response->assertStatus(200);
    $this->assertDatabaseMissing('appointments', [
      'uuid' => $appointment->uuid
    ]);
  }

  public function test_update_status_checkin()
  {
    $coworking = $this->createCoworkingWithOpeningHoursAndRoom();
    $partner   = Auth::user();
    $schedule  = Schedule::factory()->create(['user_id' => $partner->id]);
    $rooms     = Room::where('coworking_id', $coworking->id)->get();
    $this->loginWithCustomer();

    Appointment::factory()->create([
      'schedule_id' => $schedule->id,
      'customer_id' => Auth::user()->id,
      'room_id'     => $rooms->first()->id,
      'time_init'   => '2022-11-20 12:00:00',
      'time_end'    => '2022-11-20 14:00:00',
    ]);

    $appointment = Appointment::factory()->create([
      'schedule_id' => $schedule->id,
      'customer_id' => Auth::user()->id,
      'room_id'     => $rooms->first()->id,
      'time_init'   => '2022-11-20 09:00:00',
      'time_end'    => '2022-11-20 12:00:00',
    ]);

    $params = [
      'uuid' => $appointment->uuid
    ];

    $payload = [
      'status' => 'checkin',
      'date_time' => '2022-11-20 09:35:00'
    ];

    $response = $this->post(route('appointment.update.status', $params), $payload);
    $response->assertStatus(200);
    $this->assertEquals('2022-11-20 09:35:00', $appointment->fresh()->checkin_at);
    $this->assertEquals('checkin', $appointment->fresh()->status);
    $this->assertDatabaseHas('appointments', [
      'uuid' => $appointment->uuid,
      'status' => 'checkin',
      'checkin_at' => '2022-11-20 09:35:00'
    ]);
  }
  

  public function test_update_status_checkout()
  {
    $coworking = $this->createCoworkingWithOpeningHoursAndRoom();
    $partner   = Auth::user();
    $schedule  = Schedule::factory()->create(['user_id' => $partner->id]);
    $rooms     = Room::where('coworking_id', $coworking->id)->get();
    $this->loginWithCustomer();

    Appointment::factory()->create([
      'schedule_id' => $schedule->id,
      'customer_id' => Auth::user()->id,
      'room_id'     => $rooms->first()->id,
      'time_init'   => '2022-11-20 12:00:00',
      'time_end'    => '2022-11-20 14:00:00',
    ]);

    $appointment = Appointment::factory()->create([
      'schedule_id' => $schedule->id,
      'customer_id' => Auth::user()->id,
      'room_id'     => $rooms->first()->id,
      'time_init'   => '2022-11-20 09:00:00',
      'time_end'    => '2022-11-20 12:00:00',
    ]);

    $params = [
      'uuid' => $appointment->uuid
    ];

    $payload = [
      'status' => 'checkout',
      'date_time' => '2022-11-20 12:00:00'
    ];

    $response = $this->post(route('appointment.update.status', $params), $payload);
    $response->assertStatus(200);
    $this->assertEquals('2022-11-20 12:00:00', $appointment->fresh()->checkout_at);
    $this->assertEquals('checkout', $appointment->fresh()->status);
    $this->assertDatabaseHas('appointments', [
      'uuid' => $appointment->uuid,
      'status' => 'checkout',
      'checkout_at' => '2022-11-20 12:00:00'
    ]);
  }

  public function test_update_status_finished()
  {
    $coworking = $this->createCoworkingWithOpeningHoursAndRoom();
    $partner   = Auth::user();
    $schedule  = Schedule::factory()->create(['user_id' => $partner->id]);
    $rooms     = Room::where('coworking_id', $coworking->id)->get();
    $this->loginWithCustomer();

    Appointment::factory()->create([
      'schedule_id' => $schedule->id,
      'customer_id' => Auth::user()->id,
      'room_id'     => $rooms->first()->id,
      'time_init'   => '2022-11-20 12:00:00',
      'time_end'    => '2022-11-20 14:00:00',
    ]);

    $appointment = Appointment::factory()->create([
      'schedule_id' => $schedule->id,
      'customer_id' => Auth::user()->id,
      'room_id'     => $rooms->first()->id,
      'time_init'   => '2022-11-20 09:00:00',
      'time_end'    => '2022-11-20 12:00:00',
    ]);

    $params = [
      'uuid' => $appointment->uuid
    ];

    $payload = [
      'status' => 'finished',
      'date_time' => '2022-11-20 18:00:00'
    ];

    $response = $this->post(route('appointment.update.status', $params), $payload);
    $response->assertStatus(200);
    $this->assertEquals('2022-11-20 18:00:00', $appointment->fresh()->finished_at);
    $this->assertEquals('finished', $appointment->fresh()->status);
    $this->assertDatabaseHas('appointments', [
      'uuid' => $appointment->uuid,
      'status' => 'finished',
      'finished_at' => '2022-11-20 18:00:00'
    ]);
  }

  public function test_update_status_canceled()
  {
    $coworking = $this->createCoworkingWithOpeningHoursAndRoom();
    $partner   = Auth::user();
    $schedule  = Schedule::factory()->create(['user_id' => $partner->id]);
    $rooms     = Room::where('coworking_id', $coworking->id)->get();
    $this->loginWithCustomer();

    Appointment::factory()->create([
      'schedule_id' => $schedule->id,
      'customer_id' => Auth::user()->id,
      'room_id'     => $rooms->first()->id,
      'time_init'   => '2022-11-20 12:00:00',
      'time_end'    => '2022-11-20 14:00:00',
    ]);

    $appointment = Appointment::factory()->create([
      'schedule_id' => $schedule->id,
      'customer_id' => Auth::user()->id,
      'room_id'     => $rooms->first()->id,
      'time_init'   => '2022-11-20 09:00:00',
      'time_end'    => '2022-11-20 12:00:00',
    ]);

    $params = [
      'uuid' => $appointment->uuid
    ];

    $payload = [
      'status' => 'canceled',
      'date_time' => '2022-11-20 18:00:00'
    ];

    $response = $this->post(route('appointment.update.status', $params), $payload);
    $response->assertStatus(200);
    $this->assertEquals('2022-11-20 18:00:00', $appointment->fresh()->canceled_at);
    $this->assertEquals('canceled', $appointment->fresh()->status);
    $this->assertDatabaseHas('appointments', [
      'uuid'        => $appointment->uuid,
      'status'      => 'canceled',
      'canceled_at' => '2022-11-20 18:00:00'
    ]);
  }
  
  public function test_when_customer_create_a_appointment_and_partner_finalize_appointment()
  {
    $this->withoutExceptionHandling();
    $coworking = $this->createCoworkingWithOpeningHoursAndRoom();
    $partner   = Auth::user();
    $schedule  = Schedule::factory()->create(['user_id' => $partner->id]);
    $rooms     = Room::where('coworking_id', $coworking->id)->get();
    $this->loginWithCustomer();
    $customer = Auth::user();

    $appointment = Appointment::factory()->create([
      'schedule_id' => $schedule->id,
      'customer_id' => Auth::user()->id,
      'room_id'     => $rooms->first()->id,
      'time_init'   => '2022-11-20 12:00:00',
      'time_end'    => '2022-11-20 14:00:00',
    ]);

    Appointment::factory()->create([
      'schedule_id' => $schedule->id,
      'customer_id' => Auth::user()->id,
      'room_id'     => $rooms->first()->id,
      'time_init'   => '2022-11-20 09:00:00',
      'time_end'    => '2022-11-20 12:00:00',
    ]);

    $params = ['uuid' => $appointment->uuid];

    $payload = [
      'date_time' => '2022-11-20 18:00:00',
      'status'    => 'finished',
    ];
  
    $response = $this->post(route('appointment.update.status', $params), $payload);
    $response->assertStatus(200);
    $this->assertEquals('2022-11-20 18:00:00', $appointment->fresh()->finished_at);
    $this->assertEquals('finished', $appointment->fresh()->status);
    $this->assertDatabaseHas('appointments', [
      'uuid' => $appointment->uuid,
      'status' => 'finished',
      'finished_at' => '2022-11-20 18:00:00'
    ]);

    $this->assertDatabaseHas('billings', [
      'user_id'    => $customer->id,
      'model_type' => get_class($appointment),
      'model_id'   => $appointment->id,
    ]);
  }

  public function test_when_partner_search_appointments_by_date()
  {
    $this->withoutExceptionHandling();
    $coworking = $this->createCoworkingWithOpeningHoursAndRoom();
    $partner   = Auth::user();
    $schedule  = Schedule::factory()->create(['user_id' => $partner->id]);
    $rooms     = Room::where('coworking_id', $coworking->id)->get();
    $this->loginWithCustomer();
    $customer = Auth::user();

    $appointment = Appointment::factory()->create([
      'schedule_id' => $schedule->id,
      'customer_id' => Auth::user()->id,
      'room_id'     => $rooms->first()->id,
      'time_init'   => '2022-11-20 12:00:00',
      'time_end'    => '2022-11-20 14:00:00',
    ]);

    Appointment::factory()->create([
      'schedule_id' => $schedule->id,
      'customer_id' => Auth::user()->id,
      'room_id'     => $rooms->first()->id,
      'time_init'   => '2022-12-20 09:00:00',
      'time_end'    => '2022-12-20 12:00:00',
    ]);

    $payload = [
      'date' => '2022-11-20',
      'filter' => 'all',
      // 'uuid' => '',
      'schedule_uuid' => $schedule->uuid,
    ];

    $response = $this->post(route('appointment.search'), $payload);
    $response->assertStatus(200);
    $this->assertCount(1, $response->json('data.data'));
  }

  public function test_when_partner_search_appointments_by_room()
  {
    $this->withoutExceptionHandling();
    $coworking = $this->createCoworkingWithOpeningHoursAndRoom();
    $partner   = Auth::user();
    $schedule  = Schedule::factory()->create(['user_id' => $partner->id]);
    $rooms     = Room::where('coworking_id', $coworking->id)->get();
    $this->loginWithCustomer();
    $customer = Auth::user();

    $appointment = Appointment::factory()->create([
      'schedule_id' => $schedule->id,
      'customer_id' => Auth::user()->id,
      'room_id'     => $rooms->first()->id,
      'time_init'   => '2022-11-20 12:00:00',
      'time_end'    => '2022-11-20 14:00:00',
    ]);

    Appointment::factory()->create([
      'schedule_id' => $schedule->id,
      'customer_id' => Auth::user()->id,
      'room_id'     => $rooms->first()->id,
      'time_init'   => '2022-12-20 09:00:00',
      'time_end'    => '2022-12-20 12:00:00',
    ]);

    $payload = [
      'date'          => '2022-11-20',
      'filter'        => 'all',
      'uuid'          => $rooms->first()->uuid,
      'schedule_uuid' => $schedule->uuid,
    ];

    $response = $this->post(route('appointment.search'), $payload);
    $response->assertStatus(200);
    $this->assertCount(1, $response->json('data.data'));
  }


  public function test_when_partner_search_appointments_by_status()
  {
    $this->withoutExceptionHandling();
    $coworking = $this->createCoworkingWithOpeningHoursAndRoom();
    $partner   = Auth::user();
    $schedule  = Schedule::factory()->create(['user_id' => $partner->id]);
    $rooms     = Room::where('coworking_id', $coworking->id)->get();
    $this->loginWithCustomer();
    $customer = Auth::user();

    $appointment = Appointment::factory()->create([
      'schedule_id' => $schedule->id,
      'customer_id' => Auth::user()->id,
      'room_id'     => $rooms->first()->id,
      'time_init'   => '2022-11-20 12:00:00',
      'time_end'    => '2022-11-20 14:00:00',
    ]);

    $appointmentFinished = Appointment::factory()->create([
      'schedule_id' => $schedule->id,
      'customer_id' => Auth::user()->id,
      'room_id'     => $rooms->first()->id,
      'status'      => 'finished',
      'time_init'   => '2022-12-20 09:00:00',
      'time_end'    => '2022-12-20 12:00:00',
    ]);

    $payload = [
      'date'          => '2022-12-20',
      'filter'        => 'finished',
      'schedule_uuid' => $schedule->uuid,
    ];

    $response = $this->post(route('appointment.search'), $payload);
    $response->assertStatus(200);
    $this->assertCount(1, $response->json('data.data'));
    $this->assertEquals('finished', $response->json('data.data.0.status'));
    
    $this->assertEquals($appointmentFinished->uuid, $response->json('data.data.0.uuid'));
    

  }
}
