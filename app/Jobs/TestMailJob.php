<?php

namespace App\Jobs;

use App\Facades\SendGridFacade;
use App\Mail\SendgridTestMail;
use App\Services\SendGridService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use SendGrid\Mail\Mail;

class TestMailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $data;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $data)
    {
      $this->onQueue('mail');
      $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
      // new job

    }
}
