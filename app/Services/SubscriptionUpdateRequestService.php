<?php

namespace App\Services;

use App\Repositories\SubscriptionUpdateRequestRepository;

class SubscriptionUpdateRequestService
{
    public function store(array $data)
    {
        $repository = new SubscriptionUpdateRequestRepository();
        return $repository->create($data);
    }

    public function update($id, array $data)
    {
      $repository = new SubscriptionUpdateRequestRepository();
      return $repository->update($data, $id);
    }
}