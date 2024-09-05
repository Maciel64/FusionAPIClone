<?php

namespace Tests\Feature;

use App\Models\Coworking;
use App\Models\Room;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PhotoTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_when_user_add_photo_profile()
    {
      $this->withoutExceptionHandling();
      $this->login();
      $payload = [
        'type' => 'avatar',
        'uuid' => Auth::user()->uuid,
        'photo' => UploadedFile::fake()->image('avatar.jpg'),
      ];

      $response = $this->post(route('photo.store'), $payload);
      $response->assertStatus(200);
      $response->assertJsonStructure(['data' => ['uuid','name','url',]]);
      $this->assertEquals(__('response.store.success', ['resource' => 'Photo']), $response->json('message'));
      $this->assertTrue(Storage::disk('avatar')->exists($response->json('data.name')));
    }

    public function test_when_user_send_a_new_photo_profile()
    {
      $this->withoutExceptionHandling();
      $this->login();

      $payload = [
        'type' => 'avatar',
        'uuid' => Auth::user()->uuid,
        'photo' => UploadedFile::fake()->image('avatar.jpg'),
      ];

      $this->post(route('photo.store'), $payload);
      // $file = file_get_contents('https://avatar.dicebear.com/api/bottts/'.Auth::user()->uuid.'.jpg?background=black');
      // $file = UploadedFile::fake()->create('avatar.jpg', $file);

      $payload['photo'] = UploadedFile::fake()->image('avatar.jpg');
      $response = $this->post(route('photo.store'), $payload);
      $response->assertStatus(200);
      $response->assertJsonStructure(['data' => ['uuid','name','url']]);

      $this->assertEquals(__('response.store.success', ['resource' => 'Photo']), $response->json('message'));

      $this->assertTrue(Storage::disk('avatar')->exists($response->json('data.name')));
      $this->assertEquals(1, auth()->user()->fresh()->photos()->get()->count());
    }
    
    public function test_when_user_get_photo_profile()
    {
      $this->login();
      $payload = [
        'type' => 'avatar',
        'uuid' => Auth::user()->uuid,
        'photo' => UploadedFile::fake()->image('avatar.jpg'),
      ];
      $file =  UploadedFile::fake()->image('avatar.jpeg');
      $this->post(route('photo.store'), $payload);
      $response = $this->get(route('photo.show', ['uuid' => Auth::user()->photos()->first()->uuid]));
      $response->assertStatus(200);
      $response->assertJsonStructure(['data' => ['uuid','name','url',]]);
      $this->assertEquals(__('response.show.success', ['resource' => 'Photo']), $response->json('message'));
    }

    public function test_when_user_destroy_photo_profile()
    {
      $this->login();

      $payload = [
        'uuid'  => Auth::user()->uuid,
        'type'  => 'avatar',
        'photo' => UploadedFile::fake()->image('avatar.jpeg')
      ];

      $photo = $this->post(route('photo.store'), $payload);
      $params = [
        'uuid' => Auth::user()->photos()->first()->uuid,
        'type' => 'avatar'
      ];

      $response = $this->delete(route('photo.destroy', $params));
      $response->assertStatus(200);
      $this->assertEquals(__('response.destroy.success', ['resource' => 'Photo']), $response->json('message'));
      $this->assertFalse(Storage::disk('avatar')->exists($photo->json('data.name')));
    }


    public function test_when_add_a_photos_to_coworking()
    {
      $this->withoutExceptionHandling();
      $this->loginWithAdmin();
      
      $coworking = Coworking::factory()->create([
        'user_id' => auth()->user()->id,
      ]);

      $payload = [
        'uuid' => $coworking->uuid,
        'type' => 'coworking',
        'photos' => [
          UploadedFile::fake()->image('coworking-1.jpeg'),
          UploadedFile::fake()->image('coworking-2.jpeg'),
          UploadedFile::fake()->image('coworking-3.jpeg'),
        ]
      ];

      $response = $this->post(route('photo.bulk.store'), $payload);
      $response->assertStatus(200);
      $this->assertEquals(__('response.store.success', ['resource' => 'Photo']), $response->json('message'));
    }

    public function test_when_a_partner_add_a_photos_to_room()
    {
      $this->withoutExceptionHandling();
      $coworking = $this->loginWithPartner();
      $room = Room::factory()->create([
        'coworking_id' => $coworking->id        
      ]);

      $payload = [
        'uuid' => $room->uuid,
        'type' => 'room',
        'photos' => [
          UploadedFile::fake()->image('room-1.jpeg'),
          UploadedFile::fake()->image('room-2.jpeg'),
          UploadedFile::fake()->image('room-3.jpeg'),
        ]
      ];

      $response = $this->post(route('photo.bulk.store'), $payload);
      $response->assertStatus(200);
      $this->assertEquals(__('response.store.success', ['resource' => 'Photo']), $response->json('message'));

      $room = $room->fresh();
      $this->assertEquals(3, $room->photos->count());
    }
}
