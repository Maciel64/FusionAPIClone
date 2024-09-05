<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\UserVerifyCode;
use App\Repositories\BaseRepository;

class UserVerifyCodeRepository extends BaseRepository
{
  public function __construct()
  {
    parent::__construct(UserVerifyCode::class);
  }

  public function destroyCodeVerificationByUser(User $user)
  {
    return $this->where('user_id', $user->id)->delete();
  }

}