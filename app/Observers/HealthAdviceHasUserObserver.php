<?php

namespace App\Observers;

use App\Facades\CacheFacade;
use App\Models\HealthAdviceHasUser;

class HealthAdviceHasUserObserver
{
    /**
     * Handle the HealthAdviceHasUser "created" event.
     *
     * @param  \App\Models\HealthAdviceHasUser  $healthAdviceHasUser
     * @return void
     */
    public function created(HealthAdviceHasUser $healthAdviceHasUser)
    {
      CacheFacade::forgetByArray($healthAdviceHasUser, ['all', 'get']);
    }

    /**
     * Handle the HealthAdviceHasUser "updated" event.
     *
     * @param  \App\Models\HealthAdviceHasUser  $healthAdviceHasUser
     * @return void
     */
    public function updated(HealthAdviceHasUser $healthAdviceHasUser)
    {
      CacheFacade::forgetByArray($healthAdviceHasUser, ['all', 'get', $healthAdviceHasUser->id, $healthAdviceHasUser->uuid]);
    }

    /**
     * Handle the HealthAdviceHasUser "deleted" event.
     *
     * @param  \App\Models\HealthAdviceHasUser  $healthAdviceHasUser
     * @return void
     */
    public function deleted(HealthAdviceHasUser $healthAdviceHasUser)
    {
      CacheFacade::forgetByArray($healthAdviceHasUser, ['all', 'get', $healthAdviceHasUser->id, $healthAdviceHasUser->uuid]);
    }

    /**
     * Handle the HealthAdviceHasUser "restored" event.
     *
     * @param  \App\Models\HealthAdviceHasUser  $healthAdviceHasUser
     * @return void
     */
    public function restored(HealthAdviceHasUser $healthAdviceHasUser)
    {
        //
    }

    /**
     * Handle the HealthAdviceHasUser "force deleted" event.
     *
     * @param  \App\Models\HealthAdviceHasUser  $healthAdviceHasUser
     * @return void
     */
    public function forceDeleted(HealthAdviceHasUser $healthAdviceHasUser)
    {
        //
    }
}
