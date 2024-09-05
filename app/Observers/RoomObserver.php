<?php

namespace App\Observers;

use App\Facades\CacheFacade;
use App\Models\Room;

class RoomObserver
{
    /**
     * Handle the Room "created" event.
     *
     * @param  \App\Models\Room  $room
     * @return void
     */
    public function created(Room $room)
    {
      CacheFacade::forgetByArray($room, ['all', 'get']);
    }

    /**
     * Handle the Room "updated" event.
     *
     * @param  \App\Models\Room  $room
     * @return void
     */
    public function updated(Room $room)
    {
      CacheFacade::forgetByArray($room, ['all', 'get', $room->id, $room->uuid]);
    }

    /**
     * Handle the Room "deleted" event.
     *
     * @param  \App\Models\Room  $room
     * @return void
     */
    public function deleted(Room $room)
    {
      CacheFacade::forgetByArray($room, ['all', 'get', $room->id, $room->uuid]);
    }

    /**
     * Handle the Room "restored" event.
     *
     * @param  \App\Models\Room  $room
     * @return void
     */
    public function restored(Room $room)
    {
        //
    }

    /**
     * Handle the Room "force deleted" event.
     *
     * @param  \App\Models\Room  $room
     * @return void
     */
    public function forceDeleted(Room $room)
    {
        //
    }
}
