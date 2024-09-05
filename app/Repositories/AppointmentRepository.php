<?php

namespace App\Repositories;

use App\Models\Appointment;
use App\Repositories\CoworkingRepository;
use App\Repositories\RoomRepository;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Carbon;


class AppointmentRepository extends BaseRepository
{
  public function __construct()
  {
    parent::__construct(Appointment::class);
  }

  public function indexOrdered($request){

    if (!$request->dateInit) {
      $dateInit = now()->subDays(30)->toDateString();
      $request->dateInit = $dateInit;
    }

    if(!$request->dateEnd){
      $dateEnd = now()->addDays(30)->toDateString();
      $request->dateEnd = $dateEnd; 
    }

    $model = $this->getModelWithFilterDate($request->dateInit, $request->dateEnd);

    
    $allowedStatuses = ['pending', 'paid', 'canceled', 'finished'];
    if (in_array($request->status, $allowedStatuses)) {
      $model->where('status', $request->status);
    }    

    if ($request->status == 'exceptCanceled'){
      $model->whereNotIn('status', ['canceled']);
    }

    if ($request->status == 'onlyPaidAndFinished'){
      $model->whereIn('status', ['paid', 'finished']);
    }
    

    // if ($request->status == 'except_canceled') {
    //   $model->whereIn('status', ['pending', 'paid', 'finished']);
    // }    

    if ($request->coworking_uuid != null){
      $coworkingRepository = new CoworkingRepository();
      $coworking = $coworkingRepository->findByUuid($request->coworking_uuid);
      $coworking_id = $coworking->id;

      $model->whereHas('room', function ($query) use ($coworking_id) {
        $query->where('coworking_id', $coworking_id);
      });
    }

    if ($request->room_uuid != null){
      $roomRepository = new RoomRepository();
      $room = $roomRepository->findByUuid($request->room_uuid);
      $room_id = $room->id;

      $model->where('room_id', $room_id);
    }

    if ($request->customer_uuid != null){
      $customerRepository = new UserRepository();
      $customer = $customerRepository->findByUuid($request->customer_uuid);
      $customer_id = $customer->id;

      $model->where('customer_id', $customer_id);
    }
    
    if(!isset($request->ordered_attribute)){
      $request->ordered_attribute = 'time_init';
    }

    if ($request->orientation == 'desc'){
      $model->orderByDesc($request->ordered_attribute);
    }else{
      $model->orderBy($request->ordered_attribute);
    }

    return $model->get();
  }


  public function getAppointmentsByRoomIdAndDate($roomId, $date)
  {
    $model = $this->getModelWithFilterDate($date, $date);
    return $model->where('room_id', $roomId)->orderBy('time_init')->get();
  }

  public function getAppointmentsByRoomIdAndDateWithoutCanceled($roomId, $date)
  {
    $model = $this->getModelWithFilterDate($date, $date);
    return $model
      ->select('time_init', 'time_end')
      ->where('room_id', $roomId)
      ->where('status', '!=', 'canceled')
      ->orderBy('time_init')->get();
  }


  public function listByRoom($roomId, $date)
  {
    $model = $this->getModelWithFilterDate($date, $date);
    $dateInit = Date::parse($date)->startOfDay();
    $dateEnd  = Date::parse($date)->endOfDay();

    return $model
      ->where('room_id', $roomId)
      ->where('time_init', '>=', $dateInit)
      ->where('time_init', '<=', $dateEnd)
      ->paginate();
  }

  public function listBySchedule(string $scheduleUuid, string $dateInit, string $dateEnd)
  {
    $repository = new ScheduleRepository();
    $schedule = $repository->findByUuid($scheduleUuid);
    $model = $this->getModelWithFilterDate($dateInit, $dateEnd);
    return $model->where('schedule_id', $schedule->id)->paginate(10);
  }

  public function listByCustomer(string $dateInit, string $dateEnd)
  {
    $model = $this->getModelWithFilterDate($dateInit, $dateEnd);
    return $model->where('customer_id', auth()->user()->id)->paginate(15);
  }

  public function listForCustomerByAdmin(string $dateInit, string $dateEnd, string $user_uuid)
  {
    $repository = new UserRepository();
    $customer = $repository->findByUuid($user_uuid);
    $model = $this->getModelWithFilterDate($dateInit, $dateEnd);
    return $model->where('customer_id', $customer->id)->paginate(15);
  }

  public function getModelWithFilterDate($dateInit, $dateEnd)
  {
    $dateInit = Date::parse($dateInit)->startOfDay();
    $dateEnd = Date::parse($dateEnd)->endOfDay();
    return $this->model->where('time_init', '>=', $dateInit)->where('time_init', '<=', $dateEnd);
  }

  public function getSchedulesByDate(string $dateInit, string $dateEnd)
  {
      $models = $this->getModelWithFilterDate($dateInit, $dateEnd)
          ->select(\DB::raw('TIME(time_init) as time'))
          ->groupBy('time')
          ->selectRaw('count(*) as count')
          ->get();
      
      return $models;
  }

  public function getRevenue(string $dateInit, string $dateEnd)
  {
    $model = $this->getModelWithFilterDate($dateInit, $dateEnd)
        ->sum('value_total');
    
    return $model;
  }
  
  public function getTotalOfAppointments(string $dateInit, string $dateEnd)
  {
    $model = $this->getModelWithFilterDate($dateInit, $dateEnd)
        ->count();    
    return $model;
  }

  public function getAverageHoursReserved(string $dateInit, string $dateEnd)
  {
    $model = $this->whereBetween('created_at', [$dateInit.' 00:00:00', $dateEnd.' 23:59:59'])
        ->count();    
    return $model;
  }

  public function getTotalOfCanceled(string $dateInit, string $dateEnd)
  {
    $model = $this->getModelWithFilterDate($dateInit, $dateEnd)
            ->where('status', 'canceled')
            ->count();    
    return $model;
  }

  public function getTotalOfCanceledValue(string $dateInit, string $dateEnd)
  {
    $model = $this->getModelWithFilterDate($dateInit, $dateEnd)
            ->where('status', 'canceled')
            ->selectRaw('DATE(canceled_at) as date, sum(value_total) as value_total')
            ->groupBy('date')
            ->get();    
    return $model;
  }

  public function getAppointmentCount(string $dateInit, string $dateEnd)
  {
    $model = $this->model->whereBetween('created_at', [$dateInit.' 00:00:00', $dateEnd.' 23:59:59'])
        ->count();    
    return $model;
  }

  public function getAppointmentCountByDistinctUser(string $dateInit, string $dateEnd)
  {
    $model = $this->model->whereBetween('created_at', [$dateInit.' 00:00:00', $dateEnd.' 23:59:59'])
    ->distinct('customer_id')
    ->count();    
    return $model;
  }

  public function getCanceledAppointmentCount(string $dateInit, string $dateEnd)
  {
    $model = $this->model->whereBetween('created_at', [$dateInit.' 00:00:00', $dateEnd.' 23:59:59'])
        ->where('status', 'canceled')
        ->count();    
    return $model;
  }

  public function getRevenuePerHourByDate(string $dateInit, string $dateEnd)
  {
    $model = $this->model->whereBetween('created_at', [$dateInit.' 00:00:00', $dateEnd.' 23:59:59'])
        ->sum('value_total');    
    return $model;
  }




  public function findByUuidAndCustomer(string $uuid, string $customerUuid)
  {
    $repository = new UserRepository();
    $customer = $repository->findByUuid($customerUuid);
    return $this->model->where('uuid', $uuid)->where('customer_id', $customer->id)->first();
  }
}