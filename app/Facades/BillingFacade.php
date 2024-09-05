<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class BillingFacade extends Facade 
{
  protected static function getFacadeAccessor() 
  {
    return 'BillingFacade';
  }
}