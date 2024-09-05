<?php

namespace App\Console;

use App\Facades\BillingFacade;
use App\Models\Billing;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {

      switch (config('settings.billing_type')) {
        case "daily":
          $schedule->command("billing:fail appointment")->dailyAt('1:00');  
          $schedule->command("billing:fail appointment")->dailyAt('2:00');  
          $schedule->command("billing:fail appointment")->dailyAt('3:00');  
          break;
        
        case "monthly":
          $schedule->command("billing:fail appointment")->monthlyOn(2, '01:00');
          $schedule->command("billing:fail appointment")->monthlyOn(3, '01:00');
          $schedule->command("billing:fail appointment")->monthlyOn(4, '01:00');
          break;
      }
      
      $schedule->command("billing:fail subscription")->monthlyOn(2, '2:30');
      $schedule->command("billing:fail subscription")->monthlyOn(3, '2:30');
      $schedule->command("billing:fail subscription")->monthlyOn(4, '2:30');

      $schedule->command("generate:orders appointment")->dailyAt('1:40');
      $schedule->command("generate:orders subscription")->monthlyOn(1, '3:40');
      $schedule->command('generate:transfers')->dailyAt('04:00');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
