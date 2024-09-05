<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Appointment;
use App\Models\Category;
use App\Models\CoworkingOpeningHours;
use App\Models\Room;
use App\Models\Schedule;
use App\Repositories\AddressRepository;
use Database\Factories\CoworkingOpeningHoursFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class RoomTest extends TestCase
{

  use RefreshDatabase ;

  public function setUp(): void
  {
    parent::setUp();
  }

  public function test_list_room_related_with_coworking()
  {
    $this->withoutExceptionHandling();
    $coworking = $this->loginWithPartner();
    Room::factory(2)->create(['coworking_id' => $coworking->id]);
    $params = [
      'user_uuid' => Auth::user()->uuid,
      'coworking_uuid' => $coworking->uuid
    ];
    $response = $this->get(route('coworking.room.index', $params));
    $response->assertStatus(200);
    $this->assertCount(2, $response->json('data'));
  }

  public function test_store_room_related_with_coworking()
  {
    // $this->withoutExceptionHandling();
    $coworking = $this->loginWithPartner();
    $params = [
      'user_uuid' => Auth::user()->uuid,
      'coworking_uuid' => $coworking->uuid
    ];

    $payload = [
      'name' => 'Room 1',
      'number' => '1A',
      'description' => 'Room 1 description',
      'price_per_minute' => 5.8,
    ];

    $response = $this->post(route('coworking.room.store', $params), $payload);
    $response->assertStatus(200);
  }

  public function test_when_get_a_room_with_address()
  {
    $this->withoutExceptionHandling();
    $coworking = $this->loginWithPartner();
    Schedule::factory()->create(['user_id' => $coworking->user_id]);
    $data = [
      'uuid' => $coworking->uuid,
      'type' => 'coworking',
      'line_1' => 'Rua los angeles',
      'line_2' => '',
      'country' => 'BR',
      'city' => 'Itaquaquecetuba',
      'state' => 'SP',
      'zip_code' => '13200-000',
    ];

    $addressRepository = new AddressRepository();
    $addressRepository->store($data);

    $params = [
      'user_uuid' => Auth::user()->uuid,
      'coworking_uuid' => $coworking->uuid
    ];

    $payload = [
      'name' => 'Room 1',
      'number' => '1A',
      'description' => 'Room 1 description',
      'price_per_minute' => 5.8,
    ];

    $response = $this->post(route('coworking.room.store', $params), $payload);
    $response->assertStatus(200);
  }

  public function test_store_room_related_with_coworking_and_opening_hours()
  {
    // $this->withoutExceptionHandling();
    $coworking = $this->loginWithPartner();

    CoworkingOpeningHours::factory()->create(['coworking_id' => $coworking->id,'day_of_week' => 'monday']);
    CoworkingOpeningHours::factory()->create(['coworking_id' => $coworking->id,'day_of_week' => 'tuesday']);
    CoworkingOpeningHours::factory()->create(['coworking_id' => $coworking->id,'day_of_week' => 'wednesday']);
    
    $params = [
      'user_uuid' => Auth::user()->uuid,
      'coworking_uuid' => $coworking->uuid
    ];

    $payload = [
      'name' => 'Room 1',
      'number' => '1A',
      'description' => 'Room 1 description',
      'price_per_minute' => 5.8,
    ];

    $response = $this->post(route('coworking.room.store', $params), $payload);
    $response->assertStatus(200);
  }

  // public function test_show_room_related_with_coworking()
  // {
  //   $this->withoutExceptionHandling();
  //   $coworking = $this->loginWithPartner();
  //   $room = Room::factory()->create(['coworking_id' => $coworking->id]);
  //   $params = [
  //     'user_uuid' => Auth::user()->uuid,
  //     'coworking_uuid' => $coworking->uuid, 'uuid' => $room->uuid
  //   ];
  //   $response = $this->get(route('coworking.room.show', $params));
  //   $response->assertStatus(200);
  //   $this->assertEquals(__('response.show.success', ['resource' => 'Room']), $response->json('message'));
  // }

  public function test_update_room_related_with_coworking()
  {
    $this->withoutExceptionHandling();
    $coworking = $this->loginWithPartner();
    $room = Room::factory()->create(['coworking_id' => $coworking->id]);
    $params = [
      'user_uuid' => Auth::user()->uuid,
      'coworking_uuid' => $coworking->uuid, 
      'uuid' => $room->uuid
    ];

    $payload = [
      'name' => 'Room 1',
      'number' => '1A',
      'description' => 'Room 1 description'
    ];

    $response = $this->put(route('coworking.room.update', $params), $payload);
    $response->assertStatus(200);
    $this->assertEquals(__('response.update.success', ['resource' => 'Room']), $response->json('message'));
  }

  public function test_destroy_room_related_with_coworking()
  {
    $this->withoutExceptionHandling();
    $coworking = $this->loginWithPartner();
    $room = Room::factory()->create(['coworking_id' => $coworking->id]);
    $params = [
      'user_uuid' => Auth::user()->uuid,
      'coworking_uuid' => $coworking->uuid, 
      'uuid' => $room->uuid
    ];
    $response = $this->delete(route('coworking.room.destroy', $params));
    $response->assertStatus(200);
  }

  public function test_room_attach_category()
  {
    $coworking = $this->loginWithPartner();
    $room = Room::factory()->create(['coworking_id' => $coworking->id]);
    $category = Category::factory()->create();
    $params = [
      'uuid' => $room->uuid];
    $payload = ['category_uuid' => $category->uuid];
    $response = $this->post(route('room.attach.category', $params), $payload);
    $response->assertStatus(200);
    $this->assertEquals(__('response.attach.success', ['resource' => 'Category']), $response->json('message'));
  }

  public function test_room_detach_category()
  {
    $coworking = $this->loginWithPartner();
    $room = Room::factory()->create(['coworking_id' => $coworking->id]);
    $category = Category::factory()->create();
    $room->categories()->attach($category->id);
    $params = ['uuid' => $room->uuid];
    $payload = ['category_uuid' => $category->uuid];
    $response = $this->post(route('room.detach.category', $params), $payload);
    $response->assertStatus(200);
    $this->assertEquals(__('response.detach.success', ['resource' => 'Category']), $response->json('message'));
  }

  public function test_room_availability()
  {
    $coworking = $this->createCoworkingWithOpeningHoursAndRoom();
    $partner   = Auth::user();
    $schedule  = Schedule::factory()->create(['user_id' => $partner->id]);
    $room     = Room::where('coworking_id', $coworking->id)->first();
    $this->loginWithCustomer();
    Appointment::factory()->create([
      'schedule_id' => $schedule->id,
      'customer_id' => Auth::user()->id,
      'room_id'     => $room->id,
      'time_init'   => '2022-11-20 12:00:00',
      'time_end'    => '2022-11-20 14:00:00',
    ]);

    $appointment = Appointment::factory()->create([
      'schedule_id' => $schedule->id,
      'customer_id' => Auth::user()->id,
      'room_id'     => $room->id,
      'time_init'   => '2022-11-20 09:00:00',
      'time_end'    => '2022-11-20 12:00:00',
    ]);

    
    $params = [
      'uuid' => $room->uuid,
      'date' => '2022-11-20',
    ];

    $response = $this->get(route('room.availability', $params));
    $response->assertStatus(200);
    $this->assertEquals(__('response.list.success', ['resource' => 'Room']), $response->json('message'));
    $this->assertEquals(2, count($response->json('data')));

  }

  public function test_search_rooms_by_category()
  {
    $this->withDeprecationHandling();

    $coworking = $this->loginWithPartner();
    $data = [
      'uuid' => $coworking->uuid,
      'type' => 'coworking',
      'line_1' => 'Rua los angeles',
      'line_2' => '',
      'country' => 'BR',
      'city' => 'Itaquaquecetuba',
      'state' => 'SP',
      'zip_code' => '13200-000',
    ];

    $addressRepository = new AddressRepository();
    $addressRepository->store($data);
    
    $category = Category::factory()->create();


    $rooms = Room::factory()->count(3)->create(['coworking_id' => $coworking->id]);

    $rooms->each(function ($room) use ($category) {
      $room->categories()->attach($category->id);
    });

    $payload = [
      'type' => 'category',
      'value' => $category->uuid,
    ];

    $this->loginWithCustomer();
    $response = $this->post(route('room.search'), $payload);

    $response->assertStatus(200);
  }


  public function test_search_rooms_by_city()
  {
    $this->withDeprecationHandling();

    $coworking = $this->loginWithPartner();
    $data = [
      'uuid' => $coworking->uuid,
      'type' => 'coworking',
      'line_1' => 'Rua los angeles',
      'line_2' => '',
      'country' => 'BR',
      'city' => 'Itaquaquecetuba',
      'state' => 'SP',
      'zip_code' => '13200-000',
    ];

    $addressRepository = new AddressRepository();
    $addressRepository->store($data);
    
    $category = Category::factory()->create();


    $rooms = Room::factory()->count(3)->create(['coworking_id' => $coworking->id]);

    $rooms->each(function ($room) use ($category) {
      $room->categories()->attach($category->id);
    });

    $payload = [
      'type' => 'city',
      'value' => 'itaqua',
    ];
    $this->loginWithCustomer();

    $response = $this->post(route('room.search'), $payload);
    $response->assertJsonCount(3, 'data');
    $response->assertStatus(200);
  }
}
