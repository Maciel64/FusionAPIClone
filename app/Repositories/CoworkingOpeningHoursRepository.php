<?php

namespace App\Repositories;

use App\Models\CoworkingOpeningHours;

class CoworkingOpeningHoursRepository extends BaseRepository
{

  public function __construct()
  {
    parent::__construct(CoworkingOpeningHours::class);
  }
  
  public function getOpeningByDayOfWeek($coworkingId, $dayOfWeek)
  {
    return $this->model
    ->where('coworking_id', $coworkingId)
    ->where('day_of_week', $dayOfWeek)->first();
  }

  public function getByCoworkingId($coworkingId)
  {
    return $this->model->where('coworking_id', $coworkingId)->get();
  }

  public function deleteByUuidAndCoworkingId(string $uuid, int $coworkingId)
  {
    return $this->model->where('uuid', $uuid)->where('coworking_id', $coworkingId)->delete();
  }

  public function getOpeningsByCoworkingIdAndUuids(int $coworkingId, array $uuids){
    return $this->model->where('coworking_id', $coworkingId)->whereIn('uuid', $uuids)->get();
  }
}