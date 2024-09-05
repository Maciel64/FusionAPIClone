<?php

namespace Tests\Feature;

use App\Models\Contact;
use App\Models\Coworking;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class CoworkingTest extends TestCase
{

  use RefreshDatabase;

  public function setUp(): void
  {
    parent::setUp();
  }

  public function test_when_create_a_coworking()
  {
    $this->withoutExceptionHandling();
    $this->loginWithAdmin();
    $payload = [
      'name' => 'Coworking Test',
      'description' => 'Coworking Test',
    ];

    $params = [
      'user_uuid' => auth()->user()->uuid, 
    ];

    $response = $this->post(route('coworking.store', $params), $payload);
    $response->assertStatus(200);
    $this->assertEquals(__('response.store.success', ['resource' => 'Coworking']), $response->json('message'));
  }
  
  public function test_when_create_a_coworking_with_contacts()
  {
    $this->withoutExceptionHandling();
    $coworking = $this->loginWithPartner();
    $contact = Contact::factory()->create([
      'model_id'   => $coworking->id,
      'model_type' => Coworking::class,
    ]);

    $this->assertCount(1, $coworking->contacts);
    $this->assertEquals($contact->id, $coworking->contacts->first()->id);
  }

  public function test_when_update_a_coworking()
  {
    $this->withoutExceptionHandling();
    $this->loginWithAdmin();
    
    $payload = [
      'name' => 'Coworking Test Updated',
      'description' => 'Coworking Test Updated',
    ];
    
    $coworking = Coworking::factory()->create([
      'user_id' => auth()->user()->id,
    ]);

    $params = [
      'user_uuid' => auth()->user()->uuid, 
      'uuid' => $coworking->uuid,
    ];

    $response = $this->put(route('coworking.update', $params), $payload);
    $response->assertStatus(200);
    $this->assertEquals(__('response.update.success', ['resource' => 'Coworking']), $response->json('message'));
  }

  public function test_when_get_a_specific_coworking()
  {
    $this->withoutExceptionHandling();
    $this->loginWithAdmin();
    
    $coworking = Coworking::factory()->create([
      'user_id' => auth()->user()->id,
    ]);

    $params = [
      'user_uuid' => auth()->user()->uuid, 
      'uuid' => $coworking->uuid,
    ];

    $response = $this->get(route('coworking.show', $params));
    $response->assertStatus(200);
    $this->assertEquals(__('response.show.success', ['resource' => 'Coworking']), $response->json('message'));
  }

  public function test_when_get_all_coworkings_by_user()
  {
    $this->withoutExceptionHandling();
    $this->loginWithAdmin();
    
    Coworking::factory(2)->create([
      'user_id' => auth()->user()->id
    ]);

    $params = [
      'user_uuid' => auth()->user()->uuid, 
    ];

    $response = $this->get(route('coworking.index', $params));
    $response->assertStatus(200);
    $this->assertEquals(__('response.list.success', ['resource' => 'Coworking']), $response->json('message'));
    $this->assertCount(2, $response->json('data'));
  }

  public function test_when_remove_a_specific_coworking()
  {
    $this->withoutExceptionHandling();
    $this->loginWithAdmin();
    
    $coworking = Coworking::factory()->create([
      'user_id' => auth()->user()->id,
    ]);

    $params = [
      'user_uuid' => auth()->user()->uuid, 
      'uuid' => $coworking->uuid,
    ];

    $response = $this->delete(route('coworking.destroy', $params));

    $response->assertStatus(200);
    $this->assertEquals(__('response.destroy.success', ['resource' => 'Coworking']), $response->json('message'));
  }


  
}
