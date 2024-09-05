<?php

namespace Tests\Feature;

use App\Models\Plan;
use Tests\Mockings\PagarmeMocking;
use Tests\TestCase;

class PlanTest extends TestCase
{

  private PagarmeMocking $mock; 

  public function setUp(): void
  {
    parent::setUp();
    $this->mock = new PagarmeMocking();
  }

  public function test_index()
  {
    $this->loginWithOwner();

    Plan::factory()->count(3)->create();
    $response = $this->get(route('plan.index'));
    $response->assertStatus(200);
  }

  public function test_store()
  {
    $this->mock->createPlan();
    $this->loginWithOwner();
    $payload = [
      'name' => 'Test Plan',
      'price' => 100,
      'description' => 'Test Plan Description'
    ];

    $response = $this->post(route('plan.store'), $payload);
    $response->assertStatus(200);
  }

  public function test_update()
  {
    $this->mock->updatePlan();
    $this->loginWithOwner();
    $plan = Plan::factory()->create();

    $payload = [
      'name' => 'Test Plan Updated',
      'price' => 100,
      'description' => 'Test Plan Description'
    ];

    $params = [
      'uuid' => $plan->uuid
    ];

    $response = $this->put(route('plan.update', $params), $payload);
    $response->assertStatus(200);
  }

  public function test_show()
  {
    $this->loginWithOwner();
    $plan = Plan::factory()->create();

    $params = ['uuid' => $plan->uuid];
    $response = $this->get(route('plan.show', $params));
    $response->assertStatus(200);
  }

  public function test_destroy()
  {
    $this->loginWithOwner();
    $plan = Plan::factory()->create();
    $params = ['uuid' => $plan->uuid];
    $response = $this->delete(route('plan.destroy', $params));
    $response->assertStatus(200);
  }

}
