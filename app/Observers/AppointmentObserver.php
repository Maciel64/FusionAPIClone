<?php

namespace App\Observers;

use App\Facades\BillingFacade;
use App\Facades\CacheFacade;
use App\Models\Appointment;

class AppointmentObserver
{
    /**
     * Handle the Appointment "created" event.
     *
     * @param  \App\Models\Appointment  $appointment
     * @return void
     */
    public function created(Appointment $appointment)
    {
      CacheFacade::forgetByArray($appointment, ['all', 'get']);

    }

    /**
     * Handle the Appointment "updated" event.
     *
     * @param  \App\Models\Appointment  $appointment
     * @return void
     */
    public function updated(Appointment $appointment)
    {
      CacheFacade::forgetByArray($appointment, ['all', 'get', $appointment->id, $appointment->uuid]);
      // if($appointment->status == 'finished') {
      //   BillingFacade::store($appointment->customer_id, get_class($appointment), $appointment->id, $appointment->value_total);
      // }
    }

    /**
     * Handle the Appointment "deleted" event.
     *
     * @param  \App\Models\Appointment  $appointment
     * @return void
     */
    public function deleted(Appointment $appointment)
    {
      CacheFacade::forgetByArray($appointment, ['all', 'get', $appointment->id, $appointment->uuid]);
    }

    /**
     * Handle the Appointment "restored" event.
     *
     * @param  \App\Models\Appointment  $appointment
     * @return void
     */
    public function restored(Appointment $appointment)
    {
        //
    }

    /**
     * Handle the Appointment "force deleted" event.
     *
     * @param  \App\Models\Appointment  $appointment
     * @return void
     */
    public function forceDeleted(Appointment $appointment)
    {
        //
    }
}
