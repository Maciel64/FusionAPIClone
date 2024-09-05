<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexCustomerRequest;
use App\Http\Requests\RegisterCustomerRequest;
use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\SearchCustomerRequest;
use App\Http\Requests\CompleteUserDataRequest;
use App\Models\Card;
use App\Models\Subscription;
use App\Models\User;
use App\Services\CardService;
use App\Services\CustomerService;
use App\Services\SubscriptionService;
use App\Services\UserService;
use Illuminate\Database\Console\Migrations\RollbackCommand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class CustomerController extends Controller
{
  /**
   * index 
   * 
   * List all customers.
   * 
   * @group Customer
   * @authenticated
   *
   * @bodyParam name string The customer name. Example: John Doe
   * @bodyParam email string The customer email. Example john.doe@fusion.com:
   * @bodyParam verified boolean required The customer account verification. Example: true or false
   * 
   * @response {
   *  "status": true,
   *  "message": "response.index.success",
   *  "data": {
   *    "current_page": 1,
   *    "data": [
   *      {
   *        "uuid": "fbec513f-aa90-40cf-bcaf-32de9852cdf9",
   *        "name": "Luiz Felipe",
   *        "email": "luiz.customer@fusion.com",
   *        "role_name": "customer",
   *        "photo": null,
   *        "address": {
   *          "uuid": "7871bcfe-4ac3-4893-8304-e6ac20980c49",
   *          "line_1": "Rua 1",
   *          "line_2": "test",
   *          "city": "test",
   *          "state": "SP",
   *          "country": "BR",
   *          "neighborhood": null,
   *          "zip_code": "12345678"
   *        },
   *        "schedule": null,
   *        "advice": {
   *          "uuid": "098e19d7-0889-4afa-b211-ba37098b25e8",
   *          "advice_code": "884455",
   *          "health_advice": "CRM"
   *        },
   *        "contacts": [
   *          {
   *            "uuid": "421abe13-7ff1-48c7-aa81-16cd69679751",
   *            "type": "home_phone",
   *            "country_code": "55",
   *            "area_code": "11",
   *            "number": "999999999"
   *          },
   *          {
   *            "uuid": "708f603b-fc41-450d-be18-e0510f5b5933",
   *            "type": "mobile_phone",
   *            "country_code": "55",
   *            "area_code": "11",
   *            "number": "999999999"
   *          }
   *        ],
   *        "workspace": null
   *      }
   *    ],
   *    "first_page_url": "http://localhost:7000/api/fusion/customer/search?page=1",
   *    "from": 1,
   *    "last_page": 1,
   *    "last_page_url": "http://localhost:7000/api/fusion/customer/search?page=1",
   *    "links": [
   *      { "url": null, "label": "&laquo; Previous", "active": false },
   *      {
   *        "url": "http://localhost:7000/api/fusion/customer/search?page=1",
   *        "label": "1",
   *        "active": true
   *      },
   *      { "url": null, "label": "Next &raquo;", "active": false }
   *    ],
   *    "next_page_url": null,
   *    "path": "http://localhost:7000/api/fusion/customer/search",
   *    "per_page": 15,
   *    "prev_page_url": null,
   *    "to": 1,
   *    "total": 1
   *  }
   *}
   * 
   * @return \Illuminate\Http\Response
   */
  public function index(IndexCustomerRequest $request, UserService $service)
  {
    $data = $request->validated();
    $this->resource = ['resource' => 'Customer'];
    $response = $service->getAllCustomers($data);
    return $this->response('index', $response);
  }

  public function getAllSorted(Request $request,CustomerService $service)
  {
    $response = $service->sortedCustomers($request->attribute, $request->sortBy );
    return response()->json($response);
  }

  public function search(SearchCustomerRequest $request, CustomerService $service)
  {
    $data = $request->all();
    $this->resource = ['resource' => 'Customer'];
    $response = $service->search($data);
    return response()->json($response);
  }

  /**
   * store 
   * 
   * Store a newly created resource in storage.
   * 
   * @group Customer
   * @authenticated
   *
   * @bodyParam name string required The customer name. Example: John Doe
   * @bodyParam email string required The customer email. Example:
   * @bodyParam document string required The customer document. Example: 12345678901
   * @bodyParam document_type string required The customer document type. Example: cpf or cnpj
   * @bodyParam gender string required The gender type. Example: male or female
   * @bodyParam birthdate string required The birthdate. Example: 1990-01-01
   * @bodyParam phones object required The phone object. Example: {"home_phone": {country_code": "55", "area_code": "11", "number": "999999999"}}
   * @bodyParam phones.home_phone.country_code string required The country code. Example: 55
   * @bodyParam phones.home_phone.area_code string required The area code. Example: 11
   * @bodyParam phones.home_phone.number string required The number. Example: 999999999
   * @bodyParam phones.mobile_phone.country_code string required The country code. Example: 55
   * @bodyParam phones.mobile_phone.area_code string required The area code. Example: 11
   * @bodyParam phones.mobile_phone.number string required The number. Example: 999999999
   * @bodyParam health_advice string required The health advice. Example: CRM, CRP, etc.
   * @bodyParam advice_code string required The advice code. Example: 884455
   * @bodyParam address object required The address object. Example: {"adress:{"line_1": "Rua dos Bobos", "line_2": "nº 0", "city": "São Paulo", "state": "SP", "country": "Brasil", "zip_code": "12345678"}}
   * @bodyParam address.line_1 string required The line 1. Example: Rua dos Bobos
   * @bodyParam address.line_2 string required The line 2. Example: nº 0
   * @bodyParam address.city string required The city. Example: São Paulo
   * @bodyParam address.state string required The state. Example: SP
   * @bodyParam address.country string required The country. Example: Brasil
   * @bodyParam address.zip_code string required The zip code. Example: 12345678
   * 
   * @response {
   * "status":true,
   * "message":"Customer created successfully",
   * "data":[]
   * }
   * 
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(StoreCustomerRequest $request, UserService $service)
  {
    DB::beginTransaction();
    try {
      $this->resource  = ['resource' => 'Customer'];
      $data            = $request->validated();
      $data['password'] = Hash::make($data['email']);
      $customer        = $service->customerStore($data);
      $customerService = new CustomerService();
      $response        = $customerService->search([]);
      DB::commit();
      return response()->json($response);
    } catch (\Throwable $th) {
      DB::rollback();
      return $this->response($th->getMessage(), false);
    }
  }

  
  public function register(RegisterCustomerRequest $request, CustomerService $service)
  {
    DB::beginTransaction();
    try {
      // $cardService = new CardService();
      // $subscriptionService = new SubscriptionService();

      $this->resource = ['resource' => 'Customer'];

      $data = $request->validated();
      $data['password'] = Hash::make($data['password']);
      $customer = $service->customerStore($data);
      if(!$customer instanceof User) return response()->json(["error" => "Falha ao criar usuário"]);

      // $card = $cardService->store($data['card'], $customer->uuid);

      // if(!$card instanceof Card) return response()->json(["error" => "Falha ao criar cartão"]);
      
      // $subscription = $subscriptionService->store($customer->uuid, $data['plan_uuid']);

      // if(!$subscription instanceof Subscription) return response()->json(["error" => "Falha ao criar assinatura"]);

      DB::commit();
      return response()->json(["status" => true, "message" => "Cadastro de especialista criado com sucesso", "data" => $customer]);
    } catch (\Throwable $th) {
      DB::rollBack();
      return response()->json(["error" => $th->getMessage()]);
    }
  }

  /**
   * customer.check
   * 
   * @group Fusion
   * @subgroup Customer
   * @authenticated
   * 
   * @bodyParam uuid string required The customer uuid. Example: 12345678901
   * 
   * @response {
   *  "status":true,
   *  "message":"Customer check successful",
   *  "data":[]
   * }
   *
   * @param Request $request
   * @param UserService $service
   * @return void
   */
  public function check(Request $request, UserService $service)
  {
    $this->resource = ['resource' => 'Customer'];
    $data = $request->validate(['uuid' => 'required|exists:users,uuid']);
    $response = $service->customerCheck($data['uuid']);
    return $this->response('check', $response);
  }

  /**
   * Display the specified resource.
   *
   * @param  \App\Models\User  $user
   * @return \Illuminate\Http\Response
   */
  public function show(User $user)
  {
      //
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  \App\Models\User  $user
   * @return \Illuminate\Http\Response
   */
  public function edit(User $user)
  {
      //
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \App\Models\User  $user
   * @return \Illuminate\Http\Response
   */
  public function completeUserData(CompleteUserDataRequest $request, CustomerService $service)
  {
    
    
    DB::beginTransaction();
    try {
      $data = $request->validated();
      $cardService = new CardService();

      $customer = $service->completeData($data['user_uuid'],$data);
      if(!$customer instanceof User) return response()->json(["error" => "Falha ao criar usuário"]);        

      $card = $cardService->store($data['card'], $customer->uuid);
      if(!$card instanceof Card){
        return response()->json(["error" => "Falha ao criar cartão"]);
      }
      
      DB::commit();
      return response()->json([
        "status" => true,
        "message" => "Dados cadastrados com sucesso",
        "data" => $customer->fresh()
      ]);

      
    } catch (\Throwable $th) {
      DB::rollBack();
      return response()->json(["error" => $th->getMessage()]);
    }
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  \App\Models\User  $user
   * @return \Illuminate\Http\Response
   */
  public function destroy(User $user)
  {
      //
  }

  /**
   * cancel.account
   * 
   * @group Customer
   * @authenticated
   * 
   * @bodyParam uuid string required The customer uuid. Example: 12345678901
   * 
   * @response {
   *  "status":true,
   *  "message":"Customer account canceled successfully",
   *  "data":[]
   * }
   *
   * @param Request $request
   * @param UserService $service
   * @return void
   */
  public function cancelAccount(Request $request, UserService $service)
  {
    $response = $service->cancelAccount($request->uuid);
    return $this->response('account_deactive', $response);
  }

  /**
   * cancel.active
   * 
   * @group Customer
   * @authenticated
   * 
   * @bodyParam uuid string required The customer uuid. Example: 12345678901
   * 
   * @response {
   *  "status":true,
   *  "message":"Customer account activated successfully",
   *  "data":[]
   * }
   *
   * @param Request $request
   * @param UserService $service
   * @return void
   */
  public function activeAccount(Request $request, UserService $service)
  {
    $response = $service->activeAccount($request->uuid);
    return $this->response('account_active', $response);
  }
}
