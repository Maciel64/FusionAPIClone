<?php

return [
  'base_url' => env('VIACEP_BASE_URL', 'https://viacep.com.br/'),
  'endpoints' => [
    'address' => [
      'endpoint' => '/ws/{zip_code}/json/',
      'method' => 'GET',
    ]
  ],
];