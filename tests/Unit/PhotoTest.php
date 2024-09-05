<?php

namespace Tests\Unit;

use App\Models\Photo;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class PhotoTest extends TestCase
{
    /** @test */
    public function when_create_a_photo_related_with_user()
    {
      $this->withoutExceptionHandling();
      $user = User::factory()->create();
      $photo = Photo::factory()->create([
        'model_id' => $user->id,
        'model_type' => User::class
      ]);
      $photoUser = $user->photos()->first();
      $this->assertEquals($photo->id, $photoUser->id);
    }

    /** @test */
    public function when_deleting_a_user_related_to_the_photo_and_verifying_that_the_relationship_was_actually_removed()
    {
      $this->withoutExceptionHandling();
      $user = User::factory()->create();
      $photo = Photo::factory()->create([
          'model_id' => $user->id,
          'model_type' => User::class
      ]);
      $photoUser = $user->photos()->first();
      $this->assertEquals($photo->id, $photoUser->id);
      $user->delete();
    }

    /** @test */
    public function when_deleting_a_photo_related_to_the_user_and_verifying_that_the_relationship_was_actually_removed()
    {
      $this->withoutExceptionHandling();
      $user = User::factory()->create();
      $photo = Photo::factory()->create([
        'model_id' => $user->id,
        'model_type' => User::class
      ]);
      $photoUser = $user->photos()->first();
      $this->assertEquals($photo->id, $photoUser->id);
      $photo->delete();
    }
}
