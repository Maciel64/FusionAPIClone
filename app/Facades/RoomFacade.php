<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class RoomFacade extends Facade 
{
  protected static function getFacadeAccessor() 
  {
    return 'RoomFacade';
  }
}