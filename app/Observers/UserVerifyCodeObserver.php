<?php

namespace App\Observers;

use App\Facades\CacheFacade;
use App\Models\UserVerifyCode;

class UserVerifyCodeObserver
{

    public function creating(UserVerifyCode $user)
    {
      $user->expires_at = now()->addDays(15);
    }
    
    /**
     * Handle the UserVerifyCode "created" event.
     *
     * @param  \App\Models\UserVerifyCode  $userVerifyCode
     * @return void
     */
    public function created(UserVerifyCode $userVerifyCode)
    {
      CacheFacade::forgetByArray($userVerifyCode, ['all', 'get']);
    }

    /**
     * Handle the UserVerifyCode "updated" event.
     *
     * @param  \App\Models\UserVerifyCode  $userVerifyCode
     * @return void
     */
    public function updated(UserVerifyCode $userVerifyCode)
    {
      CacheFacade::forgetByArray($userVerifyCode, ['all', 'get', $userVerifyCode->id, $userVerifyCode->uuid]);
    }

    /**
     * Handle the UserVerifyCode "deleted" event.
     *
     * @param  \App\Models\UserVerifyCode  $userVerifyCode
     * @return void
     */
    public function deleted(UserVerifyCode $userVerifyCode)
    {
      CacheFacade::forgetByArray($userVerifyCode, ['all', 'get', $userVerifyCode->id, $userVerifyCode->uuid]);
    }

    /**
     * Handle the UserVerifyCode "restored" event.
     *
     * @param  \App\Models\UserVerifyCode  $userVerifyCode
     * @return void
     */
    public function restored(UserVerifyCode $userVerifyCode)
    {
        //
    }

    /**
     * Handle the UserVerifyCode "force deleted" event.
     *
     * @param  \App\Models\UserVerifyCode  $userVerifyCode
     * @return void
     */
    public function forceDeleted(UserVerifyCode $userVerifyCode)
    {
        //
    }
}
