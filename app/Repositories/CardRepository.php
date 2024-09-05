<?php

namespace App\Repositories;

use App\Models\Card;
use App\Models\User;
Use App\Repositories\UserRepository;

class CardRepository extends BaseRepository
{
  public function __construct()
  {
    parent::__construct(Card::class);
  }

  public function getAllCardsByCustomer($user_uuid)  
  {
    $userRepository = new UserRepository();
    $user = $userRepository->getByUuid($user_uuid)->first();
    return $this->model->where('user_id', $user->id)->paginate(config('app.pagination')) ?? false;
  }
}


