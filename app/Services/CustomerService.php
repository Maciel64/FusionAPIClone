<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Log;

class CustomerService extends UserService
{


  public function sortedCustomers($attribute = 'name',$sortBy = 'asc'){
    $repository = new UserRepository();
    if ($sortBy != 'desc') $sortBy = 'asc';
    if ($attribute != 'created_at' && $attribute != 'health_advice') $attribute = 'name';
    $sortedData = $repository->getAllCustomersSorted($attribute,$sortBy);

    return $sortedData;


  }

  public function search($data)
  {
    $model = new User();
    // dd($data['value']);

    // criar uma query que busque o usuário pela role "customer" usando join
    $query = $model->select('users.*')->join('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
      ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
      ->where('roles.name', 'customer');

    if ($data['value'] == '' || $data['value'] == null){
      return $query->orderBy('name', 'asc')->paginate(30);
    }

    if ($data['attribute'] == 'health_advice') {
        $query->join('health_advice_has_users', 'health_advice_has_users.user_id', '=', 'users.id')
            ->where('health_advice_has_users.health_advice', $data['value'])
            ->orderBy('name', 'asc');
            return $query->paginate(30);
    }
    
    if ($data['attribute'] == 'name'){
      $value = isset($data['value']) ? $data['value'] : null;
      $query = $query->where('users.name', 'like', "%$value%")->orderBy('name', 'asc');
      return $query->paginate(30);
    }

    return $query->orderBy('name', 'asc')->paginate(30);
  }
}
