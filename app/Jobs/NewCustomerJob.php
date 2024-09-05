<?php

namespace App\Jobs;

use App\Facades\SendGridFacade;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class NewCustomerJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private int $userId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $userId)
    {
      $this->userId = $userId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
      $user = User::find($this->userId);
      SendGridFacade::send($user->email, 'New Customer', 'emails.user.new-customer', $user->name);
    }
}
