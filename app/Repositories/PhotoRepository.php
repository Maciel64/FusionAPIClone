<?php

namespace App\Repositories;

use App\Models\Photo;
use App\Models\User;
use App\Traits\Morph;
use Illuminate\Support\Facades\Storage;

class PhotoRepository extends BaseRepository
{

  use Morph;

  public function __construct()
  {
    parent::__construct(Photo::class);
  }

  public function getByName($name)
  {
    return $this->model->where('name', 'like', "%$name%")->get();
  }

  public function listByModel($type, $uuid)
  {
    $model = $this->getModelByType($type, $uuid);
    return $this->model->where('model_type', get_class($model))->where('model_id', $model->id)->get(); 
  }

  public function getPhotoByUserUuid($userUuid)
  {
    $user = new UserRepository();
    $user = $user->findByUuid($userUuid);
    return $user->photo()->first() ?? false;
  }

  public function deletePhotoByUuid($uuid)
  {
    $photo = $this->findByUuid($uuid);
    Storage::disk('avatars')->delete($photo->name);
    return $photo->delete();
  }
  
}