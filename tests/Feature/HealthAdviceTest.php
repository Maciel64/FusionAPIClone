<?php

namespace Tests\Feature;

use App\Models\HealthAdvice;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class HealthAdviceTest extends TestCase
{

  use RefreshDatabase;

  public function setUp(): void
  {
    parent::setUp();
  }

  public function test_index()
  {
    $this->loginWithAdmin();
    $response = $this->get('api/health-advice');
    $response->assertStatus(200);

  }

  public function test_store()
  {
    $this->loginWithAdmin();

    $payload = [
      'initials' => 'CRM',
      'name' => 'Cuidados com a saúde',
    ];

    $response = $this->post(route('health-advice.store'), $payload);
    $response->assertStatus(200);
  }

  public function test_show()
  {
    $this->loginWithAdmin();

    $advice = HealthAdvice::factory()->create([
      'initials' => 'CRM',
      'name' => 'Cuidados com a saúde',
    ]);

    $params = [
      'uuid' => $advice->uuid,
    ];

    $response = $this->get(route('health-advice.show', $params));
    $response->assertStatus(200);
  }

  public function test_update()
  {
    $this->loginWithAdmin();

    $advice = HealthAdvice::factory()->create([
      'initials' => 'CRM',
      'name' => 'Cuidados com a saúde',
    ]);

    $payload = [
      'initials' => 'CRM',
      'name' => 'Cuidados com a saúde updated',
    ];

    $params = [
      'uuid' => $advice->uuid,
    ];

    $response = $this->put(route('health-advice.update', $params), $payload);
    $response->assertStatus(200);
  }

  public function test_destroy()
  {
    $this->loginWithAdmin();

    $advice = HealthAdvice::factory()->create([
      'initials' => 'CRM',
      'name' => 'Cuidados com a saúde',
    ]);

    $params = [
      'uuid' => $advice->uuid,
    ];

    $response = $this->delete(route('health-advice.destroy', $params));
    $response->assertStatus(200);

    $this->assertDatabaseMissing('health_advice', [
      'uuid' => $advice->uuid,
    ]);
  }

}
