<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class PlanRepositoryFacade extends Facade 
{
  protected static function getFacadeAccessor() 
  {
    return 'PlanRepositoryFacade';
  }
}