<?php

namespace Tests\Feature;

use App\Models\BillingFail;
use App\Models\BillingFailAttempt;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class BillingFailTest extends TestCase
{
  public function setUp(): void
  {
    parent::setUp();
  }

  public function test_factory_create_a_billing_fail()
  {
    $this->withoutExceptionHandling();
    $billingFail = BillingFail::factory()->create();
    $this->assertNotNull($billingFail);
  }

  public function test_factory_create_a_billing_fail_attempt()
  {
    $this->withoutExceptionHandling();
    $billingFail = BillingFail::factory()->create();
    $this->assertNotNull($billingFail);

    $billingFailAttempt = BillingFailAttempt::factory()->create([
      'billing_fail_id' => $billingFail->id,
      'status'          => 'failed',
    ]);

    $this->assertNotNull($billingFailAttempt);
    $this->assertEquals($billingFailAttempt->billing_fail_id, $billingFail->id);
  }
}
