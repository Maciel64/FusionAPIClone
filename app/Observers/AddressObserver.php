<?php

namespace App\Observers;

use App\Facades\CacheFacade;
use App\Models\Address;
use App\Traits\Key;
use Illuminate\Support\Facades\Cache;

class AddressObserver
{


    /**
     * Handle the Address "created" event.
     *
     * @param  \App\Models\Address  $address
     * @return void
     */
    public function created(Address $address)
    {
      CacheFacade::forgetByArray($address, ['all', 'get']);
    }

    /**
     * Handle the Address "updated" event.
     *
     * @param  \App\Models\Address  $address
     * @return void
     */
    public function updated(Address $address)
    {
      CacheFacade::forgetByArray($address, ['all', 'get', $address->id, $address->uuid]);
    }

    /**
     * Handle the Address "deleted" event.
     *
     * @param  \App\Models\Address  $address
     * @return void
     */
    public function deleted(Address $address)
    {
      CacheFacade::forgetByArray($address, ['all', 'get', $address->id, $address->uuid]);
    }

    /**
     * Handle the Address "restored" event.
     *
     * @param  \App\Models\Address  $address
     * @return void
     */
    public function restored(Address $address)
    {
        //
    }

    /**
     * Handle the Address "force deleted" event.
     *
     * @param  \App\Models\Address  $address
     * @return void
     */
    public function forceDeleted(Address $address)
    {
        //
    }
}
