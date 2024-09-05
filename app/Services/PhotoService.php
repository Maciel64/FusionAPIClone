<?php

namespace App\Services;

use App\Repositories\CoworkingRepository;
use App\Repositories\PhotoRepository;
use App\Repositories\RoomRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Storage;

class PhotoService
{

  private $disk = 'public';
  private $uuid = null;

  public function storeBulkPhotos(array $data)
  {
    $this->uuid =  $data['uuid'];
    $this->disk = $data['type'];
    foreach ($data['photos'] as $value) 
      $response[] = $this->createOrUpdatePhoto(['photo' => $value]);
    return $response;
  }
  
  public function createOrUpdatePhoto(array $data)
  {
    if($this->uuid == null and isset($data['uuid'])) {
      $this->uuid = $data['uuid'];
      $this->disk = $data['type'];
    }

    $model = $this->mergeDataModel($data);
    if($this->disk == 'avatar') {
      $photo = $this->updatePhotoIfExists($model, $data);
      if($photo) return $photo;
    }

    $repository = new PhotoRepository();
    $this->putFile($data);
    return $repository->create($data);
  }

  public function putFile(&$data)
  {
    $storage = UploadService::handle($this->disk, $data['photo']);
    $data['name'] = $storage->name;
    $data['url']  = $storage->url;
  }

  private function mergeDataModel(array &$data): object
  {
    switch ($this->disk) {
      case 'avatar':
        $model = new UserRepository();
        break;
      case 'coworking':
        $model = new CoworkingRepository();
        break;
      case 'room':
        $model = new RoomRepository();
        break;
      case 'room_main_photo':
        $model = new RoomRepository();
        break;
      case 'coworking_main_photo':
        $model = new CoworkingRepository();
        break;        
    }

    $model = $model->findByUuid($this->uuid);
    $data['model_type'] = get_class($model);
    
    //Model Type custom para fotos de perfil de sala e coworking :)
    if(in_array($this->disk, ['room_main_photo', 'coworking_main_photo'])){$data['model_type'] .= 'Photo';}

    $data['model_id'] = $model->id;
    return $model;
  }

  private function updatePhotoIfExists(object $model, $data)
  {
    $photo = $model->photos()->first();
    if (!$photo) return false;
    $this->putFile($data);
    UploadService::delete($this->disk, $photo->name);
    return $photo->update(['name' => $data['name'], 'url' => $data['url']]) ? $photo->fresh() : false;
  }

  public function getPhotosByModelUuid(string $uuid)
  {
    $this->uuid = $uuid;
    $model = $this->mergeDataModel([]);
    return $model->photos()->paginate(config('settings.paginate_photos')) ?? false;
  }

  public function destroy(string $disk, string $uuid)
  {
    $repository = new PhotoRepository();
    $photo = $repository->findByUuid($uuid);
    Storage::disk($disk)->delete($photo->name);
    return $repository->delete($photo->id);
  }
}