<?php

namespace App\Repositories;
use App\Models\FacilityRoom;
use Validator;

class FacilityRoomRepository extends BaseRepository
{

  public function __construct()
  {
    parent::__construct(FacilityRoom::class);
  }

  public function createFacility($data)
  {
    $validator = Validator::make($data, [
        'room_id' => 'required|exists:rooms,id',
        'facility_id' => 'required|exists:facilities,id'
    ]);

    if ($validator->fails()) {
        return $validator->errors();
    }
    $facilityRoom = $this->model::create([
      'room_id' => $data['room_id'],
      'facility_id' => $data['facility_id'],
    ]);

    return response()->json(['facilityRoom' => $facilityRoom], 201);
     
  }

  public function destroyFacility($data)
  {
    $facilityRoom = FacilityRoom::where('room_id', $data['room_id'])
                                ->where('facility_id', $data['facility_id'])
                                ->first();
    if ($facilityRoom != null){
      $facilityRoom = FacilityRoom::where('room_id', $data['room_id'])
                                  ->where('facility_id', $data['facility_id'])
                                  ->delete();
      return response()->json([
        "success" => true,
        "message" => "Facility deleted successfully.",
        "data" => null
      ]);
    }                           
    return response()->json([
      "success" => false,
      "message" => "Failed to delete facility.",
      "data" => null
    ], 500);
  } 
    


  // public function getAllFacilities(){

  //   return $this->model->paginate(10) ?? false;
  //   where
  // }

  
}