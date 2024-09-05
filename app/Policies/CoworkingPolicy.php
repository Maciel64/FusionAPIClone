<?php

namespace App\Policies;

use App\Models\Coworking;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CoworkingPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Coworking  $coworking
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Coworking $coworking)
    {
        //
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
      if($user->hasRole('partner')) {
        return true;
      }
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Coworking  $coworking
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Coworking $coworking)
    {
      if($user->hasRole('partner') and $user->id === $coworking->user_id) {
        return true;
      }
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Coworking  $coworking
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Coworking $coworking)
    {
      if($user->hasRole('partner') and $user->id === $coworking->user_id) {
        return true;
      }
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Coworking  $coworking
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Coworking $coworking)
    {
      if($user->hasRole('owner')) {
        return true;
      }
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Coworking  $coworking
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Coworking $coworking)
    {
      if($user->hasRole('owner')) {
        return true;
      }
    }
}
