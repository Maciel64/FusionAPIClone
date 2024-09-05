<?php


namespace App\Services;

use App\Repositories\UserRepository;

class PartnerService extends UserService
{
  public function listAll()
  {
    $repository = new UserRepository();
    $partners   = $repository->getAllUsersByRole('partner');
    return $partners ? $partners->pluck('name', 'uuid') : false;
  }
}