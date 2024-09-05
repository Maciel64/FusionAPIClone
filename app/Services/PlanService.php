<?php

namespace App\Services;

use App\Facades\PlanRepositoryFacade;
use App\Facades\UserRepositoryFacade;
use App\Models\Plan;
use App\Models\User;
use App\Repositories\PlanRepository;
use App\Repositories\UserRepository;
use App\Traits\Helpers;
use App\Traits\PlanTrait;
use Exception;

class PlanService
{
  use Helpers;

  public function store(array $data)
  {
    if(isset($data['price'])) $data['price'] = $this->formatMoney($data['price']);
    $repository = new PlanRepository();
    $plan = $repository->create($data);
    if($plan instanceof Plan)
      return $repository->get();
    return false;
  }

  public function update($planUuid, array $data)
  {
    if(isset($data['price'])) $data['price'] = $this->formatMoney($data['price']);
    $repository = new PlanRepository();
    $plan       = $repository->findByUuid($planUuid);
    if($plan->update($data))
      return $repository->get();
    return false;
  }

}