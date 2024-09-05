<?php

use App\Repositories\AppointmentRepository;
use App\Repositories\PlanRepository;
use App\Repositories\RoomRepository;
use App\Repositories\UserRepository;
use App\Services\BillingService;
use App\Services\CacheService;
use App\Services\PagarmeService;
use App\Services\ScheduleService;
use App\Services\SendGridService;
use App\Services\UserService;

/*
|--------------------------------------------------------------------------
| Facades to register in AppServiceProvider
|--------------------------------------------------------------------------
|
| This file is for storing the facades to register in AppServiceProvider.
|
*/

return [
  'UserService'           => UserService::class,
  'SendGridFacade'        => SendGridService::class,
  'PagarmeFacade'         => PagarmeService::class,
  'ScheduleServiceFacade' => ScheduleService::class,
  'RoomFacade'            => RoomRepository::class,
  'AppointmentFacade'     => AppointmentRepository::class,
  'BillingFacade'         => BillingService::class,
  'UserRepositoryFacade'  => UserRepository::class,
  'PlanRepositoryFacade'  => PlanRepository::class,
  'CacheFacade'           => CacheService::class
];