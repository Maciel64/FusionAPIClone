<?php

namespace Tests\Mockings;

use Illuminate\Support\Facades\Http;

class BackendMocking
{
    private $baseUrl;

    public function __construct()
    {
      $this->exampleMocking();
    }

    public function exampleMocking()
    {
      Http::fake([
        'github.com/*' => Http::response(['foo' => 'bar'], 200),
      ]);
    }
}