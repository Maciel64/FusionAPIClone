<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContactRequest;
use App\Http\Requests\UpdateContactRequest;
use App\Models\Contact;
use App\Repositories\ContactRepository;
use App\Services\ContactService;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function __construct()
    {
      $this->resource = ['resource' => 'Contact'];
    }

    /**
     * store
     *
     * Store a newly created resource in storage.
     * 
     * @group Contact
     * @authenticated
     * 
     * @bodyParam resource_type string required The resource type. Example: coworking, user, etc.
     * @bodyParam resource_uuid string required The resource uuid. Example: 1a2b3c4d-5e6f-7g8h-9i0j-1k2l3m4n5o6p
     * @bodyParam type string required The contact type. Example: mobile_phone, home_phone, etc.
     * @bodyParam country_code string required The country code. Example: 55
     * @bodyParam area_code string required The area code. Example: 11
     * @bodyParam number string required The number. Example: 999999999
     * 
     * @response {
     *  "status":true,
     *  "message":"Contact created successfully",
     *  "data":{
     *    "type":"mobile_phone",
     *    "country_code":"55",
     *    "area_code":"11",
     *    "number":"999999999",
     *    "uuid":"c6a080a3-8adb-46fa-9d76-c8c5ea63f8cf"
     *  }
     * }
     *
     * @param  \App\Http\Requests\StoreContactRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreContactRequest $request, ContactService $service)
    {
      $data = $request->validated();
      $response = $service->store($data);
      return $this->response('store', $response);
    }

    /**
     * show
     * 
     * Display the specified resource.
     *
     * @group Contact
     * @authenticated
     * 
     * @urlParam uuid string required The Contact uuid. Example: 1a2b3c4d-5e6f-7g8h-9i0j-1k2l3m4n5o6p
     * 
     * @response {
     *    "status":true,
     *    "message":"Contact retrieved successfully",
     *    "data":{
     *      "uuid":"2f45b1bd-1640-4204-b984-9343ee634fdb",
     *      "type":"home_phone",
     *      "country_code":"34",
     *      "area_code":"88",
     *      "number":"53541894"
     *    }
     * }
     * @param  \App\Models\Contact  $contact
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, ContactRepository $repository)
    {
      $response = $repository->findByUuid($request->uuid);
      return $this->response('show', $response);
    }

    /**
     * update
     * 
     * Update the specified resource in storage.
     *
     * @group Contact
     * @authenticated
     * 
     * @urlParam uuid string required The Contact uuid. Example: 1a2b3c4d-5e6f-7g8h-9i0j-1k2l3m4n5o6p
     * 
     * @bodyParam type string required The contact type. Example: mobile_phone, home_phone, etc.
     * @bodyParam country_code string required The country code. Example: 55
     * @bodyParam area_code string required The area code. Example: 11
     * @bodyParam number string required The number. Example: 999999999
     * 
     * @response {
     *  "status":true,
     *  "message":"Contact updated successfully",
     *  "data":{
     *    "uuid":"5d380d5c-041d-43eb-81b2-eb04b1154ea6",
     *    "type":"mobile_phone",
     *    "country_code":"55",
     *    "area_code":"19",
     *    "number":"999583179"
     *  }
     * }
     * @param  \App\Http\Requests\UpdateContactRequest  $request
     * @param  \App\Models\Contact  $contact
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateContactRequest $request, ContactService $service, $contact )
    {
      $data = $request->validated();
      $response = $service->updateContact($data, $contact);      
      return $this->response('update', $response);
    }

    /**
     * destroy
     * 
     * Remove the specified resource from storage.
     * 
     * @group Contact
     * @authenticated
     * 
     * @queryParam uuid string required The Contact uuid. Example: 1a2b3c4d-5e6f-7g8h-9i0j-1k2l3m4n5o6p
     * 
     * @response {
     * "status":true,
     * "message":"Contact deleted successfully",
     * "data":[]
     * }
     *
     * @param  \App\Models\Contact  $contact
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, ContactRepository $repository)
    {
      $response = $repository->deleteByUuid($request->uuid);
      return $this->response('destroy', $response);
    }
}
