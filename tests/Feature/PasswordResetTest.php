<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
  use RefreshDatabase;

  public function test_reset_password_link_can_be_requested(): void
  {
      $this->withExceptionHandling();
      Notification::fake();

      $user = User::factory()->create();

      $response = $this->post(route(route('password.reset')), ['email' => $user->email]);
      Notification::assertSentTo($user, ResetPassword::class);
  }

  public function test_password_can_be_reset_with_valid_token(): void
  {
      $this->withExceptionHandling();
      Notification::fake();

      $user = User::factory()->create();

      $this->post(route(route('password.reset')), ['email' => $user->email]);

      Notification::assertSentTo($user, ResetPassword::class, function (object $notification) use ($user) {
          $response = $this->post(route('reset-password'), [
              'token' => $notification->token,
              'email' => $user->email,
              'password' => 'password',
              'password_confirmation' => 'password',
          ]);
          $response->assertSessionHasNoErrors();
          return true;
      });
  }
}
