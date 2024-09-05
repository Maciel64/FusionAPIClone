<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\ScheduleRepository;

class ScheduleService
{
  public function store(User $user)
  {
    $repository = new ScheduleRepository();
    if($user->hasRole('partner'))
      return $repository->create(['user_id' => $user->id]);
  }
}