<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCoworkingOpeningHoursRequest;
use App\Http\Requests\UpdateCoworkingOpeningHoursRequest;
use App\Models\CoworkingOpeningHours;
use App\Repositories\CoworkingOpeningHoursRepository;
use App\Services\CoworkingOpeningHoursService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CoworkingOpeningHoursController extends Controller
{

    public function __construct()
    {
      $this->resource = ['resource' => 'Coworking Opening Hours'];
    }

    /**
     * opening.hours.index
     * 
     * Display a listing of the resource.
     * 
     * @group Partner
     * @subgroup Coworking
     * @authenticated
     *
     * @queryParam coworking_uuid string required The coworking uuid. Example: 1a2b3c4d-5e6f-7g8h-9i0j-1k2l3m4n5o6p 
     *
     * @response {
     *   "status": true,
     *   "message": "Coworking Opening Hours list retrieved successfully",
     *   "data": [
     *     {
     *       "uuid": "ee151c21-e4a3-47bc-a193-00cc8e9225ef",
     *       "day_of_week": "monday",
     *       "opening": "08:00:00",
     *       "closing": "18:00:00"
     *     },
     *     {
     *       "uuid": "b0b41b8b-613c-4f14-a18d-c7854fca1646",
     *       "day_of_week": "tuesday",
     *       "opening": "08:00:00",
     *       "closing": "18:00:00"
     *     },
     *     {
     *       "uuid": "fb6a15e6-0ef0-41c1-b715-6005a894588e",
     *       "day_of_week": "wednesday",
     *       "opening": "08:00:00",
     *       "closing": "18:00:00"
     *     },
     *     {
     *       "uuid": "2acfbf9e-e218-4a06-b5a3-c47f7dc2e2f7",
     *       "day_of_week": "thursday",
     *       "opening": "08:00:00",
     *       "closing": "18:00:00"
     *     },
     *     {
     *       "uuid": "a17bc38d-3735-4109-a5eb-f4c2c9477f3b",
     *       "day_of_week": "friday",
     *       "opening": "08:00:00",
     *       "closing": "18:00:00"
     *     },
     *     {
     *       "uuid": "057f5e6d-7013-4a7d-90ce-8f62f0a7e1a1",
     *       "day_of_week": "saturday",
     *       "opening": "08:00:00",
     *       "closing": "18:00:00"
     *     },
     *     {
     *       "uuid": "627cb865-4163-4b0e-a45a-5ca391a6d392",
     *       "day_of_week": "sunday",
     *       "opening": "08:00:00",
     *       "closing": "18:00:00"
     *     }
     *   ]
     * } 
     * 
     * @param Request $request
     * @param CoworkingOpeningHoursService $service
     * @return void
     */
    public function index(Request $request, CoworkingOpeningHoursService $service)
    {
      $response = $service->index($request->coworking_uuid);
      return $this->response('list', $response);
    }

    /**
     * opening.hours.store / update
     * 
     * Store a newly created or update resource in storage.
     * 
     * @group Partner
     * @subgroup Coworking
     * @authenticated
     * 
     * @queryParam coworking_uuid string required The coworking uuid. Example: 1a2b3c4d-5e6f-7g8h-9i0j-1k2l3m4n5o6p
     * 
     * @bodyParam settings object required The settings object.
     * @bodyParam settings.day_of_week string required The day of week. Example: monday
     * @bodyParam settings.opening string required The opening time. Example: 08:00:00
     * @bodyParam settings.closing string required The closing time. Example: 18:00:00
     * 
     * @response {
     *   "status": true,
     *   "message": "Coworking Opening Hours created successfully",
     *   "data": [
     *     {
     *       "day_of_week": "monday",
     *       "opening": "08:00",
     *       "closing": "18:00",
     *       "uuid": "9c732acf-6c3e-4fba-9f71-d97d72f189c7"
     *     },
     *     {
     *       "day_of_week": "tuesday",
     *       "opening": "08:00",
     *       "closing": "18:00",
     *       "uuid": "f7997dd2-e614-4cf0-b988-3ddf3082f356"
     *     },
     *     {
     *       "day_of_week": "wednesday",
     *       "opening": "08:00",
     *       "closing": "18:00",
     *       "uuid": "d4c29b09-c45f-4603-903c-a68b271c3ac1"
     *     },
     *     {
     *       "day_of_week": "thursday",
     *       "opening": "08:00",
     *       "closing": "18:00",
     *       "uuid": "5be36083-df89-45f2-a4d9-4aef6dc751c4"
     *     },
     *     {
     *       "day_of_week": "friday",
     *       "opening": "08:00",
     *       "closing": "18:00",
     *       "uuid": "ae015488-1941-4038-b540-9476aa61b21a"
     *     },
     *     {
     *       "day_of_week": "saturday",
     *       "opening": "08:00",
     *       "closing": "18:00",
     *       "uuid": "170a8893-25af-4a84-91c3-a08dd4da879a"
     *     },
     *     {
     *       "day_of_week": "sunday",
     *       "opening": "08:00",
     *       "closing": "18:00",
     *       "uuid": "519e8bd2-67a9-4305-96b7-ee240f826f94"
     *     }
     *   ]
     * }
     *
     * @param StoreCoworkingOpeningHoursRequest $request
     * @param CoworkingOpeningHoursService $service
     * @return void
     */
    public function store(StoreCoworkingOpeningHoursRequest $request, CoworkingOpeningHoursService $service)
    {
      $data = $request->validated();
      $response = $service->insert($request->coworking_uuid, $data);
      return $this->response('success', $response);
    }

    /**
     * opening.hours.destroy
     * 
     * Delete opening hours by uuid.
     * 
     * @group Partner
     * @subgroup Coworking
     * @authenticated
     * 
     * @queryParam coworking_uuid string required The coworking uuid. Example: 1a2b3c4d-5e6f-7g8h-9i0j-1k2l3m4n5o6p
     * 
     * @response {
     *  "status":true,
     *  "message":"Coworking Opening Hours deleted successfully",
     *  "data":[]
     * }
     * 
     * @param Request $request
     * @param CoworkingOpeningHoursRepository $repository
     * @return void
     */
    public function destroy(Request $request, CoworkingOpeningHoursRepository $repository)
    {
      $response = $repository->deleteByUuid($request->uuid);
      return $this->response('destroy', $response);
    }

    /**
     * opening.hours.destroy.bulk
     * 
     * Delete list opening hours by uuids.
     * 
     * @group Partner
     * @subgroup Coworking
     * @authenticated
     * 
     * @queryParam coworking_uuid string required The coworking uuid. Example: 1a2b3c4d-5e6f-7g8h-9i0j-1k2l3m4n5o6p
     * 
     * @bodyParam uuids array required The list uuids.
     * @bodyParam uuids[0] string required The uuid. Example: 1a2b3c4d-5e6f-7g8h-9i0j-1k2l3m4n5o6p
     * @bodyParam uuids[1] string required The uuid. Example: 2a3b4c5d-6e7f-8g9h-0i1j-2k3l4m5n6o7p
     * 
     * @response {
     *  "status":true,
     *  "message":"Coworking Opening Hours deleted successfully",
     *  "data":[]
     * }
     * 
     *
     * @param Request $request
     * @param CoworkingOpeningHoursService $service
     * @return void
     */
    public function destroyBulk(Request $request, CoworkingOpeningHoursService $service)
    {
      $data = $request->validate([
        'uuids' => 'required|array'
      ]);

      $response = $service->destroyBulk($request->coworking_uuid, $data['uuids']);
      return $this->response('destroy', $response);      
    }
}
