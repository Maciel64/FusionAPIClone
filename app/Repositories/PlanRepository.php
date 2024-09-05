<?php

namespace App\Repositories;

use App\Models\Plan;

class PlanRepository extends BaseRepository
{

  public function __construct()
  {
    parent::__construct(Plan::class);
  }
}