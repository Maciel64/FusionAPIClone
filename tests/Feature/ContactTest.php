<?php

namespace Tests\Feature;

use App\Models\Contact;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ContactTest extends TestCase
{

  public function setUp(): void
  {
    parent::setUp();
  }

  public function test_store_contact()
  {
    $this->withoutExceptionHandling();
    $coworking = $this->loginWithPartner();

    $payload = [
      'resource_type' => 'coworking',
      'resource_uuid' => $coworking->uuid,
      'type'          => 'mobile_phone',
      'country_code'  => '55',
      'area_code'     => '11',
      'number'        => '999999999',
    ];

    $response = $this->post(route('contact.store'), $payload);
    $response->assertStatus(200);
    $this->assertNotEmpty($coworking->fresh()->contacts);
    $this->assertCount(1, $coworking->fresh()->contacts);

  }

  public function test_update_contact()
  {
    $this->withoutExceptionHandling();
    $coworking = $this->loginWithPartner();
    $contact = Contact::factory()->create([
      'model_id'   => $coworking->id,
      'model_type' => Coworking::class,
    ]);

    $payload = [
      'type'          => 'mobile_phone',
      'country_code'  => '55',
      'area_code'     => '19',
      'number'        => '999583179',
    ];

    $params = [
      'uuid' => $contact->uuid,
    ];

    $response = $this->put(route('contact.update', $params), $payload);
    $response->assertStatus(200);
    $this->assertEquals($payload['area_code'], $contact->fresh()->area_code);
  }

  public function test_delete_contact()
  {
    $this->withoutExceptionHandling();
    $coworking = $this->loginWithPartner();
    $contact = Contact::factory()->create([
      'model_id'   => $coworking->id,
      'model_type' => get_class($coworking),
    ]);

    $params = [
      'uuid' => $contact->uuid,
    ];

    $response = $this->delete(route('contact.destroy', $params));
    $response->assertStatus(200);
    $this->assertCount(0, $coworking->fresh()->contacts);
  }

  public function test_show_contact()
  {
    $this->withoutExceptionHandling();
    $coworking = $this->loginWithPartner();
    $contact = Contact::factory()->create([
      'model_id'   => $coworking->id,
      'model_type' => get_class($coworking),
    ]);

    $params = [
      'uuid' => $contact->uuid,
    ];

    $response = $this->get(route('contact.show', $params));
    $response->assertStatus(200);
    $response->assertJsonFragment([
      'uuid' => $contact->uuid,
    ]);

    $this->assertCount(1, $coworking->fresh()->contacts);
  }
}
