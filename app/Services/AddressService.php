<?php

namespace App\Services;

use App\Facades\CacheFacade;
use App\Models\User;
use App\Models\Address;
use App\Services\UserService;
use App\Repositories\AddressRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Cache;

class AddressService
{

  public function updateAddress($data, string $address_uuid)
  {
    $repository = new AddressRepository();
    $response = $repository->updateByUuid($address_uuid, $data);
    $addressRepository = $repository->findByUuid($address_uuid);
    //colocar if para customer
    $userRepository = new UserRepository();
    $userService = new UserService();
    if ($addressRepository->model_type == 'App\Models\User'){
      $userRepository = $userRepository->find($addressRepository->model_id);
      $responseAddress = $userService->customerUpdateInPagarme($userRepository);
      // dd($responseAddress);
    }
    
    
    return $response;
  }



  
  public function store(User $user)
  {
    if ($user->hasRole('customer')) {
      
    }
  }

  public function findAddressByZipCode(string $zip_code)
  {
    if(!$this->validateZipCode($zip_code)) return false;
    return Cache::remember(CacheFacade::keyGen(Address::class, $zip_code), config('cache.expires'), function() use($zip_code){
      $service = new RequestsService('viacep');
      $response = $service->send('address', ['zip_code' => $zip_code]);
      if ($response->status() < 204 and $response->status() >= 200) {
        $data = (object) $response->json();
        $address = new Address();
        $address->line_1 = $data->logradouro . ' - ' . $data->bairro;
        $address->line_2 = $data->complemento;
        $address->city = $data->localidade;
        $address->state = $data->uf;
        $address->country = 'Brasil';
        $address->zip_code = $data->cep;
        return $address;
      }
      return [];
    });
  }

  public function validateZipCode(string $zip_code)
  {
    return preg_match('/^[0-9]{5}-[0-9]{3}$/', $zip_code) or preg_match('/^[0-9]{8}$/', $zip_code); 
  }
}