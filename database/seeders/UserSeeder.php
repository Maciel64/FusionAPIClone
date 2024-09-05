<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      $user = User::factory()->create([
        'name' => 'Admin',
        'email' => 'admin@fusion.com'
      ]);

      $user->assignRole('owner');
      $user->assignRole('admin');

      $user->email_verified_at    = now();
      $user->account_active       = true;
      $user->account_activated_at = now();
      $user->save();
    }
}
