<?php

namespace App\Observers;

use App\Facades\CacheFacade;
use App\Models\HealthAdvice;

class HealthAdviceObserver
{
    /**
     * Handle the HealthAdvice "created" event.
     *
     * @param  \App\Models\HealthAdvice  $healthAdvice
     * @return void
     */
    public function created(HealthAdvice $healthAdvice)
    {
      CacheFacade::forgetByArray($healthAdvice, ['all', 'get']);
    }

    /**
     * Handle the HealthAdvice "updated" event.
     *
     * @param  \App\Models\HealthAdvice  $healthAdvice
     * @return void
     */
    public function updated(HealthAdvice $healthAdvice)
    {
      CacheFacade::forgetByArray($healthAdvice, ['all', 'get', $healthAdvice->id, $healthAdvice->uuid]);
    }

    /**
     * Handle the HealthAdvice "deleted" event.
     *
     * @param  \App\Models\HealthAdvice  $healthAdvice
     * @return void
     */
    public function deleted(HealthAdvice $healthAdvice)
    {
      CacheFacade::forgetByArray($healthAdvice, ['all', 'get', $healthAdvice->id, $healthAdvice->uuid]);
    }

    /**
     * Handle the HealthAdvice "restored" event.
     *
     * @param  \App\Models\HealthAdvice  $healthAdvice
     * @return void
     */
    public function restored(HealthAdvice $healthAdvice)
    {
        //
    }

    /**
     * Handle the HealthAdvice "force deleted" event.
     *
     * @param  \App\Models\HealthAdvice  $healthAdvice
     * @return void
     */
    public function forceDeleted(HealthAdvice $healthAdvice)
    {
        //
    }
}
