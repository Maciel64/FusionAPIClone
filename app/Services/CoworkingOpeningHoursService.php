<?php


namespace App\Services;

use App\Models\Coworking;
use App\Repositories\CoworkingOpeningHoursRepository;
use App\Repositories\CoworkingRepository;
use Illuminate\Database\Eloquent\Model;

class CoworkingOpeningHoursService
{
  private $coworkingRepository;
  private $repository;

  public function __construct()
  {
    $this->repository          = new CoworkingOpeningHoursRepository();
    $this->coworkingRepository = new CoworkingRepository();
  }

  public function index(string $coworkingUuid)
  {
    $coworking = $this->coworkingRepository->findByUuid($coworkingUuid);
    return $this->repository->getByCoworkingId($coworking->id);
  }

  public function makeDefault(Coworking $coworking)
  {
    $openingHours['settings'] = [
      [
        'day_of_week' => 'monday',
        'opening'     => '00:00',
        'closing'     => '23:59',
      ],
      [
        'day_of_week' => 'tuesday',
        'opening'     => '00:00',
        'closing'     => '23:59',
      ],
      [
        'day_of_week' => 'wednesday',
        'opening'     => '00:00',
        'closing'     => '23:59',
      ],
      [
        'day_of_week' => 'thursday',
        'opening'     => '00:00',
        'closing'     => '23:59',
      ],
      [
        'day_of_week' => 'friday',
        'opening'     => '00:00',
        'closing'     => '23:59',
      ],
      [
        'day_of_week' => 'saturday',
        'opening'     => '00:00',
        'closing'     => '23:59',
      ],
      [
        'day_of_week' => 'sunday',
        'opening'     => '00:00',
        'closing'     => '23:59',
      ]
    ];

    return $this->insert($coworking->uuid, $openingHours);
  }

  public function insert(string $coworkingUuid, array $data)
  {

    $coworking           = $this->coworkingRepository->findByUuid($coworkingUuid);
    $result = [];
    foreach ($data['settings'] as &$item) {
      $item['coworking_id'] = $coworking->id;
      $opening = $this->repository->getOpeningByDayOfWeek($coworking->id, $item['day_of_week']);
      if($opening){
        $this->repository->updateByUuid($opening->uuid, $item);
        $result[] = $opening->fresh();
        continue;
      }

      $result[] = $this->repository->create($item);
    }

    return $result;
  }

  public function update(string $coworkingUuid, array $data)
  {
    try{
      $coworking           = $this->coworkingRepository->findByUuid($coworkingUuid);
      $result = [];
      foreach ($data['settings'] as &$item) {
        if(isset($item['uuid'])){
          $result[] = $this->repository->updateByUuid($item['uuid'], $item);
          continue;
        }
        $item['coworking_id'] = $coworking->id;
        $result[] = $this->repository->create($item);
      }
      return $result;
    }catch(\Exception $e){
      throw $e;
    }
  }


  public function destroyBulk($coworkingUuid, $uuids)
  {
    
    $coworking = $this->coworkingRepository->findByUuid($coworkingUuid);
    if(!$coworking instanceof Coworking) return false;

    foreach ($uuids as $uuid) {
      $this->repository->deleteByUuidAndCoworkingId($uuid, $coworking->id);
    }

    $openingHours = $this->repository->getOpeningsByCoworkingIdAndUuids($coworking->id, $uuids);

    return ($openingHours->count() == 0)? true : false;
  }
}