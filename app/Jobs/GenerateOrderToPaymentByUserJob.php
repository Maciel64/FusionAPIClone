<?php

namespace App\Jobs;

use App\Facades\BillingFacade;
use App\Services\BillingService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateOrderToPaymentByUserJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected int $userId;
    protected string $model;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(int $userId, string $model)
    {
      $this->onQueue('payment-order');
      $this->userId = $userId;
      $this->model = $model;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
      $service = new BillingService();
      $service->generateOrderToPayment($this->userId, $this->model);
    }
}
