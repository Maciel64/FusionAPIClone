<?php

namespace App\Services;

use App\Facades\PagarmeFacade;
use App\Jobs\CustomerBillingAttemptJob;
use App\Jobs\GenerateOrderToPaymentByUserJob;
use App\Models\Billing;
use App\Models\BillingFail;
use App\Models\Card;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use App\Notifications\InadimplencyNotification;
use App\Repositories\FailedChargesRepository;
use App\Traits\Helpers;
use Exception;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Log;

class BillingService 
{
  use Helpers;

  private $billings;
  private $modelType;

  public function generateAppointmentOrder($data, $user, $room, $bulk = false)
  {
    $payload = $this->assembleAppointmentPayload($data, $user, $room, $bulk);
    $response = PagarmeFacade::createOrder($payload);

    return $response;
  }

  public function store(int $userId, string $modelType, int $modelId, $order)
  {
    //dd($order);
    $billing = Billing::create([
      'user_id'    => $userId,
      'model_type' => $modelType,
      'model_id'   => $modelId,
      'amount'     => $order['amount']/100,
      'order_id'       => $order['id'],
      'order_code'     => $order['code'],
      'closed'         => $order['closed'] ? 1 : 0,
      'paid'           => $order['status'] == 'paid' ? 1 : 0,
      'payment_method' => 'Credit Card - Pagar.me',
      'payment_at'     => now(),      
    ]);

    if($billing) return $billing->fresh();
    throw new Exception('Billing - Error to create billing');
  }

  // // método responsável por atualizar o registro de cobrança - VERIFICAR SE É NECESSÁRIO
  // public function update(Billing $billing, string $orderId, string $orderCode)
  // {
  //   $billingUpdated = $billing->update([
  //     'order_id'       => $orderId,
  //     'order_code'     => $orderCode,
  //     'closed'         => true,
  //     'paid'           => true,
  //     'payment_method' => 'Credit Card - Pagar.me',
  //     'payment_at'     => now(),
  //   ]);

  //   if($billingUpdated) return $billing->fresh();
  //   throw new Exception('Billing - Error to update billing');
  // }

  // Método responsável por gerar um relatório a partir de um range de data
  public function generateReport(int $userId, string $startDate, string $endDate)
  {
    $startDate = Date::parse($startDate)->startOfDay();
    $endDate   = Date::parse($endDate)->endOfDay();
    $billingReport   = Billing::where('user_id', $userId)
      ->whereBetween('created_at', [$startDate, $endDate])
      ->get();

    if($billingReport) return $billingReport;
  }

   // Método responsável por excluir um registro de cobrança
   public function destroy(Billing $billing)
   {
     $billingDeleted = $billing->delete();
     if($billingDeleted) return true;
     throw new Exception('Billing - Error to delete billing');
   }

  // hotifix - analisar se realmente está pegando os usuários activos
  // O Respectivo método filtra todos os usuários com a conta ativa e em casos de desativados, 
  // filtra os que foram desativados a menos de 30 dias que é período cobrança vigente
  // em seguida, é gerado uma lista de jobs para cada usuário que será processado de forma assíncrona
  // public function generateOrderListToPayment(string $modelType)
  // {
  //   $customers = User::select('users.*')
  //   ->join('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
  //   ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
  //   ->where('roles.name', 'customer')
  //   ->where('users.account_deactivated_at', null)
  //   ->where('users.status', 'adimplente')
  //   ->orWhere('users.account_deactivated_at', '>', now()->subDays(30))
  //   ->get();

  //   foreach ($customers as $customer) {
  //     GenerateOrderToPaymentByUserJob::dispatch($customer->id, $modelType);
  //   }
  // }

  // public function generateOrderByAttempt(BillingFail &$billingFail)
  // {
  //   $user = User::find($billingFail->user_id);
  //   $payload = $this->assemblePayloadToGenerateOrderByAttempt($user, $billingFail);
  //   $response = $this->sendOrderAndUpdateBilling($payload, $user, $billingFail->model_type);

  //   return $response;
  // }

  // public function getBillingsByReferenceFail(BillingFail $billingFail)
  // {
  //   $billings = Billing::where('user_id', $billingFail->user_id)
  //     ->where('model_type', $billingFail->model_type)
  //     ->where('paid', $billingFail->status);
    
  //   if($billingFail->model_type == Subscription::class) 
  //     return $billings->get();

  //   switch ($billingFail->reference_type) {
  //     case 'daily':
  //       $dayInit = Date::parse($billingFail->reference_date)->startOfDay();
  //       $dayEnd  = Date::parse($billingFail->reference_date)->endOfDay();
  //       $billings->whereBetween('created_at', [$dayInit, $dayEnd]);
  //       break;
      
  //     case 'monthly':
  //       $month = Date::parse($billingFail->reference_date)->month;
  //       $billings->whereMonth('created_at', $month);
  //       break;
  //   }

  //   return $billings->get();
  // }

  // private function assemblePayloadToGenerateOrderByAttempt(User $user, BillingFail $billingFail)
  // {
  //   $card = $user->card();

  //   $payload = [
  //     'code'        => $user->uuid, // user code uuid to identify the order
  //     'customer_id' => $card->customer_id,
  //     'closed'      => true,
  //   ];

  //   $this->billings = $this->getBillingsByReferenceFail($billingFail);
  //   $this->generateItems($this->billings, $payload);
  //   $this->generateDataToPayment($card, $payload);
  //   return $payload;
  // }

  // Método responsável por gerar o payload para a criação de uma ordem de pagamento
  // public function generateOrderToPayment(int $userId, string $modelType):array
  // {
  //   try {
  //     $user    = User::find($userId)->makeVisible(['subscription']);
  //     $payload = $this->assemblePayloadToOrderCreate($user, $modelType);

  //     return $this->sendOrderAndUpdateBilling($payload, $user, $modelType);
  //   } catch (\Throwable $th) {
  //     throw $th;
  //   }
  // }


  // public function sendOrderAndUpdateBilling(array $payload, User $user, string $modelType, $attempt=false)
  // {

  //   if(count($payload['items']) == 0) return [];

  //   $pagarmeService = new PagarmeService();
  //   $response       = $pagarmeService->createOrder($payload);

  //   $this->updateRegistersBilling($response->json(), $user);
  //   (new BillingFailService)->handler($response->json('status'), $user, $modelType);

  //   return $response->json();
  // }

  // // Método responsável por receber a resposta de geração de ordem de pagamento e atualizar os registros de cobrança mediante a resposta
  // private function updateRegistersBilling(array $order)
  // {
  //   if(!(isset($order['id']) and isset($order['status']) and isset($order['code']))) throw new Exception('Order - Error to create order');

  //   $data = $this->assembleDataToUpdateBilling($order);
  //   foreach($this->billings as $billing) {
  //     $billing->update($data);
  //   }
  // }

  // private function assembleDataToUpdateBilling(array &$order){
  //   $data = [
  //     'order_id'       => $order['id'],
  //     'order_code'     => $order['code'],
  //     'paid'           => $order['status'],
  //     'payment_at'     => $order['status'] == 'paid'? now():null,
  //     'payment_method' => 'Credit Card - Pagar.me',
  //   ];
    
  //   return $data;
  // }

  // private function assemblePayloadToOrderCreate(User $user, string $modelType)
  // {
  //   $card         = $user->card();
  //   $subscription = $user->subscription;

  //   if(!$card or !$subscription) 
  //     throw new Exception('[Generate Payload to create order] - Error to find card or subscription. userUuid:'. $user->uuid);

  //   $payload = [
  //     'code'        => $user->uuid, // user code uuid to identify the order
  //     'customer_id' => $card->customer_id,
  //     'closed'      => true,
  //   ];

  //   $this->billings = ($modelType == Subscription::class)?
  //     $this->addSubscriptionToCharge($user->id, $subscription):
  //     ($this->getBillingsByBillingTypeConfig($user, $modelType))->get();
  //   $this->generateItems($this->billings, $payload);
  //   $this->generateDataToPayment($card, $payload);
  //   return $payload;
  // }

  public function assembleAppointmentPayload($data, $user, $room, $bulk = false)
  {
    $card         = $user->card;
    // $subscription = $user->subscription;

    if(!$card) 
      throw new Exception('[Generate Payload to create order] - Error to find card. userUuid:'. $user->uuid);
    

    $payload = [
      'code'        => $user->uuid, // user code uuid to identify the order
      'customer_id' => $user['customer_id'], 
      'closed'      => true,
    ];
    if(!$bulk ){
      $data = [$data];
    }

    $this->generateAppointmentItems($data, $payload, $room);
    $this->generateDataToPayment($card, $payload);
    return $payload;
  }

  // private function generateItems($billings,  array &$payload): void
  // {
  //   $items = [];
    
  //   foreach($billings as $billing) {
  //     $modelType = explode('\\', $billing->model_type);
  //     $items[] = [
  //       'amount'      => (int) $billing->amount * 100,
  //       'description' => end($modelType),
  //       'quantity'    => 1,
  //       'code'        => $billing->uuid,
  //     ];
  //   }

  //   $payload['items'] = $items;
  // }

  private function generateAppointmentItems($appointments,  array &$payload, $room): void
  {
    
    $items = [];
    foreach($appointments as $appointment) {
      //dd($appointment['time_init']);
      $day_f = date("d/m/Y", strtotime($appointment['time_init']));
      $init_f = date("H:i", strtotime($appointment['time_init']));
      $end_f = date("H:i", strtotime($appointment['time_end']));

      $items[] = [
        'amount'      => (int) $appointment['value_total'] * 100,
        'description' => 'Agendamento da sala '.$room->name.' na clínica '.$room->coworking->name.' dia '.$day_f.' das '.$init_f.'h às '.$end_f.'h',
        'quantity'    => 1,
        'code'        => uniqid(),
      ];
    }
    // dd($items);
    $payload['items'] = $items;
  }

  private function generateDataToPayment(Card $card, array &$payload):void
  {
    $payload['payments'][] = [
      'payment_method' => 'credit_card',
      'credit_card'    => [
        'operation_type' => 'auth_and_capture',
        'installments'   => 1,
        'card_id'        => $card->card_id,
      ]
    ];
  }

  // private function getBillingsByBillingTypeConfig(User $user, string $model)
  // {
  //   $billing = Billing::where('user_id', $user->id)->where('model_type', $model);

  //   switch (config('settings.billing_type')) {
  //     case 'yearly':
  //       $lastYaer = Date::now()->subYear()->format('Y');
  //       $billings = $billing->whereYear('created_at', $lastYaer)->get();
  //       break;
  //     case 'monthly':
  //       $lastMonth = Date::now()->day(20)->subMonth()->format('m');
  //       $billings = $billing->whereMonth('created_at', $lastMonth);
  //       break;
  //     case 'weekly':
  //       $lastWeek = Date::now()->subWeek()->format('W');
  //       $billings = $billing->whereWeek('created_at', $lastWeek);
  //       break;
  //     case 'daily':
  //       $dataInit = Date::now()->subDay()->startOfDay()->second(1)->format('Y-m-d H:i:s');
  //       $dataEnd  = Date::now()->subDay()->endOfDay()->format('Y-m-d H:i:s');
  //       $billings = $billing->whereBetween('created_at', [$dataInit, $dataEnd]);
  //       break;
  //   }

  //   return $billings->where('paid', 'pending');
  // }

  // private function findBillingBySubscriptionThisMonth(int $userId, Subscription $subscription)
  // {
  //   $currentMonth = now()->format('m');
  //   return Billing::where('user_id', $userId)
  //   ->whereMonth('created_at', $currentMonth)
  //   ->where('model_type', Subscription::class)
  //   ->where('model_id', $subscription->id)
  //   ->first();
  // }

  // private function addSubscriptionToCharge(int $userId, Subscription $subscription)
  // {
  //   $billingSubscription = $this->findBillingBySubscriptionThisMonth($userId, $subscription);
    
  //   if(isset($billingSubscription->paid) and $billingSubscription->paid == 'pending') {
  //     return [$billingSubscription];
  //   }

  //   if(isset($billingSubscription->paid) and in_array($billingSubscription->paid, ['paid', 'failed'])) {
  //     return [];
  //   }

  //   return [$this->store($userId, Subscription::class, $subscription->id, $subscription->price)];
  // }

}