<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class SendGridFacade extends Facade 
{
  protected static function getFacadeAccessor() 
  {
    return 'SendGridFacade';
  }
}