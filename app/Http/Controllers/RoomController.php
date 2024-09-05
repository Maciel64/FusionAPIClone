<?php

namespace App\Http\Controllers;

use App\Http\Requests\RoomAttachOrDetachCategoryRequest;
use App\Http\Requests\SearchRoomsRequest;
use App\Http\Requests\SearchRoomsByNeighborhoodRequest;
use App\Http\Requests\StoreRoomRequest;
use App\Http\Requests\UpdateRoomRequest;
use App\Http\Requests\GenericSearchRequest;
use App\Http\Requests\SetFixedRoomRequest;
use App\Models\Coworking;
use App\Repositories\CoworkingRepository;
use App\Repositories\RoomRepository;
use App\Services\RoomService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RoomController extends Controller
{
    public function __construct()
    {
      $this->resource = ['resource' => 'Room'];
    }

    public function setFixed(SetFixedRoomRequest $request, RoomService $service){
      $data = $request->validated();
      $response = $service->SetFixedRoom($data);
      return response()->json(['status' => true, 'message' => 'Status de fixação alterada com sucesso', "data" => $response ]);
    }

    public function availableRooms(Request $request, RoomService $service){
      $response = $service->availableRoomsByDate($request->dateInit, $request->dateEnd);
      return response()->json($response);
    }

    /**
     * search
     * 
     * This method is used to search rooms by category, city, country
     * 
     * @group Customer
     * @subgroup Room
     * @authenticated
     * 
     * @bodyParam type string required The type of search. Example: category, city or coworking
     * @bodyParam value string required The resource uuid to search. Example: uuid
     * 
     * 
     * @response {
     *   "current_page": 1,
     *   "data": [
     *     {
     *       "uuid": "c34f985c-f08a-4e34-a130-de173490fea6",
     *       "name": "Annamae Smitham",
     *       "number": "5",
     *       "description": "Autem est facere laborum repellat blanditiis sit enim itaque. Ratione et deleniti necessitatibus incidunt quaerat excepturi ut. Hic et sapiente impedit perspiciatis.",
     *       "price_per_minute": 20.74,
     *       "opening_hours": [],
     *       "address": {
     *         "uuid": "d33c3de5-3f1b-44c0-9c63-e61b5aa1f294",
     *         "line_1": "Rua los angeles",
     *         "line_2": "",
     *         "city": "Itaquaquecetuba",
     *         "state": "SP",
     *         "country": "BR",
     *         "zip_code": "13200-000"
     *       },
     *       "categories": [
     *         {
     *           "uuid": "fd882276-6ba2-4a4c-b205-e2f4aec1e3c6",
     *           "name": "Miss Anahi Stroman",
     *           "description": "Dicta ut aliquam voluptatibus in animi perferendis fuga. Consequatur non autem accusamus deleniti tempora. Rem accusamus alias quidem est. Distinctio eum quaerat vero doloremque ut."
     *         }
     *       ],
     *       "photos": [],
     *       "contacts": []
     *     },
     *     {
     *       "uuid": "2075e62d-2f5d-4ef5-ae95-60cd8eaf58f8",
     *       "name": "Ansel Cruickshank",
     *       "number": "8077108",
     *       "description": "Vitae rerum ipsa omnis est quia et. Similique consequatur sequi aut. Enim similique adipisci ad labore.",
     *       "price_per_minute": 73.71,
     *       "opening_hours": [],
     *       "address": {
     *         "uuid": "d33c3de5-3f1b-44c0-9c63-e61b5aa1f294",
     *         "line_1": "Rua los angeles",
     *         "line_2": "",
     *         "city": "Itaquaquecetuba",
     *         "state": "SP",
     *         "country": "BR",
     *         "zip_code": "13200-000"
     *       },
     *       "categories": [
     *         {
     *           "uuid": "fd882276-6ba2-4a4c-b205-e2f4aec1e3c6",
     *           "name": "Miss Anahi Stroman",
     *           "description": "Dicta ut aliquam voluptatibus in animi perferendis fuga. Consequatur non autem accusamus deleniti tempora. Rem accusamus alias quidem est. Distinctio eum quaerat vero doloremque ut."
     *         }
     *       ],
     *       "photos": [],
     *       "contacts": []
     *     },
     *     {
     *       "uuid": "14232f00-673c-4084-9239-0a808751e322",
     *       "name": "Taryn Brown",
     *       "number": "946439",
     *       "description": "Non maxime ea illo harum quasi. Non quas dolores aut ut qui. Sit perferendis nemo dolorem ullam. Labore qui accusantium molestias.",
     *       "price_per_minute": 84.68,
     *       "opening_hours": [],
     *       "address": {
     *         "uuid": "d33c3de5-3f1b-44c0-9c63-e61b5aa1f294",
     *         "line_1": "Rua los angeles",
     *         "line_2": "",
     *         "city": "Itaquaquecetuba",
     *         "state": "SP",
     *         "country": "BR",
     *         "zip_code": "13200-000"
     *       },
     *       "categories": [
     *         {
     *           "uuid": "fd882276-6ba2-4a4c-b205-e2f4aec1e3c6",
     *           "name": "Miss Anahi Stroman",
     *           "description": "Dicta ut aliquam voluptatibus in animi perferendis fuga. Consequatur non autem accusamus deleniti tempora. Rem accusamus alias quidem est. Distinctio eum quaerat vero doloremque ut."
     *         }
     *       ],
     *       "photos": [],
     *       "contacts": []
     *     }
     *   ],
     *   "first_page_url": "http:\/\/localhost:9000\/api\/customer\/room\/search?page=1",
     *   "from": 1,
     *   "last_page": 1,
     *   "last_page_url": "http:\/\/localhost:9000\/api\/customer\/room\/search?page=1",
     *   "links": [
     *     {
     *       "url": null,
     *       "label": "&laquo; Previous",
     *       "active": false
     *     },
     *     {
     *       "url": "http:\/\/localhost:9000\/api\/customer\/room\/search?page=1",
     *       "label": "1",
     *       "active": true
     *     },
     *     {
     *       "url": null,
     *       "label": "Next &raquo;",
     *       "active": false
     *     }
     *   ],
     *   "next_page_url": null,
     *   "path": "http:\/\/localhost:9000\/api\/customer\/room\/search",
     *   "per_page": 15,
     *   "prev_page_url": null,
     *   "to": 3,
     *   "total": 3
     * }
     *
     * @param SearchRoomsRequest $request
     * @param RoomService $service
     * @return void
     */
    public function search(SearchRoomsRequest $request, RoomService $service)
    {
      switch ($request->type) {
        case 'category':
          $response = $service->getRoomsByCategoryUuid($request->value);
          break;

        case 'city':
          $response = $service->getRoomsByCity($request->value);
          break;
      }

      if(!$response) return $this->response('list', false, errorCode: 404);
      return response()->json($response->paginate(config('app.pagination')));
    }

    public function registerOperatingHours(Request $request, RoomRepository $repository, $room_uuid){ 
      $room =  $repository->findByUuid($room_uuid);
      $room->operating_hours = $request->operating_hours;
      $room->save();
      return $room->operating_hours;
    }

    

    

    /**
     * searchByNeighborhood
     *
     * This API allows you to search for rooms based on a specific neighborhood.
     *
     * @group Customer
     * @subgroup Room
     * @authenticated
     * @bodyParam value string required The neighborhood value to search for. Example: Lagoa Nova
     * @bodyParam date string required The date for availability checking. Example: 2023-05-26
     *
     * @response status=200 scenario="Success" {
     *     "data": [{
     *         "uuid": "9ad8dde6-fdc6-42df-9a92-fac208cd34a6",
     *         "name": "Sala odontológica",
     *         "number": "B412",
     *         "description": "Sala odontológica geral",
     *         "price_per_minute": 8,
     *         "opening_hours": [{
     *                 "uuid": "2cd38c76-4994-4262-9b03-30acb44b8dc9",
     *                 "day_of_week": "monday",
     *                 "opening": "08:00",
     *                 "closing": "18:00"
     *             },
     *             {
     *                 "uuid": "b47c84c5-87cb-4284-bd6f-4b928a512466",
     *                 "day_of_week": "tuesday",
     *                 "opening": "08:00",
     *                 "closing": "18:00"
     *             }
     *         ],
     *         "schedule_uuid": "dda0a57a-8573-40c6-a01c-7a95cb9c285e",
     *         "address": {
     *             "uuid": "6596c7d4-56d7-44bc-b0de-acd7cda03a4b",
     *             "line_1": "Rua Nova Colina, 677",
     *             "line_2": "n/a",
     *             "city": "Natal",
     *             "state": "RN",
     *             "country": "Brasil",
     *             "neighborhood": "Nossa Senhora da Apresentação",
     *             "zip_code": "59115615"
     *         },
     *         "categories": [],
     *         "photos": [{
     *                 "uuid": "407b77cc-2e63-430f-8fb5-e197d91d785c",
     *                 "name": "pxJN1kHJ5ogvwaPb9se5F32dA3tEwtS8fFDFl7y6.jpg",
     *                 "url": "https://api.fusion.velty.com.br/room/pxJN1kHJ5ogvwaPb9se5F32dA3tEwtS8fFDFl7y6.jpg"
     *             },
     *             {
     *                 "uuid": "88c78b88-43b5-4ff5-83d1-17e086dc1688",
     *                 "name": "iiBCTicXFb3mYltp0uT8bo0LEn6ZBYKolcGkE4QF.jpg",
     *                 "url": "https://api.fusion.velty.com.br/room/iiBCTicXFb3mYltp0uT8bo0LEn6ZBYKolcGkE4QF.jpg"
     *             }
     *         ],
     *         "contacts": [{
     *             "uuid": "d98340e7-4d46-46f2-b6fa-89cfc85809f5",
     *              "type": "mobile_phone",
     *             "country_code": "55",
     *             "area_code": "011",
     *             "number": "922334452"
     *           }
     *         ],
     *         "coworking_name": "Clinica02",
     *         "partner_name": "Romulo",
     *         "partner_uuid": "c2f63f0c-00de-4cc7-981c-51517ff73fbd",
     *         "coworking_uuid": "ac21cad7-eede-4eb0-babd-4bb8d6d47d8c"
     *       }
     *     ]
     * 
     *
     * @response 400 {
     *    "error": "Validation error.",
     *    "message": {
     *        "value": [
     *            "The value field is required."
     *        ],
     *        "date": [
     *            "The date field is required."
     *        ]
     *    }
     * }
     *
     * 
     * @post /room/searchByNeighborhood
     */
    public function searchByNeighborhood(SearchRoomsByNeighborhoodRequest $request, RoomService $service)
    {
      $response = $service->getRoomsByNeighborhood($request->value, $request->date);
      return response()->json($response);
    }

    public function genericSearch(GenericSearchRequest $request, RoomService $service)
    {
      $data = $request->validated();
      if (!array_key_exists('value', $data)) {
        $data['value'] = null;
      }
      if (!array_key_exists('date', $data) || $data['date'] == null) {
        $data['date'] = date('Y-m-d');
      }
      if (!array_key_exists('attributeToFilter', $data)) {
        $data['attributeToFilter'] = null;
      }
      if (!array_key_exists('attributeSortBy', $data)) {
        $data['attributeSortBy'] = null;
      }

      $response = $service->getRoomsByGenericSearch($data['value'], $data['date'],$data['attributeToFilter'], $data['attributeSortBy']) ;
      return response()->json($response);
    }

    public function listByPartner(Request $request, RoomService $service)
    {
      $response = $service->getRoomsByPartner($request->partner_uuid);
      return response()->json($response);
    }

    /**
     * index 
     * 
     * This method is used to list all rooms by coworking
     * 
     * @group Partner
     * @subgroup Room
     * @authenticated
     * 
     * @queryParam coworking_uuid required The uuid of the coworking
     * 
     * @response {
     *  "status": true,
     *  "message": "Room list retrieved successfully",
     *  "data": {
     *    "current_page": 1,
     *    "data": [
     *      {
     *        "uuid": "dec41514-25a3-4080-8875-c9847b56db91",
     *        "name": "Amelia Bergstrom",
     *        "number": "382420111",
     *        "description": "Iusto consequuntur odio maiores occaecati quisquam perspiciatis iusto quas. Aliquam dolorem explicabo laboriosam id et. Voluptas ut pariatur molestias.",
     *        "price_per_minute": 50.19,
     *        "opening_hours": [],
     *        "address": null,
     *        "categories": [],
     *        "photos": [],
     *        "contacts": []
     *      },
     *      {
     *        "uuid": "49449299-2b86-4918-bd55-ce0a50d67f2f",
     *        "name": "Zane Heidenreich",
     *        "number": "93855346",
     *        "description": "Accusantium et repudiandae eos qui odit aut sequi. Sit quasi voluptatum cumque. Perferendis ut qui eius ratione quia beatae. Quia sunt fuga fuga.",
     *        "price_per_minute": 27.7,
     *        "opening_hours": [],
     *        "address": null,
     *        "categories": [],
     *        "photos": [],
     *        "contacts": []
     *      }
     *    ],
     *    "first_page_url": "http:\/\/localhost:9000\/api\/partner\/coworking\/a6a30696-e76b-4fea-8b5e-f1fd044e1aab\/room?page=1",
     *    "from": 1,
     *    "last_page": 1,
     *    "last_page_url": "http:\/\/localhost:9000\/api\/partner\/coworking\/a6a30696-e76b-4fea-8b5e-f1fd044e1aab\/room?page=1",
     *    "links": [
     *      {
     *        "url": null,
     *        "label": "&laquo; Previous",
     *        "active": false
     *      },
     *      {
     *        "url": "http:\/\/localhost:9000\/api\/partner\/coworking\/a6a30696-e76b-4fea-8b5e-f1fd044e1aab\/room?page=1",
     *        "label": "1",
     *        "active": true
     *      },
     *      {
     *        "url": null,
     *        "label": "Next &raquo;",
     *        "active": false
     *      }
     *    ],
     *    "next_page_url": null,
     *    "path": "http:\/\/localhost:9000\/api\/partner\/coworking\/a6a30696-e76b-4fea-8b5e-f1fd044e1aab\/room",
     *    "per_page": 15,
     *    "prev_page_url": null,
     *    "to": 2,
     *    "total": 2
     *  }
     *}
     * 
     * @param  Request $request
     * @param \App\Repositories\RoomRepository  $repository
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, RoomRepository $repository)
    {
      $response = $repository->getAllRoomsByCoworking($request->coworking_uuid);
      return response()->json($response);
    }

    public function indexAll(Request $request, RoomRepository $repository)
    {
      $response = $repository->getAllRooms();
      return response()->json($response);
    }

    public function listRoomByCoworking(Request $request, RoomRepository $repository){
      $response = $repository->allRoomsByCoworkingWithoutPaginate($request->coworking_uuid);
      return response()->json($response);
    }



    /**
     * store
     * 
     * This Method is used to create a new room
     *  
     * @group Partner
     * @subgroup Room
     * @authenticated
     * 
     * @queryParam coworking_uuid required The uuid of the coworking
     * 
     * @bodyParam name string required The name of the room
     * @bodyParam number string required The number of the room
     * @bodyParam description string required The description of the room
     * @bodyParam price_per_minute float required The price per minute of the room
     * 
     * @response {
     *   "status": true,
     *   "message": "Room created successfully",
     *   "data": {
     *     "name": "Room 1",
     *     "number": "1A",
     *     "description": "Room 1 description",
     *     "price_per_minute": 5.8,
     *     "uuid": "62277cb6-60c4-42b9-a13a-a754b964be10",
     *     "opening_hours": [],
     *     "address": null,
     *     "categories": [],
     *     "photos": [],
     *     "contacts": []
     *   }
     * }
     * 
     * @param  \App\Http\Requests\StoreRoomRequest  $request
     * @param \App\Repositories\RoomRepository  $repository
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRoomRequest $request, RoomRepository $repository)
    {
      
      $data                 = $request->validated();
      $data['operating_hours'] = "[[]]";
      // dd($data);
      $coworkingRepository = new CoworkingRepository();
      $coworking           = $coworkingRepository->findByUuid($request->coworking_uuid);
      $data['coworking_id'] = $coworking->id;
      $room                 = $repository->create($data);
      $response             = $repository->getAllRoomsByCoworking($request->coworking_uuid);
      return response()->json($response);
    }

    /**
     * show 
     * 
     * This method is used to show a room by uuid
     *
     * @group Room
     * @authenticated
     * 
     * @queryParam coworking_uuid required The uuid of the coworking
     * @queryParam uuid required The uuid of the room
     * 
     * @response {
     *   "status": true,
     *   "message": "Room retrieved successfully",
     *   "data": {
     *     "uuid": "0e20f3f5-3809-459d-86c4-af6ceab3ee5a",
     *     "name": "Ms. Alysa Bogan",
     *     "number": "993302118",
     *     "description": "Quibusdam esse dignissimos laudantium possimus. Ut quibusdam expedita recusandae et magni. Voluptas sed corporis temporibus. Dignissimos sit assumenda dolorum temporibus et et possimus consequuntur.",
     *     "price_per_minute": 99.64,
     *     "opening_hours": [],
     *     "address": null,
     *     "categories": [],
     *     "photos": [],
     *     "contacts": []
     *   }
     * }
     * 
     * @param  Request $request
     * @param \App\Repositories\RoomRepository  $repository
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, RoomRepository $repository)
    {
      $response = $repository->findByUuid($request->uuid);
      return $this->response('show',$response);
    }

    /**
     * update 
     * 
     * Update the specified resource in storage.
     * 
     * @group Partner
     * @subgroup Room
     * 
     * @queryParam coworking_uuid required The uuid of the coworking
     * @queryParam uuid required The uuid of the room
     * 
     * @bodyParam name string required The name of the room
     * @bodyParam number string required The number of the room
     * @bodyParam description string required The description of the room
     * @bodyParam price_per_minute float required The price per minute of the room
     *
     * @response {
     *   "status": true,
     *   "message": "Room updated successfully",
     *   "data": {
     *     "uuid": "72deb73b-8bde-4e27-9189-d58df392f98b",
     *     "name": "Room 1",
     *     "number": "1A",
     *     "description": "Room 1 description",
     *     "price_per_minute": 16.43,
     *     "opening_hours": [],
     *     "address": null,
     *     "categories": [],
     *     "photos": [],
     *     "contacts": []
     *   }
     * } 
     *
     * @param  \App\Http\Requests\UpdateRoomRequest  $request
     * @param \App\Repositories\RoomRepository  $repository
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRoomRequest $request, RoomRepository $repository)
    {
      $data = $request->validated();
      $response = $repository->updateByUuid($request->uuid, $data);
      return $this->response('update',$response);
    }
    
    /**
     * destroy
     * 
     * Remove the specified resource from storage.
     * 
     * @group Partner
     * @subgroup Room
     * @authenticated
     * 
     * @queryParam coworking_uuid required The uuid of the coworking
     * @queryParam uuid required The uuid of the room
     * 
     * @response {
     *  "status":true,
     *  "message":"Room deleted successfully",
     *  "data":[]
     * }
     *
     * @param \App\Repositories\RoomRepository  $repository
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, RoomRepository $repository)
    {
      $room = $repository->findByUuid($request->uuid);
      $coworking = Coworking::find($room->coworking_id);
      $repository->deleteByUuid($request->uuid);
      $response = $repository->getAllRoomsByCoworking($coworking->uuid);
      return response()->json($response);
    }


    /**
     * attach.category
     * 
     * This method is used to attach a category to a room
     * 
     * @group Partner
     * @subgroup Room
     * @authenticated
     * 
     * @queryParam uuid required The uuid of the room
     * 
     * @bodyParam category_uuid string required The uuid of the category
     * 
     * @response {
     *   "status": true,
     *   "message": "Category attached successfully",
     *   "data": [
     *     {
     *       "uuid": "cc9ca745-73cb-41c3-aee5-eaa2bed35fd6",
     *       "name": "Kody McLaughlin III",
     *       "description": "Est voluptatibus dicta ut iste sit autem. Molestias earum nesciunt repudiandae temporibus odio eius. Odio beatae saepe et illum dicta explicabo quae recusandae. Et fugiat totam laboriosam sunt."
     *     }
     *   ]
     * }
     *
     * @param RoomAttachOrDetachCategoryRequest $request
     * @param RoomRepository $repository
     * @return void
     */
    public function attachCategory(RoomAttachOrDetachCategoryRequest $request, RoomRepository $repository)
    {
      $this->resource = ['resource' => 'Category'];
      $response = $repository->attachCategory($request->uuid, $request->category_uuid);
      return $this->response('attach',$response);
    }

    /**
     * detach.category
     * 
     * This method is used to detach a category from a room
     * 
     * @group Partner
     * @subgroup Room
     * @authenticated
     *
     * @queryParam uuid required The uuid of the room
     * @bodyParam category_uuid string required The uuid of the category
     * 
     * @response {
     *  "status":true,
     *  "message":"Category detached successfully",
     *  "data":[]
     * }
     * 
     * @param RoomAttachOrDetachCategoryRequest $request
     * @param RoomRepository $repository
     * @return void
     */
    public function detachCategory(RoomAttachOrDetachCategoryRequest $request, RoomRepository $repository)
    {
      $this->resource = ['resource' => 'Category'];
      $response = $repository->detachCategory($request->uuid, $request->category_uuid);
      return $this->response('detach',$response);
    }

    /**
     * availability
     * 
     * This method is used to get the availability of a room by date
     * 
     * @group Room
     * @authenticated
     * 
     * @queryParam uuid required The uuid of the room
     * @queryParam date required The date of the availability. Example: 2022-11-11
     * 
     * @response {
     *   "status": true,
     *   "message": "Room list retrieved successfully",
     *   "data": [
     *     {
     *       "opening": "08:00",
     *       "closing": "09:00"
     *     },
     *     {
     *       "opening": "14:00",
     *       "closing": "18:00"
     *     }
     *   ]
     * }
     *
     * @param Request $request
     * @param RoomRepository $repository
     * @return void
     */
    public function availability(Request $request, RoomRepository $repository)
    {
      $response = $repository->getAvailability($request->uuid, $request->date);
      return $this->response('list',$response);
    }

    public function availabilityBulk(Request $request, RoomRepository $repository)
    {
      $request->validate([
        'dateBulk' => 'array|required',
        'dateBulk.*' => 'date_format:Y-m-d'
      ]);

      $response = $repository->getAvailabilityBulk($request->uuid, $request->dateBulk);
      return $this->response('list',$response);
    }
}
