<?php

namespace App\Observers;

use App\Facades\CacheFacade;
use App\Models\Contact;

class ContactObserver
{
    /**
     * Handle the Contact "created" event.
     *
     * @param  \App\Models\Contact  $contact
     * @return void
     */
    public function created(Contact $contact)
    {
        CacheFacade::forgetByArray($contact, ['all', 'get']);
    }

    /**
     * Handle the Contact "updated" event.
     *
     * @param  \App\Models\Contact  $contact
     * @return void
     */
    public function updated(Contact $contact)
    {
      CacheFacade::forgetByArray($contact, ['all', 'get', $contact->id, $contact->uuid]);
    }

    /**
     * Handle the Contact "deleted" event.
     *
     * @param  \App\Models\Contact  $contact
     * @return void
     */
    public function deleted(Contact $contact)
    {
      CacheFacade::forgetByArray($contact, ['all', 'get', $contact->id, $contact->uuid]);
    }

    /**
     * Handle the Contact "restored" event.
     *
     * @param  \App\Models\Contact  $contact
     * @return void
     */
    public function restored(Contact $contact)
    {
        //
    }

    /**
     * Handle the Contact "force deleted" event.
     *
     * @param  \App\Models\Contact  $contact
     * @return void
     */
    public function forceDeleted(Contact $contact)
    {
        //
    }
}
