<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class UserRepository extends BaseRepository
{

  public function __construct()
  {
    parent::__construct(User::class);
  }

  public function getByName($name)
  {
    // with like
    return $this->model->where('name', 'like', "%$name%")->get();
  }

  public function getByUuid($uuid)
  {
    // with like
    return $this->model->where('uuid', $uuid)->get();
  }
  
  public function getByEmail($email)
  {
    return $this->model->where('email', $email)->first();
  }

  public function getAllUsersByRole($role, $paginate)
  {
    $this->model->makeVisible('roles');
    if($paginate == 'false'){
      return $this->model->role($role)->orderBy('name', 'asc')->get();
    }
    return $this->model->role($role)->orderBy('name', 'asc')->paginate(10);
  }

  public function getAllUsers($role)
  {
    // if(!auth()->user()->hasRole('owner') || !auth()->user()->hasRole('admin')) return false;
    $this->model->makeVisible('roles');
    return $this->model->role($role)->get();
  }

  public function getSpecialistCount($dateInit, $dateEnd)
  {
    $countCustomer = $this->model->makeVisible('roles', 'created_at')
      ->role('customer')
      ->whereBetween('created_at', [$dateInit.' 00:00:00', $dateEnd.' 23:59:59'])
      ->count();
    return $countCustomer;
  }

  public function getAllCustomersSorted(String $attribute, String $sortBy)
  {
    $this->model->makeVisible('roles','advice.health_advice');

    $customers = User::all();
    
    if($attribute == 'health_advice'){
      $model = new User();
      $query = $model->select('users.*')->join('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
      ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
      ->where('roles.name', 'customer');

      $query->join('health_advice_has_users', 'health_advice_has_users.user_id', '=', 'users.id')
        ->orderBy('health_advice_has_users.health_advice', $sortBy);
      
      return $query->paginate(30);
    }    

    return $this->model->role('customer')->orderBy($attribute, $sortBy)->paginate(30);
  }




}