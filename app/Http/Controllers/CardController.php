<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCardRequest;
use App\Http\Requests\UpdateCardRequest;
use App\Models\Card;
use App\Repositories\CardRepository;
use App\Services\CardService;
use Illuminate\Http\Request;

class CardController extends Controller
{
  public function __construct()
  {
    $this->resource = ['resource' => 'Card'];
  }

  public function setDefault($card_uuid, $user_uuid, CardService $service){
    $response = $service->defaultConfiguration($card_uuid, $user_uuid);
    return $response;
  }

  /**
   * store 
   * 
   * This resource is responsible for create a new card
   *
   * @group Customer
   * @subgroup Card
   * @authenticated 
   * 
   * @queryParam uuid required The customer UUID. Example: 31b7cce0-ca3f-48c6-93db-3cb6cb2fda72
   * 
   * @bodyParam number string required The card number. 
   * @bodyParam holder_name string required The card owner name. Example: John Doe
   * @bodyParam holder_document string required The document owner. Example: 12345678901
   * @bodyParam exp_month int required The expiration month of card. Example: 12
   * @bodyParam exp_year int required The expiration year of card. Example: 2028
   * @bodyParam cvv string required The cvv card. Example: 123
   * @bodyParam brand string required The brand card. Example: visa
   * @bodyParam billing_address_is_different bool required This field must be true when using an address different from the one the user registered in his account, if it is different then it will be mandatory to fill in the other fields below. Example: true
   * @bodyParam billing_address object The billing address. Example: {line_1: "test", line_2: "test", city: "test", state: "SP", country: "BR", zip_code: "12345678"}
   * @bodyParam billing_address.line_1 string The billing address line 1. Example: test
   * @bodyParam billing_address.line_2 string The billing address line 2. Example: test
   * @bodyParam billing_address.city string The billing address city. Example: test
   * @bodyParam billing_address.state string The billing address state. Example: SP
   * @bodyParam billing_address.country string The billing address country. Example: BR
   * @bodyParam billing_address.zip_code string The billing address zip code. Example: 12345678
   * 
   * @response {
   *   "status": true,
   *   "message": "Card created successfully",
   *   "data": {
   *     "uuid": "017311f1-08d5-454c-b78b-4d79daed9447",
   *     "customer_id": "cus_agLAQDLHAH7QRrwj",
   *     "address_id": "addr_4XVDNnYT2Ty5vpZA",
   *     "card_id": "card_GzWmq91T0ToqedJ6",
   *     "first_six_digits": "542501",
   *     "last_four_digits": "7793",
   *     "brand": "visa",
   *     "holder_name": "Luiz Felipe",
   *     "holder_document": "93095135270",
   *     "exp_month": "1",
   *     "exp_year": "2030",
   *     "status": "active",
   *     "type": null,
   *     "label": "Sua bandeira",
   *     "card_token": "token_xYvP6Y7uluwL0JEl",
   *     "user_uuid": "5b117beb-190d-43e4-b75c-4be54bac1f66"
   *   }
   * }
   * 
   * @param StoreCardRequest $request
   * @param CardService $service
   * @return void
   */
  public function store(StoreCardRequest $request, CardService $service)
  {
    $data = $request->validated();
    $response = $service->store($data, $request->uuid);
    return $this->response('store', $response);
  }

  public function update(UpdateCardRequest $request, CardService $service)
  {
    $data = $request->validated();
    $response = $service->update($request->card_uuid, $data);
    return $this->response('update', $response);
  }

  /**
   * show
   * 
   * This resource is responsible for show a card
   * 
   * @group Customer
   * @subgroup Card
   * @authenticated
   * 
   * @queryParam uuid required The customer UUID. Example: 31b7cce0-ca3f-48c6-93db-3cb6cb2fda72
   * @queryParam card_uuid required The card UUID. Example: 31b7cce0-ca3f-48c6-93db-3cb6cb2fda72
   * 
   * @response {
   *   "status": true,
   *   "message": "Card retrieved successfully",
   *   "data": {
   *     "uuid": "8d19a208-d4ae-4e39-b44c-d97a47d7d8ff",
   *     "address_id": "509e6681-3aaf-32fc-8fa0-a8528765e549",
   *     "first_six_digits": "883240",
   *     "last_four_digits": "1530",
   *     "brand": "elo",
   *     "holder_name": "Verlie Rippin",
   *     "holder_document": "000.000.000-00",
   *     "exp_month": "09",
   *     "exp_year": "1975",
   *     "status": "active",
   *     "type": null,
   *     "label": "visa",
   *     "user_uuid": "5549929c-5a13-47d2-b8dd-0f7fc619c6ee"
   *   }
   * }
   *
   * @param Request $request
   * @param CardRepository $repository
   * @return void
   */
  public function show(Request $request, CardRepository $repository)
  {
    $response = $repository->findByUuid($request->card_uuid);
    return $this->response('show', $response);
  }

  public function index(Request $request, CardRepository $repository){
    $response = $repository->getAllCardsByCustomer($request->user_uuid);
    return response()->json($response);
  }

  /**
   * destroy
   * 
   * This resource is responsible for delete a card
   * 
   * @group Customer
   * @subgroup Card
   * @authenticated
   * 
   * @queryParam card_uuid required The card UUID. Example: 31b7cce0-ca3f-48c6-93db-3cb6cb2fda72
   * 
   * @response {
   *  "status": true,
   *  "message": "Card deleted successfully",
   *  "data": []
   * }
   *
   * @param Request $request
   * @param CardService $service
   * @return void
   */
  public function destroy(Request $request, CardService $service)
  {
    $response = $service->destroy($request->card_uuid);
    return $this->response('destroy', $response);
  }
}
