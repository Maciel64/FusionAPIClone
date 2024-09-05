<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFacilityRequest;
use App\Http\Requests\UpdateFacilityRequest;
use Illuminate\Http\Request;
use App\Repositories\FacilityRepository;
use App\Models\Facility;
use Validator;

class FacilityController extends Controller
{
    /**
     * indexxxxxxx
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, FacilityRepository $repository)
    {
      $response = $repository->getAllFacilities();
      return response()->json($response);
    }

    public function indexNoPaginate(Request $request, FacilityRepository $repository){
      $response = $repository->getAllFacilitiesNoPaginate();
      return response()->json($response);
    }

    

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreFacilityRequest $request, FacilityRepository $repository)
    {
        $data = $request->validated();
        $response = $repository->create($data);
        return $this->response('store', $response);
      }

    /**
     * Display the specified resource.
     *
     * @param  int  $uuid
     * @return \Illuminate\Http\Response
     */ 
    public function show(Request $request, FacilityRepository $repository)
    {
        $response = $repository->findByUuid($request->uuid);
        return $this->response('show', $response);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $uuid
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateFacilityRequest $request,FacilityRepository $repository)
    {
        $data = $request->validated();
        $response = $repository->updateByUuid($request->uuid, $data);
        return $this->response('update', $response);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $uuid
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, FacilityRepository $repository)
    {
      $response = $repository->deleteByUuid($request->uuid);
      return $this->response('destroy', $response);
    }
}