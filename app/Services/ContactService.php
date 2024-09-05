<?php

namespace App\Services;

use App\Repositories\ContactRepository;
use App\Repositories\CoworkingRepository;
use App\Repositories\PatientRepository;
use App\Repositories\UserRepository;

class ContactService
{

  protected $contactRepository;

  public function __construct()
  {
    $this->contactRepository = new ContactRepository();
  }

  public function updateContact($data, $contact_uuid){
    $response = $this->contactRepository->updateByUuid($contact_uuid, $data);

    $contact = $this->contactRepository->findByUuid($contact_uuid);
    

    $userRepository = new UserRepository();
    $userService = new UserService();
    if ($contact->model_type == 'App\Models\User'){
      $userRepository = $userRepository->find($contact->model_id);
      $responseAddress = $userService->customerUpdateInPagarme($userRepository);
    }


    return $response;

  }

  public function store($data)
  {
    $repository = $this->getRepositoryInstance($data['resource_type']);
    $resource = $repository->findByUuid($data['resource_uuid']);
    unset($data['resource_type'], $data['resource_uuid']);
    $data['model_id'] = $resource->id;
    $data['model_type'] = get_class($resource);;
    $contact = $this->contactRepository->create($data);
    return $contact;
  }

  /**
   * Undocumented function
   *
   * @param string $resourceType
   * @return UserRepository|CoworkingRepository
   */
  public function getRepositoryInstance(string $resourceType)
  {
    switch ($resourceType) {
      case 'user':
        return new UserRepository();
        break;
      case 'coworking':
        return new CoworkingRepository();
        break;
      case 'patient':
        return new PatientRepository();
        break;
    }
  }
}