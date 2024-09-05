<?php

namespace App\Repositories;

use App\Facades\AppointmentFacade;
use App\Models\Room;
use App\Services\AppointmentService;
use App\Traits\Helpers;

class RoomRepository extends BaseRepository
{
  use Helpers;

  public function __construct()
  {
    parent::__construct(Room::class);
  }

  public function getAvailableRoomCount($dateInit, $dateEnd){
    $count = $this->model
        ->whereBetween('created_at', ['2001-01-01 00:00:00', $dateEnd.' 23:59:59'])
        ->count();

    return $count;         
  }


  public function getOccupiedRoomCount($dateInit, $dateEnd)
  {
      $count = $this->model
          ->whereHas('appointments') // Verifica se o quarto tem pelo menos um agendamento
          ->count();
  
      return $count;
  }

  public function getAllRoomsByCoworking($coworkingUuid)
  {
    $repository = new CoworkingRepository();
    $coworking = $repository->findByUuid($coworkingUuid);
    return $this->model->where('coworking_id', $coworking->id)->paginate(config('app.pagination')) ?? false;
  }

  public function allRoomsByCoworkingWithoutPaginate($coworkingUuid)
  {
    $repository = new CoworkingRepository();
    $coworking = $repository->findByUuid($coworkingUuid);
    return $this->model->where('coworking_id', $coworking->id)->get() ?? false;
  }

  public function getAllRooms()
  {
    return $this->model
    ->orderBy('name', 'asc')
    ->paginate(config('app.pagination')) ?? false;
  }
  
  public function attachCategory($roomUuid, $categoryUuid)
  {
    $room = $this->findByUuid($roomUuid);
    $categoryId = $this->getIdByUuid($categoryUuid, CategoryRepository::class);
    $room->categories()->attach($categoryId);
    return $room->fresh()->categories()->get();
  }

  public function detachCategory($roomUuid, $categoryUuid)
  {
    $room = $this->findByUuid($roomUuid);
    $categoryId = $this->getIdByUuid($categoryUuid, CategoryRepository::class);
    $room->categories()->detach($categoryId);
    return $room->fresh()->categories()->get();
  }

  public function getAvailability($roomUuid, $date)
  {
    $room = $this->findByUuid($roomUuid);
    $service = new AppointmentService();
    return $service->getRoomAvailability($room->id, $date);
  }

  public function getAvailabilityBulk(string $roomUuid, array $datesBulk)
  {
    $room = $this->findByUuid($roomUuid);
    $service = new AppointmentService();
    return $service->getRoomAvailabilityBulk($room->id, $datesBulk);
  }
}