<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSubscriptionRequest;
use App\Http\Requests\UpdateScheduleRequest;
use App\Http\Requests\UpdateSubscriptionRequest;
use App\Models\Subscription;
use App\Repositories\SubscriptionRepository;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
 
  public function store(Request $request, SubscriptionService $service)
  {
    $request->validate(['plan_uuid' => 'required|uuid|exists:plans,uuid']);
    $response = $service->store($request->uuid, $request->plan_uuid);
    return $this->response('store', $response);
  }

  public function update(Request $request, SubscriptionService $service)
  {
    $request->validate(['plan_uuid' => 'required|uuid|exists:plans,uuid']);
    $response = $service->schedulePlanUpdate($request->subscription_uuid, $request->uuid, $request->plan_uuid);
    return $this->response('update', $response);
  }

  public function show(Request $request, SubscriptionRepository $repository)
  {
    $response = $repository->findByUuid($request->subscription_uuid);
    return $this->response('show', $response);
  }

  public function cancel(Request $request, SubscriptionService $service)
  {
    $response = $service->cancel($request->subscription_uuid);
    return $this->response('cancel', $response);
  }
}
