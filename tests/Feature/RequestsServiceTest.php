<?php

namespace Tests\Feature;

use App\Services\RequestsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Mockings\PagarmeMocking;
use Tests\TestCase;

class RequestsServiceTest extends TestCase
{
  private PagarmeMocking $mocking;

  public function setUp(): void
  {
    parent::setUp();
    $this->mocking = new PagarmeMocking();
  }

  public function test_get_endpoint()
  {
    $service = new RequestsService('pagarme');
    $this->assertEquals($service->endpoint('orders.listOrders'), 'https://api.pagar.me/core/v5/orders');
  }

  public function test_send_request_to_get_orders_of_pagarme()
  {

    $this->mocking->getOrders();
    
    $headers = [
      'Authorization' => 'Basic '.base64_encode(config('pagarme.secrete_key').':'),
    ];

    $resource = 'orders.listOrders';
    $service = new RequestsService('pagarme');
    $service->setHeaders($headers);
    $response = $service->send($resource);
    $this->assertEquals($service->status, 200);
    $this->assertEquals($response->json('data.0.id'), 'or_28dN9w7CLU79kDjL');
  }
}
