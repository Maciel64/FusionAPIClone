<?php

namespace App\Console\Commands;

use App\Jobs\BillingAttemptJob;
use App\Models\Appointment;
use App\Models\Subscription;
use App\Services\BillingFailService;
use App\Services\BillingService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class BillingFailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'billing:fail {modelType}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Comando para verificar se há cobranças pendentes';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
      $modelType = $this->getModelTypeByArgument();
      $billingFailService = new BillingFailService();
      $billingsFail       = $billingFailService->getFailsByModelType($modelType);

      foreach ($billingsFail as $billingFail) {
        BillingAttemptJob::dispatch($billingFail->id, $modelType);
      }
    }

    public function getModelTypeByArgument()
    {
      $modelType = strtolower($this->argument('modelType'));
      switch($modelType){
        case 'subscription':
          return Subscription::class;
          break;
        case 'appointment':
          return Appointment::class;
          break;
      }
    }

}
