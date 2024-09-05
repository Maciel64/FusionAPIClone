<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Notifications\InadimplencyNotification;
use Illuminate\Console\Command;

class Notification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notify:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
      $user = User::where('email','lf.system@outlook.com')->first();
      if(!$user) {
        $user = User::factory()->create([
          'name' => 'Luiz L. Lima',
          'email' => 'lf.system@outlook.com',
          'password' => bcrypt('12345678'),
        ]);
      }

      $user->notify(new InadimplencyNotification($user));

      $this->info('Notification sent!');
      return Command::SUCCESS;
    }
}
