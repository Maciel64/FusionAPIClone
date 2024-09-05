<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePlanRequest;
use App\Http\Requests\UpdatePlanRequest;
use App\Models\Plan;
use App\Repositories\PlanRepository;
use App\Services\PlanService;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    public function __construct()
    {
      $this->resource = ['resource' => 'Plan'];  
    }

    /**
     * 
     * index
     * 
     * Display a listing of the resource.
     * 
     * @group Plan
     * @authenticated
     * 
     * @response {
     *  "status":true,
     *  "message":"Plan list retrieved successfully",
     *  "data":[
     *    {
     *      "uuid":"11f80443-846c-489a-91b0-3c6303779860",
     *      "name":"Victoria Hand",
     *      "price":"12.65",
     *      "description":"Odio odit voluptas provident voluptatum sit. Voluptates facere magnam aliquam itaque et enim quis. Modi autem ipsum ut alias. Aut soluta voluptatem impedit laboriosam autem.","trial_period_days":29,"active":true},{"uuid":"184b51a8-7b49-43aa-8f72-aba75ce6129c","name":"Paxton Stanton","price":"96.29","description":"Vel animi fugit non. Harum consequatur velit non beatae. Dolor sit fugiat est quod consequatur. Ullam facere nobis sint saepe deserunt reprehenderit fugit.","trial_period_days":30,"active":true},{"uuid":"3a53d9de-0335-46ab-863b-ea2190ec3e36","name":"Prof. Chaim Zemlak I","price":"44.64","description":"Voluptatem repellendus eaque non quasi. Eum sunt modi ullam expedita. Ipsa optio in alias quae ut.",
     *      "trial_period_days":21,
     *      "active":false
     *     }
     *  ]
     * }
     *
     * @return \Illuminate\Http\Response
     */
    public function index(PlanRepository $repository)
    {
      $response = $repository->get();
      return $this->response('list', $response);
    }

    /**
     * 
     * store
     * 
     * Store a newly created resource in storage.
     * 
     * @group Fusion
     * @subgroup Plan
     * @authenticated
     * 
     * @bodyParam name string required Name of the plan. Example: Test Plan
     * @bodyParam price string required Price of the plan. Example: 100
     * @bodyParam description string required Description of the plan. Example: Test Plan Description
     * 
     * @response {
     *  "status":true,
     *  "message":"Plan created successfully",
     *  "data":{
     *    "name":"Test Plan",
     *    "price":100,
     *    "description":"Test Plan Description",
     *    "uuid":"ecb2725a-929b-4f64-9029-f89288ae65da"
     *  }
     * }
     */
    public function store(StorePlanRequest $request, PlanService $service)
    {
      $this->authorize('create', Plan::class);
      $response = $service->store($request->validated());
      return $this->response('store', $response);
    }

    /**
     * 
     * update
     * 
     * Display the specified resource.
     * 
     * @group Fusion
     * @subgroup Plan
     * @authenticated
     * 
     * @urlParam uuid string required The uuid of the plan. Example: 11f80443-846c-489a-91b0-3c6303779860
     * 
     * @response {
     *  "status":true,
     *  "message":"Plan retrieved successfully",
     *  "data":{
     *    "uuid":"12d675c2-ed53-422d-837e-96b5997677e2",
     *    "name":"Abbey Crooks",
     *    "price":"26.91",
     *    "description":"Est adipisci aut enim cupiditate aut ut. Suscipit non ex possimus quam impedit aut vero. Sit vero provident nihil sint illum nemo.",
     *    "trial_period_days":8,
     *    "active":false
     *  }
     * }
     *
     * @param  \App\Models\Plan  $plan
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, PlanRepository $repository)
    {
     $response = $repository->findByUuid($request->uuid);
      return $this->response('show', $response);
    }


    /**
     * 
     * update
     * 
     * Update the specified resource in storage.
     * 
     * @group Fusion
     * @subgroup Plan
     * @authenticated
     *
     * @bodyParam name string Name of the plan. Example: Test Plan
     * @bodyParam price string Price of the plan. Example: 100
     * @bodyParam description string Description of the plan. Example: Test Plan Description
     * 
     * @response {
     *  "status":true,
     *  "message":"Plan updated successfully",
     *  "data":[]
     * }
     * 
     * @param  \App\Http\Requests\UpdatePlanRequest  $request
     * @param  \App\Models\Plan  $plan
     * @return \Illuminate\Http\Response
     */
    public function update(UpdatePlanRequest $request, PlanService $service)
    {
      $data = $request->validated();
      $plan = $service->update($request->uuid, $data);
      return $this->response('update', $plan);
    }

    /**
     * 
     * destroy
     * 
     * Remove the specified resource from storage.
     * 
     * @group Fusion
     * @subgroup Plan
     * @authenticated
     * 
     * @urlParam uuid string required The uuid of the plan. Example: 11f80443-846c-489a-91b0-3c6303779860
     * 
     * @response {
     * "status":true,
     * "message":"Plan deleted successfully",
     * "data":[]
     * }
     *
     * @param  \App\Models\Plan  $plan
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, PlanRepository $repository)
    {
      $plan = $repository->findByUuid($request->uuid);
      $this->authorize('delete', $plan);
      $response = $plan->delete();
      return $this->response('destroy', $response);
    }

}
