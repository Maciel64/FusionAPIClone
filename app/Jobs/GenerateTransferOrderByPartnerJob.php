<?php

namespace App\Jobs;

use App\Services\TransferService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateTransferOrderByPartnerJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected int $partnerId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(int $partnerId)
    {
      $this->onQueue('transfer-order');
      $this->partnerId = $partnerId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
      $transferService = new TransferService();
      $transferService->generateTransferOrderByPartner($this->partnerId);
    }
}
