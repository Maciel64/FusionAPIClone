<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class AppointmentFacade extends Facade 
{
  protected static function getFacadeAccessor() 
  {
    return 'AppointmentFacade';
  }
}