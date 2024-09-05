<?php

namespace App\Repositories;

use App\Models\SubscriptionUpdateRequest;

class SubscriptionUpdateRequestRepository extends BaseRepository
{
  public function __construct()
  {
      parent::__construct(SubscriptionUpdateRequest::class);
  }

  public function findBySubscriptionId(int $subscriptionId)
  {
    $model = $this->model;
    return $model
      ->where('subscription_id', $subscriptionId)
      ->where('status', 'pending')
      ->first();
  }
}