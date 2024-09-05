<?php

namespace App\Repositories;

use App\Models\PlanHasUser;

class PlansHasUserRepository extends BaseRepository
{
  public function __construct()
  {
    parent::__construct(PlanHasUser::class);
  }

  public function findByUserId(int $userId)
  {
    return $this->model
      ->where('active', true)
      ->where('user_id', $userId)
      ->first();
  }

  public function getByUserId(int $userId)
  {
    return $this->model
      ->where('active', true)
      ->where('user_id', $userId)
      ->get();
  }
}