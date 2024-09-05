<?php

namespace App\Repositories;

use App\Models\Address;
use App\Models\Coworking;
use App\Models\Room;
use App\Traits\Morph;
use Illuminate\Database\Eloquent\Model;

class AddressRepository extends BaseRepository
{
  use Morph;

  public function __construct()
  {
    parent::__construct(Address::class);
  }

  public function store(array $data)
  {
    $model = $this->merge($data);
    $address = $this->create($data);
    return $address ? $model->addresses()->first() : false;
  }

  private function merge(array &$data): Model
  {
    switch ($data['type']) {
      case 'user':
        $model = new UserRepository();
        break;
      case 'coworking':
        $model = new CoworkingRepository();
        break;
      case 'room':
        $model = new RoomRepository();
        break;
      // case 'patient':
      //   $model = new PatientRepository();
      //   break;
    }

    $model = $model->findByUuid($data['uuid']);
    $data['model_type'] = get_class($model);
    $data['model_id'] = $model->id;
    return $model;
  }

  public function getCoworkingByCity(string $city)
  {
    return $this->model
      ->select('model_id', 'model_type')
      ->where('city', 'LIKE', "%$city%")
      ->where('model_type', Coworking::class)
      ->get() ?? false;
  }

  public function getCoworkingByNeighborhood(string $neighborhood)
  {
    return $this->model
      ->select('model_id', 'model_type')
      ->where('neighborhood', 'LIKE', "%$neighborhood%")
      ->where('model_type', Coworking::class)
      ->get() ?? false;
  }

  public function getCoworkingByLocation(string $location)
  {
    $response = $this->model
      ->select('model_id', 'model_type')
      ->where('neighborhood', 'LIKE', "%$location%")
      ->orWhere('city', 'LIKE', "%$location%")
      ->orWhere('line_1', 'LIKE', "%$location%")
      ->where('model_type', Coworking::class)
      ->distinct()
      ->get() ?? false;

    $response->each(function ($coworking) {
      $coworking->makeVisible('model_id', 'model_type');
    });
    return $response;
  }

}