<?php

namespace App\Traits;

use App\Models\Coworking;
use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

trait Morph
{
  public function merge(array &$data, Model $model): void
  {
    $data['model_type'] = get_class($model);
    $data['model_id']   = $model->id;
  }

  public function getModelByType(string $type, string $uuid)
  {
    switch ($type) {
      case 'coworking':
        $class = Coworking::class;
        break;
      case 'room': 
        $class = Room::class;
        break;
      case 'user':
        $class = User::class;
        break;
    }

    $model = new $class();
    return $model->where('uuid', $uuid)->first() ?? false;
  }
}