<?php


namespace App\Services;

use App\Repositories\RoomRepository;
use App\Repositories\FacilityRepository;
use App\Repositories\FacilityRoomRepository;
use App\Services\Room;

class FacilityRoomService 
{
  public function store(string $room_uuid, string $facility_uuid)
  {
    $roomRepository = new RoomRepository();
    $room = $roomRepository->findByUuid($room_uuid);

    $facilityRepository = new FacilityRepository();
    $facility = $facilityRepository->findByUuid($facility_uuid);

    $facilityRoomRepository = new FacilityRoomRepository();

    $data = [
      'room_id' => $room->id,
      'facility_id' => $facility->id
    ];
    $response = $facilityRoomRepository->createFacility($data);

    return $response;
    
  }

  public function destroy(string $room_uuid, string $facility_uuid)
  {
    
    $roomRepository = new RoomRepository();
    $room = $roomRepository->findByUuid($room_uuid);

    $facilityRepository = new FacilityRepository();
    $facility = $facilityRepository->findByUuid($facility_uuid);

    $facilityRoomRepository = new FacilityRoomRepository();
    
    $data = [
      'room_id' => $room->id,
      'facility_id' => $facility->id
    ];

    $response = $facilityRoomRepository->destroyFacility($data);
    
    return $response;
    
  }
}