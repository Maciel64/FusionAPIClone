<?php

namespace App\Repositories;

use App\Models\BlockedSchedule;
use App\Repositories\RoomRepository;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Carbon;


class BlockedScheduleRepository extends BaseRepository
{
  public function __construct()
  {
    parent::__construct(BlockedSchedule::class);
  }

  public function checkDate($room_id, $timeInit){
    $result = $this->model
        ->where('time_init', $timeInit)
        ->where('room_id', $room_id)
        ->get();

    if ($result->isEmpty()) {
        return 'false';
    }

    return $timeInit;
  }

  public function getBlockedsByRoomIdAndDate($roomId, $date){
    {
      $model = $this->getModelWithFilterDate($date, $date);
      return $model->where('room_id', $roomId)
      ->orderBy('time_init')->get();
    }
  }

  public function getModelWithFilterDate($dateInit, $dateEnd)
  {
    $dateInit = Date::parse($dateInit)->startOfDay();
    $dateEnd = Date::parse($dateEnd)->endOfDay();
    return $this->model->where('time_init', '>=', $dateInit)->where('time_init', '<=', $dateEnd);
  }

  public function deleteBlocked($blocked_uuid){
    $result = $this->model->where('uuid', $blocked_uuid)->delete();
    return $result;
  }

  public function findBlockedByUuid($blocked_uuid){
    $result = $this->model->where('uuid', $blocked_uuid)->get();
    return $result;
  }

  public function getAllBlockedSchedules($room_id){
    // dd(now()->toDateString());
    $result = $this->model
      ->where('room_id', $room_id)
      ->whereDate('time_init', '>=', now()->toDateString().' 00:00:00') //colocar o horário correto
      ->orderBy('time_init')
      ->get();

    return $result;
  }

}