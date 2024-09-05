<?php


namespace App\Repositories;

use App\Models\Subscription;

class SubscriptionRepository extends BaseRepository
{
  public function __construct()
  {
    parent::__construct(Subscription::class);
  }
}