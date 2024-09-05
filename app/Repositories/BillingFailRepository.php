<?php

namespace App\Repositories;

use App\Models\BillingFail;
use Illuminate\Support\Facades\Date;

class BillingFailRepository extends BaseRepository
{
 
 public function __construct()
 {
   parent::__construct(BillingFail::class);
 }

  public function getFails()
  {
    return $this->model
      ->where('status', 'failed')
      ->where('attempts', '<', 3)
      ->get();
  }

  public function getFailsByUserAndDate($userId, $date)
  {
    $dateInit = Date::parse($date)->startOfDay();
    $dateEnd  = Date::parse($date)->endOfDay();
    return $this->model
      ->where('user_id', $userId)
      ->where('status', 'failed')
      ->where('attempts', '<', 3)
      ->whereBetween('reference_date', [$dateInit, $dateEnd])
      ->get();
  }

  public function getFailByUserAndModelType(int $userId, string $modelType)
  {
    return $this->model
      ->where('user_id', $userId)
      ->where('model_type', $modelType)
      ->where('status', 'failed')
      ->where('attempts', '<', 3)
      ->first();
  }
  
}