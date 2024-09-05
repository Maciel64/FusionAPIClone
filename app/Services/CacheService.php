<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class CacheService
{
    /**
   * Undocumented function
   *
   * @param string $className Example: Model::class
   * @param string $key
   * @return void
   */
  public function keyGen(string $className, string $key)
  {
    return md5($className."_".$key);
  }

  public function forgetByKeys(array $keys)
  {
    foreach ($keys as $key) {
      Cache::forget($key);
    }
  }

  public function forgetByArray(object $model, array $keys){
    foreach ($keys as $key) {
      $cacheKey = $this->keyGen(get_class($model), $key);
      Cache::forget($cacheKey);
    }
  }
}