<?php

namespace App\Services;

use App\Models\Card;
use App\Models\Plan;
use App\Models\User;
use App\Notifications\PlanPurchasedNotification;
use App\Notifications\PlanUpdateScheduled;
use App\Repositories\CardRepository;
use App\Repositories\PlanRepository;
use App\Repositories\SubscriptionRepository;
use App\Repositories\SubscriptionUpdateRequestRepository;
use App\Repositories\UserRepository;
use App\Traits\Helpers;
use Exception;

class SubscriptionService
{
  use Helpers;

  public function store(string $userUuid, string $planUuid)
  {
    $plan = (new PlanRepository())->findByUuid($planUuid);
    $user = (new UserRepository())->findByUuid($userUuid);
    $card = $user->card();
    $data = [
      'pagarme_card_id' => $card->card_id,
      'user_id'         => $user->id,
      'plan_id'         => $plan->id,
      'price'           => $plan->price,
      'status'          => "active",
    ];

    $subscription = (new SubscriptionRepository())->create($data);
    $user->notify(new PlanPurchasedNotification($plan));
    return $subscription;
  }

  /**
   * @param int $id - Subscription ID
   * @param int $userId
   * @param int $planId
   * 
   */
  public function updatePlan(int $id, int $userId, int $planId)
  {
    $user = (new UserRepository())->find($userId);
    $plan = (new PlanRepository())->find($planId);
    $data = [ 
      'user_id' => $userId, 
      'plan_id' => $planId,
      'price'   => $plan->price,
    ];

    $subscription = (new SubscriptionRepository())->find($id);

    if(!$subscription->update($data)) return false;

    $user->notify(new PlanPurchasedNotification($plan));
    return $subscription->fresh();
  }

  public function schedulePlanUpdate($subscriptionUuid, $userUuid, $planUuid)
  {
    $user         = (new UserRepository())->findByUuid($userUuid);
    $requestPlan  = (new PlanRepository())->findByUuid($planUuid);
    $subscription = (new SubscriptionRepository())->findByUuid($subscriptionUuid);
    $currentPlan  = Plan::find($subscription->plan_id);

    $data = [
      'user_id'         => $user->id,
      'subscription_id' => $subscription->id,
      'plan_id'         => $requestPlan->id,
      'status'          => "pending",
    ];

    $repository = new SubscriptionUpdateRequestRepository();
    $subscriptionUpdateRequest = $repository->findBySubscriptionId($subscription->id);
    
    if($subscriptionUpdateRequest){
      if($subscriptionUpdateRequest->update($data)){
        $user->notify(new PlanUpdateScheduled($currentPlan, $requestPlan));
        return $subscriptionUpdateRequest->fresh();
      }

      return false;
    }

    $subscriptionUpdateRequest = $repository->create($data);
    if($subscriptionUpdateRequest){
      $user->notify(new PlanUpdateScheduled($currentPlan, $requestPlan));
      return $subscriptionUpdateRequest->fresh();
    }
    
    return false;
  }

  public function updateCard(string $subscriptionUuid, string $cardUuid)
  {
    $subscription = (new SubscriptionRepository())->findByUuid($subscriptionUuid);
    $card         = (new CardRepository())->findByUuid($cardUuid);
    return ($subscription->update(['pagarme_card_id' => $card->card_id])) ? 
      $subscription->fresh() : 
    false;
  }

  public function cancel(string $uuid)
  {
    $repository   = new SubscriptionRepository();
    $subscription = $repository->findByUuid($uuid);
    
    return ($subscription->update(['status' => 'canceled'])) ?
       $subscription->fresh():
    false;
  }

}