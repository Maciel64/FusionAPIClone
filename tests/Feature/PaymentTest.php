<?php

namespace Tests\Feature;

use App\Models\Plan;
use App\Services\PaymentServices;
use App\Services\PlanService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Tests\Mockings\PagarmeMocking;
use Tests\TestCase;

class PaymentTest extends TestCase
{
  use refreshDatabase;
  
  private PagarmeMocking $mock;

  public function setUp(): void
  {
    parent::setUp();
    $this->mock = new PagarmeMocking();
  }


}
