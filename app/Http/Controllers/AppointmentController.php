<?php

namespace App\Http\Controllers;

use App\Exports\AppointmentsExport;
use App\Exports\FinanceExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Controller;
use App\Http\Requests\ListAppointmentBySchedule;
use App\Http\Requests\StoreAppointmentBulkRequest;
use App\Http\Requests\StoreAppointmentRequest;
use App\Http\Requests\UpdateAppointmentRequest;
use App\Http\Requests\IndexAppointmentRequest;
use App\Http\Requests\IndexOrderedAppointmentRequest;
use App\Models\Appointment;
use App\Repositories\AppointmentRepository;
use App\Repositories\RoomRepository;
use App\Repositories\UserRepository;
use App\Services\AppointmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AppointmentController extends Controller
{
  
    public function __construct()
    {
      $this->resource = ['resource' => 'Appointment'];
    }

    /**
     * index
     * 
     * Display a listing of the resource.
     * 
     * @group Customer
     * @subgroup Appointment
     * @authenticated
     * 
     * @urlParam dateInit required The date of appointments list. Example: 2022-11-19
     * @urlParam dateEnd required The date of appointments list. Example: 2022-12-28
     * 
     * @response {
     *  "status": true,
     *  "message": "Appointment list retrieved successfully",
     *  "data": [
     *    {
     *      "uuid": "cd1356dc-57ee-4dd5-bf71-a799d36f8e3c",
     *      "patient_name": "Prof. Liliane Gaylord PhD",
     *      "patient_phone": "424-251-5716",
     *      "time_init": "2022-11-19 08:00",
     *      "time_end": "2022-11-19 09:15",
     *      "time_total": 75,
     *      "status": "scheduled",
     *      "value_per_minute": "1.14",
     *      "value_total": "85.50",
     *      "checkin_at": null,
     *      "checkout_at": null,
     *      "finished_at": null,
     *      "canceled_at": null,
     *      "schedule_uuid": "6937025e-0862-4233-81bd-9286ad5e5f61",
     *      "specialist": {
     *        "uuid": "c15edb7c-b933-412b-8452-d8e3d2a389e2",
     *        "name": "customer",
     *        "gender": null,
     *        "email": "customer@fusion.com",
     *        "role_name": "customer",
     *        "photo": null,
     *        "contacts": []
     *      },
     *      "room": {
     *        "uuid": "0fbfb3a6-51d9-4f56-acba-487a62666c2f",
     *        "name": "Zoila Spinka",
     *        "number": "137",
     *        "price_per_minute": 96.83,
     *        "photos": [],
     *        "contacts": []
     *      }
     *    }
     *  ]
     *} 
     *
     * @return \Illuminate\Http\Response
     */
    public function indexAppointment(IndexAppointmentRequest $request, AppointmentRepository $repository)
    {
      $appointments = $repository->listByCustomer($request->dateInit, $request->dateEnd);
      return $this->response('list', $appointments);
    }

    public function indexAppointmentByAdmin(IndexAppointmentRequest $request, AppointmentRepository $repository, $user_uuid)
    {
      $appointments = $repository->listForCustomerByAdmin($request->dateInit, $request->dateEnd, $user_uuid);
      return $this->response('list', $appointments);
    }

    public function salesTurnover(Request $request, AppointmentService $service)
    {
      // dd($request->dateInit, $request->dateEnd);
      $response = $service->getSalesTurnoverData($request->dateInit, $request->dateEnd);
      return $response;
      
    }

    

    public function frequentSchedules(Request $request, AppointmentRepository $repository)
    {
      $appointments = $repository->getSchedulesByDate($request->dateInit, $request->dateEnd);
      return $appointments;
    }


    /**
     * list by schedule
     * 
     * @group Partner
     * @subgroup Appointment 
     * @authenticated
     * 
     * @urlParam schedule_uuid required The uuid of schedule. Example: 6937025e-0862-4233-81bd-9286ad5e1100
     * @urlParam dateInit required The date of appointments list. Example: 2023-06-10
     * @urlParam dateEnd required The date of appointments list. Example: 2023-12-12
     * @response {
     *  "status": true,
     *  "message": "Appointment list retrieved successfully",
     *  "data": [
     *    {
     *      "uuid": "cd1356dc-57ee-4dd5-bf71-a799d36f848533",
     *      "patient_name": "Prof. Luiz F. Lima",
     *      "patient_phone": "424-251-5716",
     *      "time_init": "2022-11-19 08:00",
     *      "time_end": "2022-11-19 09:15",
     *      "time_total": 75,
     *      "status": "scheduled",
     *      "value_per_minute": "1.14",
     *      "value_total": "85.50",
     *      "checkin_at": null,
     *      "checkout_at": null,
     *      "finished_at": null,
     *      "canceled_at": null,
     *      "schedule_uuid": "6937025e-0862-4233-81bd-9286ad5e5f61",
     *      "specialist": {
     *        "uuid": "c15edb7c-b933-412b-8452-d8e3d2a389e2",
     *        "name": "customer",
     *        "gender": null,
     *        "email": "customer@fusion.com",
     *        "role_name": "customer",
     *        "photo": null,
     *        "contacts": []
     *      },
     *      "room": {
     *        "uuid": "0fbfb3a6-51d9-4f56-acba-487a62666c2f",
     *        "name": "Spinka",
     *        "number": "137",
     *        "price_per_minute": 96.83,
     *        "photos": [],
     *        "contacts": []
     *      }
     *    }
     *  ]
     * } 
     * 
     */
    public function listBySchedule(Request $request, AppointmentRepository $repository)
    {
      $appointments = $repository->listBySchedule($request->schedule_uuid, $request->dateInit, $request->dateEnd);
      return $this->response('list', $appointments);
    }

    /**
     * search
     * 
     * This resource is responsible for search appointments 
     * 
     * @group Customer
     * @subgroup Appointment 
     * @authenticated
     * 
     * @bodyParam date required Date of appointments. Example: 2022-11-20
     * @bodyParam filter required Filter Type. Example: all,checkin,checkout,canceled,finished,coworking
     * @bodyParam uuid The Uuid of Room to search specifics endpoints. Example: 323135f7-1a47-4690-8a64-063fc98add02
     * @bodyParam schedule_uuid The UUID of schedule. Examplem: Example: 323135f7-1a47-4690-8a64-063fc98add03
     * 
     * @response {
     *  "status": true,
     *  "message": "Appointment list retrieved successfully",
     *  "data": {
     *    "current_page": 1,
     *    "data": [
     *      {
     *        "uuid": "133a7a51-e7ba-4e86-8348-11d0c4b689fa",
     *        "patient_name": "Herminia O'Keefe",
     *        "patient_phone": "+1-281-950-9270",
     *        "time_init": "2022-11-20 12:00",
     *        "time_end": "2022-11-20 14:00",
     *        "time_total": 75,
     *        "status": "scheduled",
     *        "value_per_minute": "1.41",
     *        "value_total": "105.75",
     *        "checkin_at": null,
     *        "checkout_at": null,
     *        "finished_at": null,
     *        "canceled_at": null,
     *        "schedule_uuid": "8af3bef8-fa53-4ba2-bc91-053e4de42c57",
     *        "specialist": {
     *          "uuid": "9c77632e-df26-4737-ae9c-8e90467e04b8",
     *          "name": "customer",
     *          "gender": null,
     *          "email": "customer@fusion.com",
     *          "role_name": "customer",
     *          "photo": null,
     *          "contacts": []
     *        },
     *        "room": {
     *          "uuid": "33c8b27a-474c-46cd-a1fd-dcb81b5e0ef6",
     *          "name": "Ned Bartoletti",
     *          "number": "9498",
     *          "price_per_minute": 43.26,
     *          "photos": [],
     *          "contacts": []
     *        }
     *      }
     *    ],
     *    "first_page_url": "http:\/\/localhost:9000\/api\/partner\/appointment\/search?page=1",
     *    "from": 1,
     *    "last_page": 1,
     *    "last_page_url": "http:\/\/localhost:9000\/api\/partner\/appointment\/search?page=1",
     *    "links": [
     *      {
     *        "url": null,
     *        "label": "&laquo; Previous",
     *        "active": false
     *      },
     *      {
     *        "url": "http:\/\/localhost:9000\/api\/partner\/appointment\/search?page=1",
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
     *    "path": "http:\/\/localhost:9000\/api\/partner\/appointment\/search",
     *    "per_page": 30,
     *    "prev_page_url": null,
     *    "to": 1,
     *    "total": 1
     *  }
     *}
     * 
     * @param Request $request
     * @param AppointmentService $service
     * @return void
     */
    public function search(Request $request, AppointmentService $service)
    {
      if(auth()->check());
      $data = $request->validate([
        'date'          => 'required|date_format:Y-m-d',
        'filter'        => 'required|in:all,checkin,checkout,canceled,finished,coworking',
        'uuid'          => 'sometimes|uuid',
        'schedule_uuid' => 'required|uuid|exists:schedules,uuid'
      ]);
      
      $appointments = $service->searchByScheduleAndPartner($data);
      return $this->response('list', $appointments);
    }

    public function listByRoom(Request $request, AppointmentRepository $repository)
    {
      $roomRepository = new RoomRepository();
      $room = $roomRepository->findByUuid($request->room_uuid);
      $appointments = $repository->listByRoom($room->id ,$request->date);
      return response()->json($appointments);
    }

    /**
     * store 
     * 
     * This resource is responsible for store a new appointment.
     *
     * @group Customer
     * @subgroup Appointment 
     * @authenticated
     * 
     * @urlParam customer_uuid required The UUID of customer user
     * @urlParam uuid required The UUID of appointment uuid
     *
     * @bodyParam patient_name required The patient name
     * @bodyParam patient_phone required The patient phone
     * @bodyParam room_uuid required The room UUID. Example: 323135f7-1a47-4690-8a64-063fc98add02
     * @bodyParam time_init required The appointment start time. Example: 2022-11-20 14:00:00
     * @bodyParam time_end required The appointment end time. Example: 2022-11-20 17:00:00
     * 
     * 
     * @response {
     * "status": true,
     * "message": "Appointment retrieved successfully",
     * "data": {
     *   "uuid": "22a78733-21b9-4d16-a793-bd2c593b42ab",
     *   "patient_name": "Prof. Destinee Schuppe PhD",
     *   "patient_phone": "+16829970665",
     *   "time_init": "2022-11-19 08:00",
     *   "time_end": "2022-11-19 09:15",
     *   "time_total": 75,
     *   "status": "scheduled",
     *   "value_per_minute": "1.48",
     *   "value_total": "111.00",
     *   {
     *     "status": true,
     *     "message": "Appointment created successfully",
     *     "data": {
     *       "patient_name": "Teste",
     *       "patient_phone": "999999999",
     *       "time_total": 180,
     *       "value_per_minute": 91.2,
     *       "value_total": 16416,
     *       "status": "pending",
     *       "time_init": "2022-11-20 14:00",
     *       "time_end": "2022-11-20 17:00",
     *       "uuid": "f91b8484-5381-4f7a-bcdc-424792fe3d24",
     *       "schedule_uuid": "eb7e068a-7eac-4db2-b6d2-625bd02d94c2",
     *       "specialist": {
     *         "uuid": "19d640fc-3021-49ee-abdb-570a828649b0",
     *         "name": "customer",
     *         "gender": null,
     *         "email": "customer@fusion.com",
     *         "role_name": "customer",
     *         "photo": null,
     *         "contacts": []
     *       },
     *       "room": {
     *         "uuid": "a7c7c863-53e8-44bb-b8de-b4fb231d3f3b",
     *         "name": "Mrs. Erika Mohr Jr.",
     *         "number": "68",
     *         "price_per_minute": 91.2,
     *         "photos": [],
     *         "contacts": []
     *       }
     *     }
     *   }
     *   "checkin_at": null,
     *   "checkout_at": null,
     *   "finished_at": null,
     *   "canceled_at": null,
     *   "schedule_uuid": "b44671e6-8464-4301-803d-0cd37388ab29",
     *   "specialist": {
     *     "uuid": "6152fd20-391b-4190-ac53-cb4aa8114026",
     *     "name": "customer",
     *     "gender": null,
     *     "email": "customer@fusion.com",
     *     "role_name": "customer",
     *     "photo": null,
     *     "contacts": []
     *   },
     *   "room": {
     *     "uuid": "ef8488a6-66c1-4afb-b0c5-fff00e2280fc",
     *     "name": "Prof. Cristina Koch V",
     *     "number": "612161",
     *     "price_per_minute": 36.12,
     *     "photos": [],
     *     "contacts": []
     *   }
     *  }
     * }
     * 
     * @param  \App\Http\Requests\StoreAppointmentRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreAppointmentRequest $request, AppointmentService $service)
    {
      $data = $request->validated();
      $response = $service->store($data);
      return $this->response('store', $response);  
    }

    public function storeBulk(StoreAppointmentBulkRequest $request, AppointmentService $service)
    {
      $data     = $request->validated();
      $response = $service->storeBulk($data);
      return $this->response('store', $response);  
    }

    /**
     * show
     *  
     * This resource is responsible for show specific appointment
     * 
     * @group Customer
     * @subgroup Appointment
     * @authenticated
     *
     * @urlParam customer_uuid required The UUID of customer user
     * @urlParam uuid required The UUID of appointment uuid
     * 
     * @response {
     *  "status": true,
     *  "message": "Appointment retrieved successfully",
     *  "data": {
     *    "uuid": "22a78733-21b9-4d16-a793-bd2c593b42ab",
     *    "patient_name": "Prof. Destinee Schuppe PhD'",
     *    "patient_phone": "+16829970665",
     *    "time_init": "2022-11-19 08:00",
     *    "time_end": "2022-11-19 09:15",
     *    "time_total": 75,
     *    "status": "scheduled",
     *    "value_per_minute": "1.48",
     *    "value_total": "111.00",
     *    "checkin_at": null,
     *    "checkout_at": null,
     *    "finished_at": null,
     *    "canceled_at": null,
     *    "schedule_uuid": "b44671e6-8464-4301-803d-0cd37388ab29",
     *    "specialist": {
     *      "uuid": "6152fd20-391b-4190-ac53-cb4aa8114026",
     *      "name": "customer",
     *      "gender": null,
     *      "email": "customer@fusion.com",
     *      "role_name": "customer",
     *      "photo": null,
     *      "contacts": []
     *    },
     *    "room": {
     *      "uuid": "ef8488a6-66c1-4afb-b0c5-fff00e2280fc",
     *      "name": "Prof. Cristina Koch V",
     *      "number": "612161",
     *      "price_per_minute": 36.12,
     *      "photos": [],
     *      "contacts": []
     *    }
     *  }
     *}
     * @param  \App\Models\Appointment  $appointment
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, AppointmentRepository $repository)
    {
      $response = $repository->findByUuidAndCustomer($request->uuid, $request->customer_uuid);
      return $this->response('show', $response);
    }

    /**
     * destroy
     * 
     * Remove the specified resource from storage.
     * 
     * @group Customer
     * @subgroup Appointment
     * @authenticated
     * 
     * @urlParam uuid required The UUID of appointment uuid
     * 
     * @response {
     * "status":true,
     * "message":"Appointment deleted successfully",
     * "data":[]
     * }
     * @param  \App\Models\Appointment  $appointment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, AppointmentRepository $repository)
    {
      $response = $repository->deleteByUuid($request->uuid);
      return $this->response('destroy', $response);
    }

    /**
     * update.status
     * 
     * This resource is responsible to update a specific appointment
     * 
     * @group Partner
     * @subgroup Appointment
     * @authenticated
     * 
     * @urlParam uuid required The UUID of appointment uuid
     * 
     * @bodyParam status required The status to update. Example: checkin, checkout, canceled or finished
     * 
     * @response {
     *   "status": true,
     *   "message": "Appointment updated successfully",
     *   "data": {
     *     "uuid": "31b7cce0-ca3f-48c6-93db-3cb6cb2fda72",
     *     "patient_name": "Ms. Gracie Corkery",
     *     "patient_phone": "+1 (256) 330-1205",
     *     "time_init": "2022-11-20 09:00",
     *     "time_end": "2022-11-20 12:00",
     *     "time_total": 75,
     *     "status": "checkin",
     *     "value_per_minute": "1.13",
     *     "value_total": "84.75",
     *     "checkin_at": "2022-11-20 09:35:00",
     *     "checkout_at": null,
     *     "finished_at": null,
     *     "canceled_at": null,
     *     "schedule_uuid": "c202aa39-7d78-43e1-93f2-9b482c10b42e",
     *     "specialist": {
     *       "uuid": "3f3d4080-d475-47bd-963f-4e56e93b92d4",
     *       "name": "customer",
     *       "gender": null,
     *       "email": "customer@fusion.com",
     *       "role_name": "customer",
     *       "photo": null,
     *       "contacts": []
     *     },
     *     "room": {
     *       "uuid": "f5176549-ff96-4f3f-b419-0cb1def382dc",
     *       "name": "Mr. Trent McKenzie V",
     *       "number": "779504820",
     *       "price_per_minute": 49.99,
     *       "photos": [],
     *       "contacts": []
     *     }
     *   }
     * }
     * @param  \App\Models\Appointment  $appointment
     * @return \Illuminate\Http\Response
     */
    public function updateStatus(Request $request, AppointmentService $service)
    {
      $data = $request->validate([
        'date_time' => 'required|date_format:Y-m-d H:i:s',
        'status' => 'required|in:pending,paid,canceled,finished'
      ]);

      $response = $service->updateStatus($request->uuid, $data);
      // dd($response);
      return $this->response('update', $response);
    }

    public function export(IndexOrderedAppointmentRequest $request) 
    {      
      // dd($request->dateInit);
      $title = 'Agendamentos Fusion';
      return Excel::download(new AppointmentsExport, $title.'.xlsx');
    }

    public function financeExport(IndexOrderedAppointmentRequest $request) 
    {
      // $title = 'Agendamentos fusion - Financeiro, '. $request->dateInit . ' até ' . $request->dateEnd;
      $title = 'Agendamentos fusion - Financeiro';
      return Excel::download(new FinanceExport, $title.'.xlsx');
    }

    public function indexOrderedAppointments(IndexOrderedAppointmentRequest $request, AppointmentService $service){
      // dd($request->dateInit);
      $appointments = $service->listAll($request);
      return $this->response('list', $appointments);
    }
}
