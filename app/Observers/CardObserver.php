<?php

namespace App\Observers;

use App\Facades\CacheFacade;
use App\Models\Card;

class CardObserver
{
    /**
     * Handle the Card "created" event.
     *
     * @param  \App\Models\Card  $card
     * @return void
     */
    public function created(Card $card)
    {
      CacheFacade::forgetByArray($card, ['all', 'get']);
    }

    /**
     * Handle the Card "updated" event.
     *
     * @param  \App\Models\Card  $card
     * @return void
     */
    public function updated(Card $card)
    {
      CacheFacade::forgetByArray($card, ['all', 'get', $card->id, $card->uuid]);
    }

    /**
     * Handle the Card "deleted" event.
     *
     * @param  \App\Models\Card  $card
     * @return void
     */
    public function deleted(Card $card)
    {
      CacheFacade::forgetByArray($card, ['all', 'get', $card->id, $card->uuid]);
    }

    /**
     * Handle the Card "restored" event.
     *
     * @param  \App\Models\Card  $card
     * @return void
     */
    public function restored(Card $card)
    {
        //
    }

    /**
     * Handle the Card "force deleted" event.
     *
     * @param  \App\Models\Card  $card
     * @return void
     */
    public function forceDeleted(Card $card)
    {
        //
    }
}
