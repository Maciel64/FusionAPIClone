<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBillingRequest;
use App\Http\Requests\UpdateBillingRequest;
use App\Models\Billing;
use App\Repositories\BillingRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BillingController extends Controller
{

    public function __construct()
    {
      $this->resource = ['resource' => 'Billing'];
    }

    /**
     * index
     * 
     * Display a listing of the resource.
     * 
     * @group Customer
     * @subgroup Billing
     * @authenticated
     * 
     * @response {
     *   "current_page": 1,
     *   "data": [
     *     {
     *       "uuid": "4fd8fde6-01bb-4571-b9db-56def8b80d16",
     *       "model_type": "App\\Models\\Appointment",
     *       "model_id": 1,
     *       "amount": 111,
     *       "paid": "paid",
     *       "payment_method": "Credit Card - Pagar.me",
     *       "payment_at": "2023-01-10 23:27:21",
     *       "order_id": "or_28dN9w7CLU79kDjL",
     *       "order_code": "62LVFN7I4R",
     *       "closed": 1,
     *       "created_at": "2022-12-10T23:27:21.000000Z",
     *       "updated_at": "2023-01-10T23:27:21.000000Z"
     *     },
     *     {
     *       "uuid": "e7c21011-473c-44c7-8bc6-4c8eeb6c0255",
     *       "model_type": "App\\Models\\Plan",
     *       "model_id": 1,
     *       "amount": 5.36,
     *       "paid": "paid",
     *       "payment_method": "Credit Card - Pagar.me",
     *       "payment_at": "2023-01-10 23:27:21",
     *       "order_id": "or_28dN9w7CLU79kDjL",
     *       "order_code": "62LVFN7I4R",
     *       "closed": 0,
     *       "created_at": "2022-12-10T23:27:21.000000Z",
     *       "updated_at": "2023-01-10T23:27:21.000000Z"
     *     }
     *   ],
     *   "first_page_url": "http:\/\/localhost:9000\/api\/customer\/billing?page=1",
     *   "from": 1,
     *   "last_page": 1,
     *   "last_page_url": "http:\/\/localhost:9000\/api\/customer\/billing?page=1",
     *   "links": [
     *     {
     *       "url": null,
     *       "label": "&laquo; Previous",
     *       "active": false
     *     },
     *     {
     *       "url": "http:\/\/localhost:9000\/api\/customer\/billing?page=1",
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
     *   "path": "http:\/\/localhost:9000\/api\/customer\/billing",
     *   "per_page": 30,
     *   "prev_page_url": null,
     *   "to": 2,
     *   "total": 2
     * }
     * 
     *
     * @return \Illuminate\Http\Response
     */
    public function index(BillingRepository $repository)
    {
      $userId = Auth()->user()->id;
      $response = $repository->getByCustomerId($userId);
      return response()->json($response);
    }

    /**
     * show
     * 
     * Display the specified resource.
     * 
     * @group Customer
     * @subgroup Billing
     * @authenticated
     * 
     * @queryParam uuid required The uuid of the billing. Example: 31dfe4a7-d438-42f0-8d12-99ddaefdd498
     * 
     * @response {
     *   "status": true,
     *   "message": "Billing retrieved successfully",
     *   "data": {
     *     "uuid": "31dfe4a7-d438-42f0-8d12-99ddaefdd498",
     *     "model_type": "App\\Models\\Appointment",
     *     "model_id": 1,
     *     "amount": 67.5,
     *     "paid": "paid",
     *     "payment_method": "Credit Card - Pagar.me",
     *     "payment_at": "2023-01-10 23:29:59",
     *     "order_id": "or_28dN9w7CLU79kDjL",
     *     "order_code": "62LVFN7I4R",
     *     "closed": 1,
     *     "created_at": "2022-12-10T23:29:59.000000Z",
     *     "updated_at": "2023-01-10T23:29:59.000000Z"
     *   }
     * }
     *
     * @param  \App\Models\Billing  $billing
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, BillingRepository $repository)
    {
      $response = $repository->findByUuid($request->uuid);
      return $this->response('show', $response);
    }

}
