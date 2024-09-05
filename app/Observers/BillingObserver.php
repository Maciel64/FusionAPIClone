<?php

namespace App\Observers;

use App\Facades\CacheFacade;
use App\Models\Billing;

class BillingObserver
{
    /**
     * Handle the Billing "created" event.
     *
     * @param  \App\Models\Billing  $billing
     * @return void
     */
    public function created(Billing $billing)
    {
      CacheFacade::forgetByArray($billing, ['all', 'get']);
    }

    /**
     * Handle the Billing "updated" event.
     *
     * @param  \App\Models\Billing  $billing
     * @return void
     */
    public function updated(Billing $billing)
    {
      CacheFacade::forgetByArray($billing, ['all', 'get', $billing->id, $billing->uuid]);
    }

    /**
     * Handle the Billing "deleted" event.
     *
     * @param  \App\Models\Billing  $billing
     * @return void
     */
    public function deleted(Billing $billing)
    {
      CacheFacade::forgetByArray($billing, ['all', 'get', $billing->id, $billing->uuid]);
    }

    /**
     * Handle the Billing "restored" event.
     *
     * @param  \App\Models\Billing  $billing
     * @return void
     */
    public function restored(Billing $billing)
    {
        //
    }

    /**
     * Handle the Billing "force deleted" event.
     *
     * @param  \App\Models\Billing  $billing
     * @return void
     */
    public function forceDeleted(Billing $billing)
    {
        //
    }
}
