<?php

namespace App\Repositories;

use App\Models\BillingFailAttempt;
use App\Repositories\BaseRepository;

class BillingFailAttemptsRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(BillingFailAttempt::class);
    }
}