<?php

namespace App\Observers;

use App\Facades\CacheFacade;
use App\Models\Schedule;

class ScheduleObserver
{
    /**
     * Handle the Schedule "created" event.
     *
     * @param  \App\Models\Schedule  $schedule
     * @return void
     */
    public function created(Schedule $schedule)
    {
      CacheFacade::forgetByArray($schedule, ['all', 'get']);
    }

    /**
     * Handle the Schedule "updated" event.
     *
     * @param  \App\Models\Schedule  $schedule
     * @return void
     */
    public function updated(Schedule $schedule)
    {
      CacheFacade::forgetByArray($schedule, ['all', 'get', $schedule->id, $schedule->uuid]);
    }

    /**
     * Handle the Schedule "deleted" event.
     *
     * @param  \App\Models\Schedule  $schedule
     * @return void
     */
    public function deleted(Schedule $schedule)
    {
      CacheFacade::forgetByArray($schedule, ['all', 'get', $schedule->id, $schedule->uuid]);
    }

    /**
     * Handle the Schedule "restored" event.
     *
     * @param  \App\Models\Schedule  $schedule
     * @return void
     */
    public function restored(Schedule $schedule)
    {
        //
    }

    /**
     * Handle the Schedule "force deleted" event.
     *
     * @param  \App\Models\Schedule  $schedule
     * @return void
     */
    public function forceDeleted(Schedule $schedule)
    {
        //
    }
}
