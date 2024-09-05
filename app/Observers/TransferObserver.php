<?php

namespace App\Observers;

use App\Facades\CacheFacade;
use App\Models\Transfer;

class TransferObserver
{
    /**
     * Handle the Transfer "created" event.
     *
     * @param  \App\Models\Transfer  $transfer
     * @return void
     */
    public function created(Transfer $transfer)
    {
      CacheFacade::forgetByArray($transfer, ['all', 'get']);      
    }

    public function updating(Transfer $transfer)
    {
      if (strtolower($transfer->status) === 'paid') $transfer->paid_at = now();
      if(strtolower($transfer->status) === 'pending') $transfer->paid_at = null;
      if($transfer->discount > 0) $transfer->discount = $transfer->discount * -1;
      $transfer->total = $transfer->amount + $transfer->discount;
    }

    /**
     * Handle the Transfer "updated" event.
     *
     * @param  \App\Models\Transfer  $transfer
     * @return void
     */
    public function updated(Transfer $transfer)
    {
      CacheFacade::forgetByArray($transfer, ['all', 'get', $transfer->id, $transfer->uuid]);
    }

    /**
     * Handle the Transfer "deleted" event.
     *
     * @param  \App\Models\Transfer  $transfer
     * @return void
     */
    public function deleted(Transfer $transfer)
    {
      CacheFacade::forgetByArray($transfer, ['all', 'get', $transfer->id, $transfer->uuid]);
    }

    /**
     * Handle the Transfer "restored" event.
     *
     * @param  \App\Models\Transfer  $transfer
     * @return void
     */
    public function restored(Transfer $transfer)
    {
        //
    }

    /**
     * Handle the Transfer "force deleted" event.
     *
     * @param  \App\Models\Transfer  $transfer
     * @return void
     */
    public function forceDeleted(Transfer $transfer)
    {
        //
    }
}
