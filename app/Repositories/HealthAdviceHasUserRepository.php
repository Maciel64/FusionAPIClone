<?php

namespace App\Repositories;

use App\Models\HealthAdviceHasUser;

class HealthAdviceHasUserRepository extends BaseRepository
{
  public function __construct()
  {
    parent::__construct(HealthAdviceHasUser::class);
  }

}