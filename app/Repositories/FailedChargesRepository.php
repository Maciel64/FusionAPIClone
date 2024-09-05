<?php

namespace App\Repositories;

use App\Models\FailedCharges;

class FailedChargesRepository extends BaseRepository
{
 
  public function __construct()
  {
    parent::__construct(FailedCharges::class);
  }

  public function findByUserIdAndYearAndMonthReference(int $userId, int $monthReference, int $yearReference,)
  {
    return $this->model->where('user_id', $userId)
    ->where('month_reference', $monthReference)
    ->whereYear('failed_at', $yearReference)
    ->first();
  }

  public function getAllUsersWithFailedChargesByBillingConfig()
  {
    $lastMonth = now()->subMonth()->month;
    $lastYear = now()->year;

    return $this->model->where('month_reference', $lastMonth)
    ->whereYear('failed_at', $lastYear)
    ->where('attempts', '<',3)
    ->get();
  }

  public function getConditionByBillingConfig()
  {
    
  }
}