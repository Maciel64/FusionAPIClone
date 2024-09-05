<?php

namespace App\Repositories;

use App\Models\Schedule;
use App\Repositories\BaseRepository;

class ScheduleRepository extends BaseRepository
{
  public function __construct()
  {
    parent::__construct(Schedule::class);
  }

}