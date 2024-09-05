<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Http;
use Tests\Mockings\BackendMocking;
use Tests\TestCase;

class MockTest extends TestCase
{


  public function setUp(): void
  {
    parent::setUp();
    new BackendMocking();
  }

  public function test_run_example_mocking()
  {
    $this->assertTrue(true);
  }
}
