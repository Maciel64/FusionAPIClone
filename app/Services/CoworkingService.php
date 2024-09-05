<?php

namespace App\Services;

use App\Models\Coworking;
use App\Repositories\AddressRepository;
use App\Repositories\CoworkingRepository;
use App\Repositories\UserRepository;
use App\Repositories\RoomRepository;
use Illuminate\Database\Eloquent\Model;
use App\Http\Controllers\CoworkingController;

class CoworkingService 
{

  private Room $model;


  // public function __construct()
  // {
  //   $this->model = new Cowoking();

  // }

  public function getCurrentCoworkingCountByDate($dateInit, $dateEnd){
    $repository = new CoworkingRepository();
    $createdCoworkings = $repository->getCoworkingCountByDate($dateInit, $dateEnd);
    $response = $createdCoworkings;
    return $response;
  }

  

}