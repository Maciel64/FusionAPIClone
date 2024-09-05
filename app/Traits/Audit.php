<?php 

namespace App\Traits;

use Illuminate\Support\Str;

trait Audit
{
    protected static function boot()
    {
        parent::boot();
        static::updating(function ($model) {
          if(auth()->check()) $model->updated_by = auth()->user()->uuid;
        });
    }
}