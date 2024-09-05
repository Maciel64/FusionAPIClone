<?php

namespace App\Services;

use App\Models\Room;
use App\Repositories\AddressRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\CoworkingRepository;
use App\Repositories\UserRepository;
use App\Repositories\RoomRepository;
use Illuminate\Database\Eloquent\Model;
use App\Http\Controllers\RoomController;
use App\Helpers\PaginateCollection;
use Illuminate\Support\Collection;


class RoomService
{

  private Room $model;


  public function __construct()
  {
    $this->model = new Room();
  }

  public function SetFixedRoom($data)
  {
    $repository = new RoomRepository();
    $room = $repository->findByUuid($data['room_uuid']);
    $room->fixed = $data['fixed'];
    $room->save();
    return $room->fresh();
  }

  public function getRoomsByGenericSearch($searchName, $date, $attributeToFilter, $attributeSortBy)
  {
    $repositoryAddress = new AddressRepository();
    $repositoryRoom = new RoomRepository();

    if ($attributeToFilter == null || $attributeSortBy == null) {
      if ($searchName == null) {
        $rooms = $this->model
          ->orderBy('fixed', 'desc')
          ->orderBy('created_at', 'desc')
          ->get();
      } else {
        $coworking = $repositoryAddress->getCoworkingByLocation($searchName);
        $rooms = $this->model->whereIn('coworking_id', $coworking->pluck('model_id'))
          ->orWhere('name', 'LIKE', "%$searchName%")
          ->distinct()
          ->orderBy('fixed', 'desc')
          ->orderBy('created_at', 'desc')
          ->get();
      }
    } else {
      if ($searchName == null) {
        $rooms = $this->model
          ->orderBy($attributeToFilter, $attributeSortBy)
          ->get();
      } else {
        $coworking = $repositoryAddress->getCoworkingByLocation($searchName);
        $rooms = $this->model->whereIn('coworking_id', $coworking->pluck('model_id'))
          ->orWhere('name', 'LIKE', "%$searchName%")
          ->distinct()
          ->orderBy($attributeToFilter, $attributeSortBy)
          ->get();
      }
    }

    $availableRooms = [];
    foreach ($rooms as $room) {
      $responseAvailability = $repositoryRoom->getAvailability($room->uuid, $date);
      if (!empty($responseAvailability)) {
        // $room->makeHidden('operating_hours', 'opening_hours', 'facilities');
        // $room->makeVisible('created_at');
        $availableRooms[] = $room;
      }
    }

    $availableRoomsCollection = collect($availableRooms);
    return PaginateCollection::paginate($availableRoomsCollection, 20);
  }

  public function availableRoomsByDate($dateInit, $dateEnd)
  {
    $repository = new RoomRepository();
    $availableRoomCount = $repository->getAvailableRoomCount($dateInit, $dateEnd);
    $occupiedRoomCount = $repository->getOccupiedRoomCount($dateInit, $dateEnd);
    $occupancyRate = ($occupiedRoomCount / $availableRoomCount) * 100;
    $data = [
      'availableRoomCount' => $availableRoomCount,
      'occupiedRoomCount' => $occupiedRoomCount,
      'occupancyRate' => $occupancyRate,

    ];

    return $data;
  }

  public function getRoomsByLocations(string $neighborhood)
  {
    // implementar busca por cidade e bairro  
    $repository = new AddressRepository();
    $coworking = $repository->getCoworkingByCity($neighborhood);
    if (!$coworking) return false;
    return $this->model->whereIn('coworking_id', $coworking->pluck('model_id'));
  }

  public function getRoomsByNeighborhood(string $neighborhood, string $date)
  {
    $repositoryAddress = new AddressRepository();
    $repositoryRoom = new RoomRepository();
    $coworking = $repositoryAddress->getCoworkingByNeighborhood($neighborhood);
    if (!$coworking) return 'false';
    $rooms = $this->model->whereIn('coworking_id', $coworking->pluck('model_id'));
    $availableRooms = [];
    foreach ($rooms->get() as $room) {
      $responseAvailability = $repositoryRoom->getAvailability($room->uuid, $date);
      if (!empty($responseAvailability)) {
        $availableRooms[] = $room;
      }
    }
    return $availableRooms;
  }



  public function getRoomsByCity(string $city)
  {
    $repository = new AddressRepository();
    $coworking = $repository->getCoworkingByCity($city);
    if (!$coworking) return false;
    return $this->model->whereIn('coworking_id', $coworking->pluck('model_id'));
  }

  public function getRoomsByCategoryUuid(string $uuid)
  {
    $repository = new CategoryRepository();
    $category = $repository->findByUuid($uuid);
    return $category->rooms();
  }

  public function getRoomsByCoworkingUuid(string $uuid)
  {
    $repository = new CoworkingRepository();
    $coworking = $repository->findByUuid($uuid);
    return $this->model->where('coworking_id', $coworking->id)->paginate(config('app.pagination')) ?? false;
  }

  public function getRoomsByPartner(string $partnerUuid)
  {
    $user = (new UserRepository())->findByUuid($partnerUuid);
    $coworkingRepository = new CoworkingRepository();
    $ids = $coworkingRepository->getAllCoworkingIdByPartner($user->id);
    return $this->model->whereIn('coworking_id', $ids)->paginate(config('app.pagination')) ?? [];
  }
}
