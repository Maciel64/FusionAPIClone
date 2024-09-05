<?php


namespace App\Repositories;

use App\Models\Billing;

class BillingRepository extends BaseRepository
{
  public function __construct()
  {
    parent::__construct(Billing::class);
  }

  public function findByUserId($userId)
  {
    return $this->model->where('user_id', $userId)->first();
  }

  public function getByCustomerId(int $customerId)
  {
    return $this->model->where('user_id', $customerId)->paginate(config('settings.paginate'));
  }

  public function getByUserIdAndNoPaid($userId)
  {
    return $this->model->where('user_id', $userId)->where('paid', false)->get();
  }

  public function getBillingsValue($dateInit, $dateEnd)
  {
    return $this->model->whereBetween('payment_at', [$dateInit.' 00:00:00', $dateEnd.' 23:59:59'])
    ->sum('amount');
  }

  public function getBillingValuePerDay($dateInit, $dateEnd)
  {
      $results = $this->model
          ->whereBetween('payment_at', [$dateInit.' 00:00:00', $dateEnd.' 23:59:59'])
          ->selectRaw('DATE(payment_at) as date, sum(amount) as total_amount')
          ->groupBy('date')
          ->get();
      
      return $results;
  }

  public function getBillingCountByDate($dateInit, $dateEnd)
  {
    return $this->model->whereBetween('payment_at', [$dateInit.' 00:00:00', $dateEnd.' 23:59:59'])
    ->count();
  }

}