<?php

namespace App\Services;

use App\Repositories\RoomRepository;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use App\Http\Requests\StoreBlockedScheduleBulkRequest;
use App\Repositories\BlockedScheduleRepository;
use App\Models\BlockedSchedule;
use League\CommonMark\Extension\Attributes\Node\Attributes;

class BlockedScheduleService
{
  public function storeBulk($data)
  {
    $room = (new RoomRepository)->findByUuid($data['room_uuid']);
    foreach ($data['blockeds'] as $blockedCheck) {
      $repository = new BlockedScheduleRepository();
      $response = $repository->checkDate($room->id, $blockedCheck['time_init']);
      if($response == $blockedCheck['time_init']) 
      return response()->json([
        'success' => false,
        'message' => 'Blocked schedules could not be saved. Duplicate time_init found.',
        'data' => null,
      ], 422);
    }
    foreach ($data['blockeds'] as $blocked) {
      $blockedSchedule = new BlockedSchedule();
      $blockedSchedule->room_id = $room->id;
      $blockedSchedule->time_init = Carbon::parse($blocked['time_init']);
      $blockedSchedule->time_end = Carbon::parse($blocked['time_end']);
      $blockedSchedule->save();
      $savedBlockedSchedules[] = $blockedSchedule;
    }

    return [
      'success' => true,
      'message' => 'Blocked schedules saved successfully',
      'data' => $savedBlockedSchedules,
    ];
  }

  public function delete($blocked_uuid){
    $blockedRepository = new BlockedScheduleRepository();
    $blockedSchedule = $blockedRepository->findBlockedByUuid($blocked_uuid);

    if(!$blockedSchedule || $blockedSchedule == false || !isset($blockedSchedule->first()->uuid)) return response()->json(['error' => 'Horário não encontrado.'], 404);

    $blockedRepository->deleteBlocked($blocked_uuid);
    return response()->json(['message' => 'Horário desbloqueado.']);
  }

  public function getAll($room_uuid){
    $repository = new BlockedScheduleRepository();
    $room = (new RoomRepository)->findByUuid($room_uuid);
    $response = $repository->getAllBlockedSchedules($room->id);
    return $response;
  }

}