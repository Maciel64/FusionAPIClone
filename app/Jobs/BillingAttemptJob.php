<?php

namespace App\Jobs;

use App\Facades\BillingFacade;
use App\Models\Appointment;
use App\Models\Subscription;
use App\Services\BillingFailService;
use App\Services\BillingService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class BillingAttemptJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $billingFailId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(int $billingFailId, string $modelType)
    {
      switch($modelType){
        case Subscription::class:
          $this->onQueue('billing-attempt-subscription');
          break;
        case Appointment::class:
          $this->onQueue('billing-attempt-appointment');
          break;
      }

      $this->billingFailId = $billingFailId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
      $billingFailService = new BillingFailService();
      $billingFail        = $billingFailService->find($this->billingFailId);

      $billingService     = new BillingService();
      $billingService->generateOrderByAttempt($billingFail);
    }
}
