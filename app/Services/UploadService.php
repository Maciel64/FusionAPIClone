<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use stdClass;

class UploadService
{
  private static $storage;

  public static function handle(string $disk, object $file)
  {
    self::putFile($disk, $file);
    self::setNameFile();
    self::setUrlFile();
    return self::$storage;
  }

  public static function delete(string $disk, string $fileName)
  {
    Storage::disk($disk)->delete($fileName);
    if (!Storage::disk($disk)->exists($fileName)) return true;
    return false;
  }

  private static function putFile(string $disk, $file)
  {
    self::$storage = new stdClass();
    self::$storage->file = Storage::putFile($disk, $file);
    self::$storage->disk = $disk;
  }

  private static function setNameFile():void
  {
    self::$storage->name = explode('/', self::$storage->file);
    self::$storage->name = end(self::$storage->name);
  } 

  private static function setUrlFile():void
  {
    self::$storage->url = Storage::disk(self::$storage->disk)->url(self::$storage->name);
  }
}
