<?php

namespace App\Observers;

use App\Facades\CacheFacade;
use App\Models\PlanHasUser;

class PlanHasUserObserver
{
    /**
     * Handle the PlanHasUser "created" event.
     *
     * @param  \App\Models\PlanHasUser  $planHasUser
     * @return void
     */
    public function created(PlanHasUser $planHasUser)
    {
      CacheFacade::forgetByArray($planHasUser, ['all', 'get']);
    }

    /**
     * Handle the PlanHasUser "updated" event.
     *
     * @param  \App\Models\PlanHasUser  $planHasUser
     * @return void
     */
    public function updated(PlanHasUser $planHasUser)
    {
      CacheFacade::forgetByArray($planHasUser, ['all', 'get', $planHasUser->id, $planHasUser->uuid]);
    }

    /**
     * Handle the PlanHasUser "deleted" event.
     *
     * @param  \App\Models\PlanHasUser  $planHasUser
     * @return void
     */
    public function deleted(PlanHasUser $planHasUser)
    {
      CacheFacade::forgetByArray($planHasUser, ['all', 'get', $planHasUser->id, $planHasUser->uuid]);
    }

    /**
     * Handle the PlanHasUser "restored" event.
     *
     * @param  \App\Models\PlanHasUser  $planHasUser
     * @return void
     */
    public function restored(PlanHasUser $planHasUser)
    {
        //
    }

    /**
     * Handle the PlanHasUser "force deleted" event.
     *
     * @param  \App\Models\PlanHasUser  $planHasUser
     * @return void
     */
    public function forceDeleted(PlanHasUser $planHasUser)
    {
        //
    }
}
