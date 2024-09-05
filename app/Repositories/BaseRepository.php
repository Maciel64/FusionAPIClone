<?php

namespace App\Repositories;

use App\Facades\CacheFacade;
use App\Traits\Key;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class BaseRepository
{
  /**
   * Model instance
   */
  protected $model; 

  public function __construct($model)
  {
    $this->model = new $model;
  }

  public function find(int $id)
  {
    $model = $this->model;
    $key   = CacheFacade::keyGen(get_class($model), $id);
    return Cache::remember($key, config('cache.expires'), function() use($model, $id){
      return $model->find($id);
    });
  }

  public function findByUuid($uuid)
  {
    $model = $this->model;
    $key   = CacheFacade::keyGen(get_class($model), $uuid);
    return Cache::remember($key, config('cache.expires'), function() use ($model, $uuid){
      return $model->where('uuid', $uuid)->first() ?? false;
    });
  }

  public function findByEmail($email)
  {
    $model = $this->model;
    $key   = CacheFacade::keyGen(get_class($model), $email);
    return Cache::remember($key, config('cache.expires'), function() use($model, $email){
      return $model->where('email', $email)->first();
    });
  }

  public function all()
  {
    $model = $this->model;
    $key   = CacheFacade::keyGen(get_class($model), 'all');
    return Cache::remember($key, config('cache.expires'), function() use($model){
      return $model->all();
    });
  }

  public function get()
  {
    $model = $this->model;
    $key   = CacheFacade::keyGen(get_class($model), 'get');
    return Cache::remember($key, config('cache.expires'), function() use($model){
      return $model->get();
    });
  }

  public function create(array $data)
  {
    return $this->model->create($data);
  }

  public function insert(array $data)
  {
    return $this->model->insert($data);
  }

  public function update(array $data, int $id)
  {
    $record = $this->model->find($id);
    $record->update($data);
    return $record->find($record->id);
  }

  public function updateByUuid(string $uuid, array $data)
  {
    $record = $this->model->where('uuid', $uuid)->first();
    return ($record->update($data))? $record->fresh() : false;
  }

  public function delete(int $id)
  {
    $record = $this->model->find($id);
    return $record->delete();
  }

  public function deleteByUuid($uuid)
  {
    $record = $this->model->where('uuid', $uuid)->first();
    return $record->delete();
  }

  public function where($column, $value)
  {
    return $this->model->where($column, $value);
  }

  public function getModel()
  {
    return $this->model;
  }
}