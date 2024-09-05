<?php

namespace App\Observers;

use App\Facades\CacheFacade;
use App\Models\CoworkingOpeningHours;

class CoworkingOpeningHoursObserver
{
    /**
     * Handle the CoworkingOpeningHours "created" event.
     *
     * @param  \App\Models\CoworkingOpeningHours  $coworkingOpeningHours
     * @return void
     */
    public function created(CoworkingOpeningHours $coworkingOpeningHours)
    {
      CacheFacade::forgetByArray($coworkingOpeningHours, ['all', 'get']);
    }

    /**
     * Handle the CoworkingOpeningHours "updated" event.
     *
     * @param  \App\Models\CoworkingOpeningHours  $coworkingOpeningHours
     * @return void
     */
    public function updated(CoworkingOpeningHours $coworkingOpeningHours)
    {
      CacheFacade::forgetByArray($coworkingOpeningHours, ['all', 'get', $coworkingOpeningHours->id, $coworkingOpeningHours->uuid]);
    }

    /**
     * Handle the CoworkingOpeningHours "deleted" event.
     *
     * @param  \App\Models\CoworkingOpeningHours  $coworkingOpeningHours
     * @return void
     */
    public function deleted(CoworkingOpeningHours $coworkingOpeningHours)
    {
      CacheFacade::forgetByArray($coworkingOpeningHours, ['all', 'get', $coworkingOpeningHours->id, $coworkingOpeningHours->uuid]);
    }

    /**
     * Handle the CoworkingOpeningHours "restored" event.
     *
     * @param  \App\Models\CoworkingOpeningHours  $coworkingOpeningHours
     * @return void
     */
    public function restored(CoworkingOpeningHours $coworkingOpeningHours)
    {
        //
    }

    /**
     * Handle the CoworkingOpeningHours "force deleted" event.
     *
     * @param  \App\Models\CoworkingOpeningHours  $coworkingOpeningHours
     * @return void
     */
    public function forceDeleted(CoworkingOpeningHours $coworkingOpeningHours)
    {
        //
    }
}
