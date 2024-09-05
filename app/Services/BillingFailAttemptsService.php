<?php

namespace App\Services;

use App\Repositories\BillingFailAttemptsRepository;

class BillingFailAttemptsService
{
  public function store($billingFailedId, $status)
  {
    $data = [
      'billing_fail_id' => $billingFailedId,
      'status'          => $status,
    ];

    return (new BillingFailAttemptsRepository())->create($data);
  }
}