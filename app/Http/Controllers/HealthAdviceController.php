<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreHealthAdviceRequest;
use App\Http\Requests\UpdateHealthAdviceRequest;
use App\Models\HealthAdvice;
use App\Repositories\HealthAdviceRepository;
use Illuminate\Http\Request;

class HealthAdviceController extends Controller
{

    public function __construct()
    {
      $this->resource = ['resource' => 'HealthAdvice'];
    }

    /**
     * 
     * index 
     * 
     * Display a listing of the resource.
     * 
     * @group Health Advice
     * @authenticated
     *
     * @response {
     *  "current_page": 1,
     *  "data": [
     *    {
     *      "uuid": "257a6a55-6263-43d9-8232-629b4da1280c",
     *      "name": "Conselhor Federal de Biomedicina",
     *      "initials": "CFBM"
     *    },
     *    {
     *      "uuid": "d2a99b96-938d-4d96-914d-b051c418fe75",
     *      "name": "Conselho Regional de Biomedicina",
     *      "initials": "CRBM"
     *    }
     *  ],
     *  "first_page_url": "http:\/\/localhost:9000\/api\/fusion\/health-advice?page=1",
     *  "from": 1,
     *  "last_page": 2,
     *  "last_page_url": "http:\/\/localhost:9000\/api\/fusion\/health-advice?page=2",
     *  "links": [
     *    {
     *      "url": null,
     *      "label": "&laquo; Previous",
     *      "active": false
     *    },
     *    {
     *      "url": "http:\/\/localhost:9000\/api\/fusion\/health-advice?page=1",
     *      "label": "1",
     *      "active": true
     *    },
     *    {
     *      "url": "http:\/\/localhost:9000\/api\/fusion\/health-advice?page=2",
     *      "label": "2",
     *      "active": false
     *    },
     *    {
     *      "url": "http:\/\/localhost:9000\/api\/fusion\/health-advice?page=2",
     *      "label": "Next &raquo;",
     *      "active": false
     *    }
     *  ],
     *  "next_page_url": "http:\/\/localhost:9000\/api\/fusion\/health-advice?page=2",
     *  "path": "http:\/\/localhost:9000\/api\/fusion\/health-advice",
     *  "per_page": 15,
     *  "prev_page_url": null,
     *  "to": 15,
     *  "total": 21
     *}
     * 
     */
    public function index(HealthAdviceRepository $repository)
    {
      $response = $repository->get();  
      return response()->json($response);
    }

    /**
     * store
     * 
     * Store a newly created resource in storage.
     * 
     * @group Fusion
     * @subgroup Health Advice
     * @authenticated
     * 
     * @bodyParam name string required Name of the Health Advice. Example: Cuidados com a saúde
     * @bodyParam initials string required Initials of the Health Advice. Example: CRM
     * 
     * @response {
     *  "status":true,
     *  "message":"HealthAdvice created successfully",
     *  "data":{
     *     "name":"Cuidados com a saúde",
     *     "initials":"CRM",
     *     "uuid":"b8a2e066-21cd-4a0d-a84d-eb0e99d2f512"
     *   }
     * }
     */
    public function store(StoreHealthAdviceRequest $request, HealthAdviceRepository $repository)
    {
      $response = $repository->create($request->validated());
      return $this->response('store', $response);
    }

    /**
     * show
     * 
     * Display the specified resource.
     * 
     * @group Fusion
     * @subgroup Health Advice
     * @authenticated
     * 
     * @queryParam uuid string required Uuid of the Health Advice. Example: 18c88df0-a4b8-4871-b894-ffc023c8802b
     * 
     * @response {
     *  "status":true,
     *  "message":"HealthAdvice retrieved successfully",
     *  "data":{
     *    "uuid":"18c88df0-a4b8-4871-b894-ffc023c8802b",
     *    "name":"Cuidados com a sa\u00fade",
     *    "initials":"CRM"
     *  }
     * }
     */
    public function show(Request $request, HealthAdviceRepository $repository)
    {
      $response = $repository->findByUuid($request->uuid);
      return $this->response('show', $response);
    }

    /**
     * update
     * 
     * Update the specified resource in storage.
     *
     * @group Fusion
     * @subgroup Health Advice
     * @authenticated
     * 
     * @queryParam uuid string required Uuid of the Health Advice. Example: 18c88df0-a4b8-4871-b894-ffc023c8802b
     * 
     * @bodyParam name string required Name of the Health Advice. Example: Cuidados com a saúde
     * @bodyParam initials string required Initials of the Health Advice. Example: CRM
     * 
     * @response {
     *  "status":true,
     *  "message":"HealthAdvice updated successfully",
     *  "data":{
     *    "uuid":"21f280dd-e12a-469d-a006-ac8764996cb0",
     *    "name":"Cuidados com a sa\u00fade updated",
     *    "initials":"CRM"
     *  }
     * }
     */
    public function update(UpdateHealthAdviceRequest $request, HealthAdviceRepository $repository)
    {
      $data = $request->validated();
      $response = $repository->updateByUuid($request->uuid, $data);
      return $this->response('update', $response);
    }

    /**
     * 
     * destroy
     * 
     * Remove the specified resource from storage.
     * 
     * @group Fusion
     * @subgroup Health Advice
     * @authenticated
     * 
     * @queryParam uuid string required Uuid of the Health Advice. Example: 18c88df0-a4b8-4871-b894-ffc023c8802b
     * 
     * @response {
     *  "status":true,
     *  "message":"HealthAdvice deleted successfully",
     *  "data":[]
     * }
     */
    public function destroy(Request $request, HealthAdviceRepository $repository)
    {
      $response = $repository->deleteByUuid($request->uuid);
      return $this->response('destroy', $response);
    }
}
