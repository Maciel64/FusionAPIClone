<?php

namespace App\Services;

use App\Models\Card;
use App\Models\User;
use App\Repositories\CardRepository;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\PagarMeLog;
use stdClass;

class PagarmeService 
{
  private $service;

  public function __construct()
  {
    $this->service = new RequestsService('pagarme');
    $this->service->setHeaders([
      'Authorization' => 'Basic '.base64_encode(config('pagarme.secrete_key').':')
    ]);
  }

  public function storePagarMeLog($userId, $action, $payload, $response)
  {
    PagarMeLog::create([
      'user_id' => $userId,
      'logged_user_name' =>Auth()->user()->name??'public route',
      'action' => $action,
      'request' => json_encode($payload),
      'response' => $response,  
    ]);
  }

  public function getOrders()
  {
    $response = $this->service->send('orders.listOrders');
    return $response->json();
  }

  public function getOrder(string $id)
  {
    $response = $this->service->send('orders.getOrder', ['order_id' => $id]);
    return $response->json();
  }

  public function createCustomer(array $data, int $userId)
  {
    if (!in_array($data['gender'], ['male','female'])){
      $data['gender'] = null;
    }
    $response =  $this->service->send(resource: 'customers.createCustomer', payload: $data);
    $this->storePagarMeLog($userId, 'createCustomer', $data, $response);
    if($response->status() === 200 && $response->json('id')) {

      $user = User::where('id', $userId)->first();
      $user->customer_id = $response['id'];
      $user->save();

      $data = [
        'user_id'     => $userId,
        'customer_id' => $response->json('id'),
        'address_id'  => $response->json('address.id'),
      ];



      return $response;
      
    }else{
      Log::error('Erro ao criar usuário no gateway de pagamento', ['response' => $response->json()]);
    }

    abort(500, 'Erro ao criar usuário no gateway de pagamento');
  }

  // private function createOrUpdateCard(int $userId, array $data)
  // {
  //   $data['user_id'] = $userId;
  //   try {
  //     $card = Card::where('user_id', $userId)->first();
  //     if(!$card)
        
  //       return Card::create($data);

  //     if($card->update($data))
  //       return $card->fresh();
  //     throw new Exception("PagarmeService: Error to update card");      

  //   } catch (\Throwable $th) {
  //     throw $th;
  //     return $th->getMessage();
  //   }
  // }

  public function getCustomer(string $customerId)
  {
    return $this->service->send(resource:'customers.getCustomer', params:['customer_id' => $customerId]);
  }

  public function editCustomer(string $customerId, array $data)
  {
    // dd($data);
    return $this->service->send(resource:'customers.editCustomer', params:['customer_id' => $customerId], payload: $data);
  }

  public function listCustomers()
  {
    return $this->service->send('customers.listCustomers');
  }

  /**
   * Undocumented function
   *
   * @param integer $userId user_id from database
   * @param string $customerId customer_id from pagarme
   * @param array $payload payload to create card
   * @return Response
   */
  public function createCard(int $userId, string $customerId, array $payload)
  {
    $params   = ['customer_id' => $customerId];
    $response = $this->service->send(resource:'customers.createCard' , params: $params, payload: $payload);
    $this->storePagarMeLog($userId, 'createCard', $payload, $response);
    $data     = $this->assembleCardData($response);
    $data['user_id'] = $userId;
    $data['is_default'] = false;

    //Se não tiver nenhum cartão cria um cartão como principal
    $userCard = Card::where('user_id', $userId)->first();
    if(!$userCard){
      $data['is_default'] = true;
    }
    // dd($data);

    return Card::create($data);
    
  }

  // public function updateCard(Card $card, $payload)
  // {
  //   $params = [
  //     'customer_id' => $card->customer_id,
  //     'card_id'     => $card->card_id
  //   ];

  //   $response = $this->service->send(resource: 'customers.editCard', params: $params, payload: $payload);
  //   $data     = $this->assembleCardData($response);
  //   return $this->createOrUpdateCard(userId:$card->user_id, data:$data);
  // }

  public function assembleCardData($response)
  {
    if(!($response->status() === 200 && $response->json('id'))) 
      throw new Exception("Error to assemble card data");

    return [
      "card_id"          => $response->json("id"),
      "first_six_digits" => $response->json("first_six_digits"),
      "last_four_digits" => $response->json("last_four_digits"),
      "brand"            => $response->json("brand"),
      "holder_name"      => $response->json("holder_name"),
      "holder_document"  => $response->json("holder_document"),
      "exp_month"        => $response->json('exp_month'),
      "exp_year"         => $response->json('exp_year'),
      "status"           => $response->json("status"),
      "label"            => $response->json("label")
    ];
  }

  public function getCard($userId)
  {
    try{
      $card = Card::where('user_id', $userId)->first();
      $params = ['customer_id' => $card->customer_id, 'card_id' => $card->card_id];
      return $this->service->send(resource: 'customers.getCard', params: $params);
    }catch(Exception $e) {
      throw new Exception("Error to get card", 1);
    }
  }
  
  public function listCards(string $userId)
  {
    $card = Card::where('user_id', $userId)->first();
    $params = ['customer_id' => $card->customer_id];
    return $this->service->send(resource: 'customers.listCards', params: $params);
  }

  public function deleteCard(string $uuid)
  {
    $repository = new CardRepository();
    $card = $repository->findByUuid($uuid);
    $params = ['customer_id' => $card->user->customer_id, 'card_id' => $card->card_id];
    return $this->service->send(resource: 'customers.deleteCard', params: $params);
  }

  public function renewCard(string $customerId, string $cardId, array $payload)
  {
    $params = ['customer_id' => $customerId, 'card_id' => $cardId];
    return $this->service->send(resource: 'customers.renew', params: $params, payload: $payload);
  }

  public function createTokenCard(int $userId, array $payload)
  {
    $card = Card::where('user_id', $userId)->first();
    $response = $this->service->send(resource: 'customers.createTokenCard', payload: $payload);
    if($card->update(['card_token' => $response->json('id')]))
      return $response;
    throw new Exception("Error to register token card");
  }

  public function createOrder(array $payload)
  {
    return $this->service->send(resource: 'orders.createOrder', payload: $payload);
  }

  public function createTransaction(array $payload)
  {
    return $this->service->send(resource: 'transactions.createTransaction', payload: $payload);
  }

  public function getTransaction(string $id)
  {
    return $this->service->send(resource: 'transactions.getTransaction', params: ['transaction_id' => $id]);
  }

  public function getTransactionByOrderId(string $id)
  {
    return $this->service->send(resource: 'transactions.getTransactionByOrderId', params: ['order_id' => $id]);
  }

  public function getTransactionByCardId(string $id)
  {
    return $this->service->send(resource: 'transactions.getTransactionByCardId', params: ['card_id' => $id]);
  }

  public function createPlan(array $payload)
  {
    return $this->service->send(resource: 'plans.createPlan', payload: $payload);
  }

  public function editPlan(string $id, array $payload)
  {
    return $this->service->send(resource: 'plans.editPlan', params: ['plan_id' => $id], payload: $payload);
  }

  public function createPlanSubscription(array $payload)
  {
    return $this->service->send(resource: 'subscriptions.createPlanSubscription', payload: $payload);
  }

  public function cancelSubscription(string $id)
  {
    return $this->service->send(resource: 'subscriptions.cancelSubscription', params: ['subscription_id' => $id]);
  }

  public function updateSubscriptionCard(string $id, array $payload)
  {
    return $this->service->send(resource: 'subscriptions.editSubscriptionCard', params: ['subscription_id' => $id], payload: $payload);
  }
}