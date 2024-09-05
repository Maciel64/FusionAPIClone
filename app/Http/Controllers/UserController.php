<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAddressRequest;
use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\StorePhotoRequest;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Requests\UpdateBasicDataPartnerRequest;
use App\Http\Requests\UpdateBasicDataCustomerRequest;
use App\Http\Requests\DeactiveOrActiveUserRequest;
use Validator;
use Illuminate\Validation\Rule;
use App\Models\User;
use App\Repositories\AddressRepository;
use App\Repositories\HealthAdviceRepository;
use App\Repositories\HealthAdviceHasUserRepository;
use App\Repositories\PhotoRepository;
use App\Repositories\UserRepository;
use App\Services\PhotoService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
  
  public function __construct()
  {
    $this->resource = ['resource' => 'User'];
  }

  public function deactiveOrActiveUser(DeactiveOrActiveUserRequest $request, UserService $service){
    $data = $request->validated();
    $response = $service->setActiveStatus($data['user_uuid'], $data['setStatus']);
    return $response;
  }

  /**
   * index
   * 
   * List all users (obs. Permission only for owner)
   * 
   * @group Fusion
   * @subgroup User
   * 
   * @authenticated 
   * 
   * @queryParam role string required Role of the user. Example: admin or partner
   * 
   * @response {
   *   "status": true,
   *   "message": "Users list retrieved successfully",
   *   "data": [
   *     {
   *       "uuid": "2b1726fb-f272-418a-9944-ac1f1fe69999",
   *       "name": "Kaden Gerlach",
   *       "email": "keeley.bashirian@example.com",
   *       "last_access": null,
   *       "role_name": "admin",
   *       "photo": {
   *         "uuid": "02bd6327-cbd5-4c52-96d4-7defbfb0d8cb",
   *         "name": "Angeline Swift",
   *         "url": "https://via.placeholder.com/640x480.png/005511?text=iusto"
   *       },
   *       "address": {
   *         "uuid": "36388402-00de-4aa3-8c32-0df7c0696dfe",
   *         "street": "Preston Mill",
   *         "number": "2856",
   *         "complement": "Suite 851",
   *         "neighborhood": "berg",
   *         "city": "Hahnside",
   *         "state": "NY",
   *         "zip_code": "71640-7298"
   *       },
   *       "contact": {
   *         "uuid": "7b74b948-483c-4572-a313-0c06a575bd2d",
   *         "email": "terry.leann@hotmail.com",
   *         "phone": "503-614-6613"
   *       }
   *     }
   *   ]
   * }
   * 
   * @return \Illuminate\Http\Response
   */
  public function index(Request $request, UserRepository $repository)
  {
    $response = $repository->getAllUsersByRole($request->role, $request->paginate);
    return response()->json($response);
  }

  public function indexAll(Request $request, UserRepository $repository)
  {
    $response = $repository->getAllUsers($request->role);
    return response()->json($response);
  }

  public function specialistData(Request $request, UserService $service)
  {
    $response = $service->specialistInformations($request->dateInit, $request->dateEnd);
    return $response;
  }

  /**
   * 
   * store
   * 
   * Create a new Partner or Admin
   * 
   * @group Fusion
   * @subgroup User
   * @authenticated 
   * 
   * @bodyParam name string required The name of the user. Example: John Doe
   * @bodyParam email string required Email of the user. Example: john.doe@fusion.com
   * @bodyParam user_type string required Type of the user. Example: partner, customer or admin
   * @response {
   *   "status": true,
   *   "message": "User created successfully",
   *   "data": {
   *     "uuid": "a483d702-4005-4135-901b-55c5ce4a0f36",
   *     "name": "New admin",
   *     "document_type": null,
   *     "document": null,
   *     "gender": null,
   *     "birth_date": null,
   *     "email": "lf.system@outlook.com",
   *     "last_access": null,
   *     "role_name": "partner",
   *     "photo": null,
   *     "address": null,
   *     "schedule": {
   *       "uuid": "506630f2-2a23-4609-9609-dc7f7efbb19f",
   *       "name": "Schedule"
   *     },
   *     "advice": null,
   *     "contacts": []
   *   }
   * }
   * @param  StoreUserRequest  $request
   * @param  UserRepository  $repository
   * @return Response
   */
  public function store(StoreUserRequest $request,  UserService $service)
  {
    $data = $request->validated();
    $response = $service->store($data);
    return $this->response('store', $response);
  }

  /**
   * public function storeCustomer(StoreCustomerRequest $request, UserService $service)
   * {
   *   $this->resource = ['resource' => 'Customer'];
   *   $data = $request->validated();
   *   $response = $service->storeCustomer($data);
   *   return $this->response('store', $response);
   * }
   */

  /**
   * check.customer
   * 
   * verify information of the customer
   * 
   * @group Fusion
   * @subgroup User
   * @authenticated
   * 
   * @queryParam uuid required Customer UUID. Example: 6130f8f3-74cf-48c5-91d9-8db6a4471641
   *
   * @response {
   *   "status": true,
   *   "message": "Customer check successful",
   *   "data": []
   * }
   * 
   */
  public function checkCustomer(Request $request, UserService $service)
  {
    $this->resource = ['resource' => 'Customer'];
    $data = $request->validate(['uuid' => 'required|exists:users,uuid']);
    $response = $service->customerCheck($data['uuid']);
    return $this->response('check', $response);
  }

  /**
   * show
   * 
   * Display the specified resource.
   * 
   * 
   * @group Fusion
   * @subgroup User
   * @authenticated
   * 
   * @queryParam uuid required User UUID. Example: 6130f8f3-74cf-48c5-91d9-8db6a4471641
   * 
   * @response {
   *   "status": true,
   *   "message": "User retrieved successfully",
   *   "data": {
   *     "uuid": "3a10806b-abc0-4e41-b058-833ebf10fba3",
   *     "name": "Francisca Swaniawski IV",
   *     "document_type": "cpf",
   *     "document": "",
   *     "gender": null,
   *     "birth_date": "2000-01-07",
   *     "email": "mateo53@example.com",
   *     "last_access": null,
   *     "role_name": null,
   *     "photo": null,
   *     "address": null,
   *     "schedule": null,
   *     "advice": null,
   *     "contacts": []
   *   }
   * }
   * 
   * @param  string  $uuid User UUID
   * 
   * @param  UserRepository $repository
   * @return Response
   */
  public function show(string $uuid, UserRepository $repository)
  {
    $response = $repository->findByUuid($uuid);
    return $this->response('show', $response);
  }

  /**
   * update
   * 
   * Update the specified resource in storage.
   *
   * @group User
   * @authenticated
   * 
   * @queryParam uuid required User UUID. Example: f7387b46-e4e2-49bc-b23a-4fe7eba8137a
   * 
   * @bodyParam name string User name. Example: John Doe
   * @bodyParam email string User email. Example: john.doe@fusion.com
   * @bodyParam password string Password. Example: 123456
   * @bodyParam phone string Phone number. Example: 11999999999
   * @bodyParam birth_date date Birth date. Example: 1990-01-01
   * 
   * @response {
   *   "status": true,
   *   "message": "User updated successfully",
   *   "data": {
   *     "uuid": "0841d4bd-bbd4-45d8-b0fa-b9fd707df23f",
   *     "name": "John Doe Updated",
   *     "document_type": "cpf",
   *     "document": "",
   *     "gender": null,
   *     "birth_date": "1998-05-22",
   *     "email": "alanna.champlin@example.org",
   *     "last_access": null,
   *     "role_name": null,
   *     "photo": null,
   *     "address": null,
   *     "schedule": null,
   *     "advice": null,
   *     "contacts": []
   *   }
   * }
   * 
   * @param  UpdateUserRequest  $request
   * @param  UserRepository $repository
   * @return Response
   */
  public function update(UpdateUserRequest $request, UserRepository $repository)
  {
    
    $response = $repository->updateByUuid($request->uuid, $request->validated());
    return $this->response('update', $response);
  }

  public function updateBasicDataPartner(UpdateBasicDataPartnerRequest $request, UserRepository $repository, $user_uuid)
  {
    $response = $repository->updateByUuid($user_uuid, $request->validated());
    return $this->response('update', $response);
  }

  public function updateBasicDataCustomer(UpdateBasicDataCustomerRequest $request, UserService $service, $user_uuid)
  {
    $response = $service->updateBasicDataCustomer($user_uuid, $request->validated());
    return $this->response('update', $response);
  }

  public function updateBasicDataByAdmin(Request $request, UserRepository $repository, $user_uuid)
  {
    if(auth()->user()->role_name != 'admin' && auth()->user()->role_name != 'owner'){
      return response()->json([
        'status' => false,
        'message' => 'Você não tem autorização de acesso'
      ]);
    }
    //request no controler para checar o e-mail do usuário
    $user = $repository->findByUuid($user_uuid); 
    $rules = [
      'name'     => 'sometimes|string|max:255',
      'email' => 'sometimes|string|email|max:255',
      'email' => Rule::unique('users')->ignore($user->uuid, 'uuid'),
      'document' => 'sometimes|string|max:14',
      'health_advice' => 'sometimes|string|max:255|exists:health_advice,initials',
      'advice_code'   => 'sometimes|string|max:255',
    ];

    $validator = Validator::make($request->all(), $rules);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }    
    $response = $repository->updateByUuid($user_uuid, $request->all());
    $healthAdviceRepository = new HealthAdviceHasUserRepository();
    $healthAdviceRepository->updateByUuid($user->healthAdvice->uuid, $request->all());    
    
    return $this->response('update', $response);
  }

  /**
   * destroy
   * 
   * Remove the specified resource from storage.
   *
   * @group Fusion
   * @subgroup User
   * @authenticated
   * 
   * @urlParam uuid required User UUID. Example: 6130f8f3-74cf-48c5-91d9-8db6a4471641
   * 
   * @response {
   *   "status": true,
   *   "message": "User deleted successfully",
   *   "data": []
   * }
   * @param  UserRepository $repository
   * @return Response
   */
  public function destroy(string $uuid, UserRepository $repository)
  {
    $response = $repository->deleteByUuid($uuid);
    return $this->response('destroy', $response);
  }

  /**
   * 
   * partner.verify.email
   * 
   * verify email of the partner
   * 
   * @group Partner
   * @subgroup User
   * @authenticated
   * 
   * @header X-Internal-Token
   * 
   * @bodyParam code required Code to verify email. Example: 123456
   * @bodyParam email required Email to verify. Example:
   * 
   * @response {
   *   "status": true,
   *   "message": "response.verifyEmail.success",
   *   "data": {
   *     "verified": true
   *   }
   * }
   * 
   * @param Request $request
   * @param UserService $service
   * @return void
   */
  public function verifyEmail(Request $request, UserService $service)
  {
    $data = $request->validate([
      'code' => 'required|string',
      'email' => 'required|email|exists:users,email'
    ]);

    $response = $service->verifyEmail($data['email'], $data['code']);
    return $this->response('verifyEmail', $response);
  }

  public function resendVerificationCode(Request $request, UserService $service)
  {
    $data = $request->validate([
      'email' => 'required|email|exists:users,email'
    ]);

    $repository = new UserRepository();
    $user       = $repository->findByEmail($data['email']);
    $response   = $service->createAndSendNotificationToVerifyCode($user);

    if($response instanceof User){
      return response()->json([
        'status' => true,
        'message' => 'Código reenviado com sucesso',
        'data' => []
      ]);
    }
    return response()->json([
      'status' => false,
      'message' => 'Não foi possível reenviar o código',
      'data' => []
    ]);
  }

  public function forceVerification(Request $request, UserService $service)
  {
    $response = $service->forceVerification($request->email);
    return response()->json($response);
  }

  // public function listAllPartners(Request $request, UserService $service)
  // {
  //   $response = $service->listAllPartners();
  //   return response()->json($response);
  // }

}
