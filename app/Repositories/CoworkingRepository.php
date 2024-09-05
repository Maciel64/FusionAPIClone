<?php

namespace App\Repositories;

use App\Models\Coworking;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;

class CoworkingRepository extends BaseRepository
{
  
  public function __construct()
  {
    parent::__construct(Coworking::class);
  }

  public function getByUser()
  {
    return $this->model->where('user_id', auth()->user()->id)->get() ?? false;
  }

  public function getByUserUuid($uuid)  
  {
    $userRepository = new UserRepository();
    $user           = $userRepository->findByUuid($uuid); 
    return $this->model->where('user_id', $user->id)->get() ?? false;
  }

  public function getAllCoworkingIdByPartner(int $partnerId)
  {
    return $this->model->where('user_id', $partnerId)->pluck('id');
  }

  public function getAllCoworking()
  {
    return $this->model->get();
  }

  public function getCoworkingCountByDate($dateInit, $dateEnd)
  {
    $count = $this->model
        ->whereBetween('created_at', ['2001-01-01 00:00:00', $dateEnd.' 23:59:59'])
        ->count();

    return $count;
  }

}