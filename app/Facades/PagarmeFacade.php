<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class PagarmeFacade extends Facade 
{
  protected static function getFacadeAccessor() 
  {
    return 'PagarmeFacade';
  }
}