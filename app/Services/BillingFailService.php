<?php

namespace App\Services;

use App\Models\Billing;
use App\Models\BillingFail;
use App\Models\User;
use App\Notifications\DelinquentNotification;
use App\Repositories\BillingFailRepository;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Log;

class BillingFailService
{

  public function find(int $id)
  {
    return (new BillingFailRepository())->find($id);
  }
  
  public function store(int $userId, string $referenceDate, string $modelType)
  {
    $data = [
      'user_id'        => $userId,
      'reference_date' => $referenceDate,
      'reference_type' => config('settings.billing_type'),
      'status'         => 'failed',
      'attempts'       => 1,
      'model_type'     => $modelType,
    ];

    $billingFail = (new BillingFailRepository())->create($data);
    (new BillingFailAttemptsService())->store($billingFail->id, 'failed');
    return $billingFail;
  }

  public function update($id, $status)
  {
    $billingFail = (new BillingFailRepository())->find($id);

    $data = ($status == 'failed')?
      ['attempts' => $billingFail->attempts + 1]: 
      ['status' => $status];

    $billingFail->update($data);

    (new BillingFailAttemptsService())->store($billingFail->id, $status);
    return $billingFail->fresh();
  }

  public function getFailsByModelType(string $modelType)
  {
    return BillingFail::where('model_type', $modelType)
      ->where('status', 'failed')
      ->where('attempts', '<', 3)
      ->get();
  }

  private function markAsDelinquent(int $userId, string $status, $attempts = 0)
  {
    $user = User::find($userId);
    if($user->status == 'inadimplente') return true;
    if($attempts == 3 and  $status == 'failed') {
      $user->notify(new DelinquentNotification);
      return $user->update(['status' => 'inadimplente']);
    };
    return false;
  }
  
  // para novas tentativas de cobrança
  public function getBillingsFailByUserAndModelType(int $userId, string $modelType)
  {
    return BillingFail::where('user_id', $userId)
    ->where('model_type', $modelType)
    ->where('status', 'failed')
    ->where('attempts', '<', 3)->first();
  }

  public function updateToPaid(BillingFail &$billingFail)
  {
    $updated = $billingFail->update([
      'status' => 'paid',
      'attempts' => $billingFail->attempts + 1,
    ]);

    if($updated)
      (new BillingFailAttemptsService())->store($billingFail->id, 'paid');
    return $billingFail->fresh();
  }

  public function updateAttempt(BillingFail &$billingFail)
  {
    $updated = $billingFail->update([
      'attempts' => $billingFail->attempts + 1,
    ]);

    if($updated){
      (new BillingFailAttemptsService())->store($billingFail->id, 'failed');
    }
    $bilingFail = $billingFail->fresh();
    $this->markAsDelinquent($billingFail->user_id, 'failed', $bilingFail->attempts);
    return $billingFail->fresh();
  }

  public function handler(string $status, User $user, string $modelType)
  {
    $billingFail = $this->getBillingsFailByUserAndModelType($user->id, $modelType);

    if(!$billingFail and $status == 'paid') return true;

    switch (true) {
      case ($status == 'paid' and $billingFail):
        return $this->updateToPaid($billingFail);
        break;
      
      case ($status == 'failed' and $billingFail):
        return $this->updateAttempt($billingFail);
        break;

      default:
        $referenceDate = $this->getReferenceDateByBillingConfig();
        return $this->store($user->id, $referenceDate, $modelType);
        break;
    }

  }

  private function getReferenceDateByBillingConfig()
  {
    switch (config('settings.billing_type')) {
      case 'monthly':
        return Date::now()->day(20)->subMonth()->format('m');
        break;
      case 'daily':
        return Date::now()->subDay();
        break;
    }
  }

}