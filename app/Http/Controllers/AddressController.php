<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAddressRequest;
use App\Http\Requests\UpdateAddressRequest;
use App\Models\Address;
use App\Repositories\AddressRepository;
use App\Services\AddressService;
use Illuminate\Http\Request;

class AddressController extends Controller
{

    public function __construct()
    {
      $this->resource = ['resource' => 'Address'];
    }

    /**
     * store
     * 
     * Create a new address for user or coworking
     * 
     * @group Address
     * @authenticated
     * 
     * @bodyParam uuid required Uuid of the user or Coworking. Example: 0b826f29-0c0f-4c4b-ae0f-5c54d4d4c214
     * @bodyParam type required The type of resources. Example: coworking,room,user
     * @bodyParam line_1 string required Example: Rua 1
     * @bodyParam line_2 string required Example: 123
     * @bodyParam country string Example: BR
     * @bodyParam city string required Example: Araras
     * @bodyParam state string required Example: SP
     * @bodyParam zip_code string required Example: 12345
     * @response {
     *  "status": true,
     *  "message": "Address created successfully",
     *  "data": {
     *    "uuid": "bab03192-9d78-44db-a46f-3b540015c89a",
     *    "line_1": "Rua 1",
     *    "line_2": "Casa 1",
     *    "city": "Cidade 1",
     *    "state": "SP",
     *    "country": "BR",
     *    "zip_code": "12345678"
     *  }
     *}
     *
     * @param StoreAddressRequest $request
     * @param AddressRepository $repository
     * @return void
     */
    public function store(StoreAddressRequest $request, AddressRepository $repository)
    {
      $data = $request->validated();
      $response = $repository->store($data);
      return $this->response('store', $response);
    }

    /**
     * 
     * update
     * 
     * Update the specified address.
     * 
     * @group Address
     * @authenticated
     * 
     * @group Address
     * @urlParam address required The UUID of the address. Example: 323135f7-1a47-4690-8a64-063fc98add02
     *
     * @bodyParam type required The type of resources. Example: coworking,room,user
     * @bodyParam line_1 string sometimes Example: Rua 1
     * @bodyParam line_2 string sometimes Example: 123
     * @bodyParam country string sometimes Example: BR
     * @bodyParam city string sometimes Example: Araras
     * @bodyParam state string sometimes Example: SP
     * @bodyParam zip_code string sometimes Example: 12345
     * 
     * @response {
     *  "status": true,
     *  "message": "Address updated successfully",
     *  "data": {
     *    "uuid": "df436dc8-6e48-45eb-a42c-586566b5a3cb",
     *    "line_1": "Rua 1",
     *    "line_2": "Casa 1",
     *    "city": "Cidade 1",
     *    "state": "SP",
     *    "country": "Bairro 1",
     *    "zip_code": "12345678"
     *  }
     *}
     * 
     * @param  \App\Http\Requests\UpdateAddressRequest  $request
     * @param  \App\Models\Address  $address
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateAddressRequest $request, AddressService $service, $address)
    {
      $data = $request->validated();
      $response = $service->updateAddress($data, $address);
      
      return $this->response('update', $response);
    }

    /**
     * destroy
     * 
     * Remove the specified address.
     *
     * @group Address
     * @authenticated
     * 
     * @urlParam address required The UUID of the address. Example: 323135f7-1a47-4690-8a64-063fc98add02
     * 
     * @response {
     * "status":true,
     * "message":"Address deleted successfully",
     * "data":[]
     * }
     * 
     * @param  \App\Models\Address  $address
     * @return \Illuminate\Http\Response
     */
    public function destroy(string $uuid, AddressRepository $repository)
    {
      $response = $repository->deleteByUuid($uuid);
      return $this->response('destroy', $response);
    }

    /**
     * Search address by zip code
     *
     * @group Address
     * @authenticated
     * 
     * @urlParam zip_code required The Zip Code of the address. Example: 13605-338
     * 
     * @response {
     *  "status": true,
     *  "message": "Address retrieved successfully",
     *  "data": {
     *      "line_1": "Rua Vicente Baptista - Jardim Apolo - Luiz Bertoline",
     *      "line_2": "",
     *      "city": "Araras",
     *      "state": "SP",
     *      "country": "Brasil",
     *      "zip_code": "13605-338"
     *  }
     * }
     * 
     * @param  \App\Models\Address  $address
     * @return \Illuminate\Http\Response
     */
    public function  findByZipCode(Request $request, AddressService $service)
    {
      $response = $service->findAddressByZipCode($request->zip_code);
      return $this->response('show', $response);
    }
}
