<?php

namespace App\Traits;

use App\Models\Plan;
use App\Models\User;
use App\Services\PagarmeService;

trait PlanTrait
{

  protected function updatePlanInPagarme(Plan $plan, array $data)
  {
    $payload = $this->generatePlanPayload($data, $plan);
    return (new PagarmeService())->editPlan($plan->pagarme_id, $payload);
  }

  protected function registerPlanInPagarme(array $data)
  {
    $payload = $this->generatePlanPayload($data);
    $service = new PagarmeService();
    return $service->createPlan($payload);
  }

  protected function generatePlanPayload(array $data, Plan $plan = null)
  {
    $payload = [
      'name'            => $data['name'] ?? $plan->name,
      'description'     => $data['description'] ?? $plan->description,
      'shippable'       => false,
      'payment_methods' => ['credit_card'],
      'interval_count'  => 1,
      'billing_type'    => 'postpaid',
      'pricing_scheme'  => [
        'scheme_type' => 'unit',
        'price'       => $data['price'] ?? $plan->price,
      ],
      'quantity' => 1,
    ];

    if($plan){
      $payload['status']         = $plan->status;
      $payload['currency']       = $plan->currency;
      $payload['interval']       = $plan->interval;
      $payload['interval_count'] = 1;
    }

    return $payload;
  }

  protected function generateDataPlan(array $response)
  {
    return [
      'pagarme_id'        => $response['id'],
      'plan_pagarme_id'   => $response['items'][0]['id'],
      'name'              => $response['name'],
      'price'             => $response['items'][0]['pricing_scheme']['price'],
      'type'              => $response['items'][0]['pricing_scheme']['scheme_type'],
      'description'       => $response['description'],
      'url'               => $response['url'],
      'interval'          => $response['interval'],
      'billing_type'      => $response['billing_type'],
      'payment_methods'   => $response['payment_methods'][0],
      'installments'      => $response['installments'][0],
      'status'            => $response['status'],
      'currency'          => $response['currency'],
      'quantity'          => $response['items'][0]['quantity'],
      'trial_period_days' => null,
    ];
  }

  protected function generateSubscriptionPayload(User $user, int $planId)
  {
    $payload = [
      'code'           => $user->uuid,
      'plan_id'        => $planId,
      'payment_method' => 'credit_card',
      'start_at'       => now()->format('Y-m-d'),
      'customer_id'    => $user->customer->pagarme_id,
      'card_token'     => $user->card->token,
    ];

    return $payload;
  }
}