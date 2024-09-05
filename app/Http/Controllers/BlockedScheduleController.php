<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\BlockedScheduleService;
use App\Repositories\BlockedScheduleRepository;
use App\Http\Requests\StoreBlockedScheduleBulkRequest;
use Illuminate\Support\Facades\Auth;

class BlockedScheduleController extends Controller
{
  public function storeBulk(StoreBlockedScheduleBulkRequest $request, BlockedScheduleService $service)
  {
    if(Auth::user()->role_name == 'customer') return false;
    $data     = $request->validated();
    $response = $service->storeBulk($data);
    return $response;
  }

  public function destroy(Request $request, BlockedScheduleService $service){
    $response = $service->delete($request->blocked_uuid);
    return $response;
  }

  public function index(Request $request, BlockedScheduleService $service){
    $response = $service->getAll($request->room_uuid);
    return $this->response('list', $response);
    
  }
}
