<?php

namespace Tests;

use App\Models\Appointment;
use App\Models\Coworking;
use App\Models\CoworkingOpeningHours;
use App\Models\Room;
use App\Models\Schedule;
use App\Models\User;
use App\Models\Workspace;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Mockery;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use RefreshDatabase;

    public function setUp(): void
    {
      parent::setUp();
      $this->seed();
    }

    private function createUser($email, $role)
    {
      try {
        $user = User::factory()->create(['email' => $email,'name' => $role]);
        $user->assignRole($role);
        if($role === "customer") {
          $this->customerCheck($user);
          Workspace::create(['name' => 'Workspace','user_id' => $user->id]);
        }
        Auth::login($user);
      } catch (\Throwable $th) {
        throw new Exception($th->getMessage()." - User: {$user->email} ");
      }
    }

    protected function login()
    {
      $user = User::where('email', 'admin@fusion.com')->first();
      Auth::login($user);
    }

    protected function logout($user)
    {
      Auth::logout($user);
    }

    protected function loginWithOwner()
    {
      $this->createUser('owner@fusion.com', 'owner');
    }
    
    protected function loginWithAdmin()
    {
      $this->createUser('luiz@syscomon.com', 'admin');
    }

    protected function loginWithPartner()
    {
      $user = User::where('email', 'partner@fusion.com')->first();
      $this->createUser('partner@fusion.com', 'partner');
      return Coworking::factory()->create(['name' => 'Coworking','user_id' => Auth::user()->id]);
    }

    protected function loginWithCustomer()
    {
      $this->createUser('customer@fusion.com', 'customer');
      
    }
    
    protected function createCoworkingWithOpeningHours(bool $openingHours = true, $subscription = false)
    {
      if($subscription){
        $user = User::where('email', 'partner@fusion.com')->first();
        if(!$user){
          $user = User::factory()->create(['email' => 'partner@fusion.com','name' => 'partner']);
        }
        $user->assignRole('partner');
        $coworking = Coworking::factory()->create(['name' => 'Coworking','user_id' => $user->id]);
      }else{
        $coworking = $this->loginWithPartner();

      }

      if (!$openingHours) return $coworking->fresh();
      $daysOfWeek = [
        ['day_of_week' => 'sunday'],
        ['day_of_week' => 'monday'],
        ['day_of_week' => 'tuesday'],
        ['day_of_week' => 'wednesday'],
        ['day_of_week' => 'thursday'],
        ['day_of_week' => 'friday'],
        ['day_of_week' => 'saturday'],
      ];

      foreach ($daysOfWeek as $day) {
        $data = [...$day, 'coworking_id' => $coworking->id];
        CoworkingOpeningHours::factory()->create($data);
      }

      return $coworking->fresh();
    }

    protected function createCoworkingWithOpeningHoursAndRoom(bool $openingHours = true, $subscription = false)
    {
      $coworking = $this->createCoworkingWithOpeningHours($openingHours, $subscription);
      Room::factory(2)->create(['coworking_id' => $coworking->id]);
      return $coworking->fresh();
    }

    protected function createAppointmentToCustomer(int $customerId, $subscription = false)
    {
      $coworking = $this->createCoworkingWithOpeningHoursAndRoom(subscription: $subscription);
      $schedule = Schedule::factory()->create(['user_id' => Auth::user()->id]);
      $rooms = Room::where('coworking_id', $coworking->id)->get();
  
      return Appointment::factory()->create([
        'customer_id' => $customerId,
        'room_id'     => $rooms->first()->id,
        'schedule_id' => $schedule->id,
      ]);
    }

    protected function createAppointmentToCustomerByScheduleAndRoomId(int $customerId, int $roomId, int $scheduleId)
    {
      return Appointment::factory()->create([
        'customer_id' => $customerId,
        'room_id'     => $roomId,
        'schedule_id' => $scheduleId,
      ]);
    }

    protected function customerCheck(User &$user)
    {
      $user->email_verified_at = now();
      $user->account_active = true;
      $user->account_activated_at = now();
      $user->save();
      return $user->fresh();
    }

    protected function httpFakerReset(): void
    {
      $reflection = new \ReflectionObject(Http::getFacadeRoot());
      $property = $reflection->getProperty('stubCallbacks');
      $property->setAccessible(true);
      $property->setValue(Http::getFacadeRoot(), collect());
    }
}
