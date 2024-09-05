<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class ScheduleServiceFacade extends Facade 
{
  protected static function getFacadeAccessor() 
  {
    return 'ScheduleServiceFacade';
  }
}