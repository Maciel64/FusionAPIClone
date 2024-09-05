<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FacilityRoomService;

class FacilityRoomController extends Controller
{
    public function storeFacility(Request $request, FacilityRoomService $service){
        $response = $service->store($request->room_uuid, $request->facility_uuid);
        return $response;        
    }
    

    public function destroyFacility(Request $request, FacilityRoomService $service){
        $response = $service->destroy($request->room_uuid, $request->facility_uuid);
        return $response;
    }

    public function showFacilities(){
        exit('oi');
    }
}
 
