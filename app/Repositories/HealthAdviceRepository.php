<?php

namespace App\Repositories;

use App\Models\HealthAdvice;

class HealthAdviceRepository extends BaseRepository
{
  public function __construct()
  {
    parent::__construct(HealthAdvice::class);
  }

}