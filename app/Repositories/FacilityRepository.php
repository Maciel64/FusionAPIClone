<?php

namespace App\Repositories;

use App\Models\Facility;

class FacilityRepository extends BaseRepository
{

  public function __construct()
  {
    parent::__construct(Facility::class);
  }

  public function getAllFacilities(){
    return $this->model
      ->orderBy('name', 'asc')
      ->paginate() ?? false;
  }

  public function getAllFacilitiesNoPaginate(){
    return $this->model
    ->orderBy('name', 'asc')
    ->get() ?? false;
  }

  
}