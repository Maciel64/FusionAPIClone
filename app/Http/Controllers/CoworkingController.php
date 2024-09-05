<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAddressRequest;
use App\Http\Requests\StoreCoworkingRequest;
use App\Http\Requests\StorePhotoRequest;
use App\Http\Requests\StorePhotosRequest;
use App\Http\Requests\UpdateCoworkingRequest;
use App\Repositories\CoworkingRepository;
use App\Repositories\PhotoRepository;
use App\Repositories\UserRepository;
use App\Services\CoworkingService;
use App\Services\PhotoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CoworkingController extends Controller
{

    public function __construct()
    {
      $this->resource = ['resource' => 'Coworking'];
    }

    /**
     * index
     * 
     * This method is used to list all coworkings by user
     * 
     * @group Partner
     * @subgroup Coworking
     * @authenticated
     * 
     * @response {
     *   "status": true,
     *   "message": "Coworking list retrieved successfully",
     *   "data": [
     *     {
     *       "uuid": "a2c5ff3c-66e5-438b-a1d2-0864714fadcf",
     *       "name": "Grace Jenkins IV",
     *       "description": "Corrupti distinctio consequatur consequatur id labore eaque iusto. Est vel quis illo. Blanditiis id molestiae dignissimos eos consequatur incidunt aut.",
     *       "photos": {
     *         "current_page": 1,
     *         "data": [],
     *         "first_page_url": "http:\/\/localhost:9000\/api\/partner\/coworking?page=1",
     *         "from": null,
     *         "last_page": 1,
     *         "last_page_url": "http:\/\/localhost:9000\/api\/partner\/coworking?page=1",
     *         "links": [
     *           {
     *             "url": null,
     *             "label": "&laquo; Previous",
     *             "active": false
     *           },
     *           {
     *             "url": "http:\/\/localhost:9000\/api\/partner\/coworking?page=1",
     *             "label": "1",
     *             "active": true
     *           },
     *           {
     *             "url": null,
     *             "label": "Next &raquo;",
     *             "active": false
     *           }
     *         ],
     *         "next_page_url": null,
     *         "path": "http:\/\/localhost:9000\/api\/partner\/coworking",
     *         "per_page": 30,
     *         "prev_page_url": null,
     *         "to": null,
     *         "total": 0
     *       },
     *       "address": null,
     *       "opening_hours": [],
     *       "schedule_uuid": null,
     *       "contacts": []
     *     },
     *     {
     *       "uuid": "b2235a14-643c-42e1-8e5b-40c9ac36b844",
     *       "name": "Sydney Wilkinson",
     *       "description": "Delectus ipsa occaecati omnis mollitia doloribus sint facere. Maiores non qui ut dolorum dolorum distinctio consequatur. Et dolores sapiente delectus hic a. Incidunt perspiciatis itaque ut quae est.",
     *       "photos": {
     *         "current_page": 1,
     *         "data": [],
     *         "first_page_url": "http:\/\/localhost:9000\/api\/partner\/coworking?page=1",
     *         "from": null,
     *         "last_page": 1,
     *         "last_page_url": "http:\/\/localhost:9000\/api\/partner\/coworking?page=1",
     *         "links": [
     *           {
     *             "url": null,
     *             "label": "&laquo; Previous",
     *             "active": false
     *           },
     *           {
     *             "url": "http:\/\/localhost:9000\/api\/partner\/coworking?page=1",
     *             "label": "1",
     *             "active": true
     *           },
     *           {
     *             "url": null,
     *             "label": "Next &raquo;",
     *             "active": false
     *           }
     *         ],
     *         "next_page_url": null,
     *         "path": "http:\/\/localhost:9000\/api\/partner\/coworking",
     *         "per_page": 30,
     *         "prev_page_url": null,
     *         "to": null,
     *         "total": 0
     *       },
     *       "address": null,
     *       "opening_hours": [],
     *       "schedule_uuid": null,
     *       "contacts": []
     *     }
     *   ]
     * }
     * @param string $userUuid user uuid
     * @param CoworkingRepository  $coworkingRepository
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, CoworkingRepository $repository)
    {
      $response = $repository->getByUserUuid($request->user_uuid);
      return $this->response('list', $response);
    }

    public function indexAll(Request $request, CoworkingRepository $repository)
    {
      $response = $repository->getAllCoworking();
      return $this->response('list', $response);
    }

    public function countAllCoworkings(Request $request, CoworkingService $service)
    {
      $response = $service->getCurrentCoworkingCountByDate($request->dateInit, $request->dateEnd);
      return response()->json(['count' => $response]);
    }

    /**
     * store 
     * 
     * This method is used to create a new coworking
     * 
     * @group Coworking
     * @subgroup Partner
     * @authenticated
     *
     * @queryParam uuid string required The partner uuid. Example: 1a2b3c4d-5e6f-7g8h-9i0j-1k2l3m4n5o6p 
     * 
     * @bodyParam name string required The coworking name. Example: Coworking Test
     * @bodyParam description string required The coworking description. Example: Coworking Test
     *
     * @response {
     *   "status": true,
     *   "message": "Coworking created successfully",
     *   "data": {
     *     "name": "Coworking Test",
     *     "description": "Coworking Test",
     *     "uuid": "98f2e276-e875-43a3-9fa7-3cd70f7f0da7",
     *     "photos": {
     *       "current_page": 1,
     *       "data": [],
     *       "first_page_url": "http:\/\/localhost:9000\/api\/partner\/coworking?page=1",
     *       "from": null,
     *       "last_page": 1,
     *       "last_page_url": "http:\/\/localhost:9000\/api\/partner\/coworking?page=1",
     *       "links": [
     *         {
     *           "url": null,
     *           "label": "&laquo; Previous",
     *           "active": false
     *         },
     *         {
     *           "url": "http:\/\/localhost:9000\/api\/partner\/coworking?page=1",
     *           "label": "1",
     *           "active": true
     *         },
     *         {
     *           "url": null,
     *           "label": "Next &raquo;",
     *           "active": false
     *         }
     *       ],
     *       "next_page_url": null,
     *       "path": "http:\/\/localhost:9000\/api\/partner\/coworking",
     *       "per_page": 30,
     *       "prev_page_url": null,
     *       "to": null,
     *       "total": 0
     *     },
     *     "address": null,
     *     "opening_hours": [],
     *     "schedule_uuid": null,
     *     "contacts": []
     *   }
     * }
     * 
     * @param StoreCoworkingRequest  $request
     * @param CoworkingRepository  $coworkingRepository
     * @return Response
     */
    public function store(StoreCoworkingRequest $request, CoworkingRepository $repository)
    {
      $data            = $request->validated();
      $userRepository  = new UserRepository();
      $user            = $userRepository->findByUuid($request->user_uuid);
      $data['user_id'] = $user->id;
      unset($data['user_uuid']);

      if($repository->create($data)){
        $response = $repository->getByUserUuid($request->user_uuid);
        return $this->response('store', $response);
      }

      return $this->response("error", false);
    }

    /**
     * 
     * show
     * 
     * This method is used to show a coworking by uuid
     * @group Partner
     * @subgroup Coworking
     * @authenticated
     * 
     * @queryParam uuid string required The coworking uuid. Example: 1a2b3c4d-5e6f-7g8h-9i0j-1k2l3m4n5o6p
     * 
     * @response {
     *   "status": true,
     *   "message": "Coworking retrieved successfully",
     *   "data": {
     *     "uuid": "9f022787-b8a1-4382-9d5b-ca79027e9dbc",
     *     "name": "April Mills",
     *     "description": "Sed voluptas et pariatur neque omnis. Porro est nesciunt deleniti eum et. Veritatis saepe veritatis totam qui suscipit molestiae.",
     *     "photos": {
     *       "current_page": 1,
     *       "data": [],
     *       "first_page_url": "http:\/\/localhost:9000\/api\/partner\/coworking\/9f022787-b8a1-4382-9d5b-ca79027e9dbc?page=1",
     *       "from": null,
     *       "last_page": 1,
     *       "last_page_url": "http:\/\/localhost:9000\/api\/partner\/coworking\/9f022787-b8a1-4382-9d5b-ca79027e9dbc?page=1",
     *       "links": [
     *         {
     *           "url": null,
     *           "label": "&laquo; Previous",
     *           "active": false
     *         },
     *         {
     *           "url": "http:\/\/localhost:9000\/api\/partner\/coworking\/9f022787-b8a1-4382-9d5b-ca79027e9dbc?page=1",
     *           "label": "1",
     *           "active": true
     *         },
     *         {
     *           "url": null,
     *           "label": "Next &raquo;",
     *           "active": false
     *         }
     *       ],
     *       "next_page_url": null,
     *       "path": "http:\/\/localhost:9000\/api\/partner\/coworking\/9f022787-b8a1-4382-9d5b-ca79027e9dbc",
     *       "per_page": 30,
     *       "prev_page_url": null,
     *       "to": null,
     *       "total": 0
     *     },
     *     "address": null,
     *     "opening_hours": [],
     *     "schedule_uuid": null,
     *     "contacts": []
     *   }
     * }
     * 
     *
     * @param Request $request
     * @param CoworkingRepository  $coworkingRepository
     * @return Response
     */
    public function show(Request $request, CoworkingRepository $repository)
    {
      $response = $repository->findByUuid($request->uuid);
      return $this->response('show', $response);
    }

    /**
     * 
     * update
     * 
     * This method is used to update a coworking by uuid
     * 
     * @group Partner
     * @subgroup Coworking
     * @authenticated
     * 
     * @queryParam uuid string required The coworking uuid. Example: 1a2b3c4d-5e6f-7g8h-9i0j-1k2l3m4n5o6p
     * 
     * @bodyParam name string required The coworking name. Example: Coworking Test
     * @bodyParam description string required The coworking description. Example: Coworking Test
     * 
     * @response {
     *   "status": true,
     *   "message": "Coworking updated successfully",
     *   "data": {
     *     "uuid": "e9e7f8a3-b4e8-4793-bf91-d8e6152400a0",
     *     "name": "Coworking Test Updated",
     *     "description": "Coworking Test Updated",
     *     "photos": {
     *       "current_page": 1,
     *       "data": [],
     *       "first_page_url": "http:\/\/localhost:9000\/api\/partner\/coworking\/e9e7f8a3-b4e8-4793-bf91-d8e6152400a0?page=1",
     *       "from": null,
     *       "last_page": 1,
     *       "last_page_url": "http:\/\/localhost:9000\/api\/partner\/coworking\/e9e7f8a3-b4e8-4793-bf91-d8e6152400a0?page=1",
     *       "links": [
     *         {
     *           "url": null,
     *           "label": "&laquo; Previous",
     *           "active": false
     *         },
     *         {
     *           "url": "http:\/\/localhost:9000\/api\/partner\/coworking\/e9e7f8a3-b4e8-4793-bf91-d8e6152400a0?page=1",
     *           "label": "1",
     *           "active": true
     *         },
     *         {
     *           "url": null,
     *           "label": "Next &raquo;",
     *           "active": false
     *         }
     *       ],
     *       "next_page_url": null,
     *       "path": "http:\/\/localhost:9000\/api\/partner\/coworking\/e9e7f8a3-b4e8-4793-bf91-d8e6152400a0",
     *       "per_page": 30,
     *       "prev_page_url": null,
     *       "to": null,
     *       "total": 0
     *     },
     *     "address": null,
     *     "opening_hours": [],
     *     "schedule_uuid": null,
     *     "contacts": []
     *   }
     * }
     *
     * @param  UpdateCoworkingRequest $request
     * @return Response
     */
    public function update(UpdateCoworkingRequest $request, CoworkingRepository $repository)
    {
      $data = $request->validated();
      $response = $repository->updateByUuid($request->uuid, $data);
      return $this->response('update', $response);
    }

    /**
     * 
     * destroy
     * 
     * This method is used to delete a coworking by uuid
     * 
     * @group Partner
     * @subgroup Coworking
     * @authenticated
     * 
     * @queryParam uuid string required The coworking uuid. Example: 1a2b3c4d-5e6f-7g8h-9i0j-1k2l3m4n5o6p
     * 
     * @response {
     *  "status":true,
     *  "message":"Coworking deleted successfully",
     *  "data":[]
     * }
     * 
     * @param Request $request
     * @param CoworkingRepository  $coworkingRepository
     * @return Response
     */
    public function destroy(Request $request, CoworkingRepository $repository)
    {
      $response = $repository->deleteByUuid($request->uuid);
      $response = $repository->getByUser();
      return $this->response('destroy', $response);
    }

}
