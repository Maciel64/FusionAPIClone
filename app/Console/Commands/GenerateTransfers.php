<?php

namespace App\Console\Commands;

use App\Services\TransferService;
use Illuminate\Console\Command;

class GenerateTransfers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:transfers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate transfers to all partners from orders paid';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
      $transferService = new TransferService();
      $result = $transferService->generateTransfers();
    }
}
