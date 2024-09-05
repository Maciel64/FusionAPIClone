<?php

namespace App\Services;

use App\Facades\PagarmeFacade;
use App\Models\Card;
use App\Repositories\CardRepository;
use App\Repositories\UserRepository;
use Exception;
use Illuminate\Validation\ValidationException;

class CardService
{

  public function defaultConfiguration(string $userUuid, string $cardUuid){
    $card = (new CardRepository())->findByUuid($cardUuid);
    $repository = new CardRepository();
    $cards = $repository->getAllCardsByCustomer($userUuid);
    foreach($cards as $userCard){
      if($userCard->uuid != $cardUuid){
        $userCard->is_default = 0;
        $userCard->save();
      }
    }
    $card->is_default = 1;
    $card->save();

    return $card;
  }

  public function store(array $data, string $customerUuid)
  {
    $customer = (new UserRepository())->findByUuid($customerUuid);
    // $this->assembleBillingAddressData($data, $card);
    $card = (new PagarmeService())->createCard($customer->id, $customer->customer_id, $data);

    //Configuração de card como principal
    if($data['is_default'] == true){
      $this->defaultConfiguration($customerUuid, $card->uuid);
    }

    return ($card)? $card->fresh() : false;
  }

  public function update($cardUuid, $data)
  {
    $card = (new CardRepository())->findByUuid($cardUuid);
    $this->assembleBillingAddressData($data, $card);
    if((new PagarmeService())->updateCard($card, $data))
      return $this->updateCardInSubscription($card)? $card->fresh() : false;
    return false;
  }

  public function updateCardInSubscription(Card $card)
  {
    $user = (new UserRepository())->find($card->user_id);
    $subscription = $user->subscription;
    if($subscription)
      return (new SubscriptionService())->updateCard($subscription->uuid, $card->uuid);
    return true;
  }

  public function assembleBillingAddressData(array &$data, $card)
  {
    if(!$data['billing_address_is_different']){
      if(!$card->address_id)
         throw ValidationException::withMessages(['billing_address' => 'The Address is required']);

        $data['billing_address_id'] = $card->address_id;
        unset($data['billing_address_is_different'], $data['billing_address']);
    }

    if(isset($data['billing_address_is_different'])) {
      unset($data['billing_address_is_different']);
    }
  }

  public function destroy(string $cardUuid)
  {
    $repository = new CardRepository();
    $card = $repository->findByUuid($cardUuid);
    if($card->is_default == 1){
      throw new Exception("O cartão principal não pode ser excluído.");
    }
    $response = (new PagarmeService())->deleteCard($cardUuid);
    if($response->status() === 200 && ($response->json('status') === 'deleted')) {
      $card->delete();
      return true;
    }
    // dd($response);
    throw new Exception("Erro ao deletar o cartão.");
  }

  public function generateTokenCard($userId, $data)
  {
    $payload = ['type' => 'card','card' => $data];
    $response = PagarmeFacade::createTokenCard($userId, $payload);
    if($response->status() != 200)
      throw new Exception("Error to create token card");
    return true;
  }

  public function getAddressByUser(&$data)
  {
    $user = auth()->user();
    $address = $user->address;
    $data['billing_address'] = [
      'line_1'   => $address->number.', '.$address->street,
      'line_2'   => $address->complement,
      'city'     => $address->city,
      'state'    => $address->state,
      'country'  => 'BR',
      'zip_code' => $address->zip_code,
    ];
  }

}