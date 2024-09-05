<?php

namespace App\Console\Commands;

use App\Facades\BillingFacade;
use App\Models\Billing;
use App\Services\BillingService;
use Illuminate\Console\Command;

class GenerateOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:orders {modelType}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate orders to payment to all customers';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
      $modelType = $this->getModelTypeByArgument();
      $billingService = new BillingService();
      $billingService->generateOrderListToPayment($modelType);
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
