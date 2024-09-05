<?php

namespace App\Observers;

use App\Facades\CacheFacade;
use App\Models\Coworking;
use App\Services\CoworkingOpeningHoursService;

class CoworkingObserver
{

    public function creating()
    {
    }

    /**
     * Handle the Coworking "created" event.
     *
     * @param  \App\Models\Coworking  $coworking
     * @return void
     */
    public function created(Coworking $coworking)
    {
      (new CoworkingOpeningHoursService())->makeDefault($coworking);
      CacheFacade::forgetByArray($coworking, ['all', 'get']);
    }

    /**
     * Handle the Coworking "updated" event.
     *
     * @param  \App\Models\Coworking  $coworking
     * @return void
     */
    public function updated(Coworking $coworking)
    {
      CacheFacade::forgetByArray($coworking, ['all', 'get', $coworking->id, $coworking->uuid]);
    }

    /**
     * Handle the Coworking "deleted" event.
     *
     * @param  \App\Models\Coworking  $coworking
     * @return void
     */
    public function deleted(Coworking $coworking)
    {
      CacheFacade::forgetByArray($coworking, ['all', 'get', $coworking->id, $coworking->uuid]);
    }

    /**
     * Handle the Coworking "restored" event.
     *
     * @param  \App\Models\Coworking  $coworking
     * @return void
     */
    public function restored(Coworking $coworking)
    {
        //
    }

    /**
     * Handle the Coworking "force deleted" event.
     *
     * @param  \App\Models\Coworking  $coworking
     * @return void
     */
    public function forceDeleted(Coworking $coworking)
    {
        //
    }
}
