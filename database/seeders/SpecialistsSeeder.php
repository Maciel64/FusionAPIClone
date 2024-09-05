<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\Contact;
use App\Models\HealthAdviceHasUser;
use App\Models\Photo;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use stdClass;

class SpecialistsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      $specialist = new stdClass();
      $specialist->name = 'Dr. José Carlos';
      $specialist->email = 'jose.carlos@fusion.com';
      $specialist->role = 'customer';
      $specialist->img = 'https://images.unsplash.com/photo-1463453091185-61582044d556?ixlib=rb-=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=8&w=1024&h=1024&q=80';
      $this->handler($specialist);

      $specialist->name = 'Dra. Luiza Maria';
      $specialist->email = 'luiza.maria@fusion.com';
      $specialist->role = 'customer';
      $specialist->img = 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80';
      $this->handler($specialist);

      $specialist->name = 'Dra. Laura Willians';
      $specialist->email = 'laura.willians@fusion.com';
      $specialist->role = 'customer';
      $specialist->img = 'https://images.unsplash.com/photo-1501031170107-cfd33f0cbdcc?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80';
      $this->handler($specialist);
    }

    public function handler($specialist)
    {
      $user = User::factory()->create([
        'name'   => $specialist->name,
        'email'  => $specialist->email,
        'status' => 'adimplente'
      ]);

      $this->handlerPhoto($user, $specialist->img);
      $this->handlerAddress($user);
      $this->handlerContacts($user);

      $user->assignRole($specialist->role);

      $user->email_verified_at    = now();
      $user->account_active       = true;
      $user->account_activated_at = now();
      $user->save();
    }

    public function handlerPhoto($user, $img)
    {
      Photo::factory()->create([
        'name'       => 'Admin',
        'url'        => $img,
        'model_type' => User::class,
        'model_id'   => $user->id
      ]);
    }

    public function handlerAddress($user)
    {
      Address::factory()->create([
        'model_type' => User::class,
        'model_id'   => $user->id
      ]);
    }

    public function handlerContacts($user)
    {
      Contact::factory()->create([
        'model_type' => User::class,
        'model_id'   => $user->id
      ]);
    }

    public function handleHealthAdvice($user)
    {
      HealthAdviceHasUser::factory()->create([
        'model_type' => User::class,
        'model_id'   => $user->id
      ]);
    }
}
