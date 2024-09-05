<?php

namespace Tests\Mockings;

use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PagarmeMocking extends TestCase
{
  private $endpoints = [
    'orders' => [
      'listOrders' => 'https://api.pagar.me/core/v5/orders',
    ],
    'customers' => [
      'createCustomer' => 'https://api.pagar.me/core/v5/customers',
      'getCustomer'    => 'https://api.pagar.me/core/v5/customers/*',
      'listCustomers'  => 'https://api.pagar.me/core/v5/customers',
      'createCard'     => 'https://api.pagar.me/core/v5/customers/*/cards',
      'getCard'        => 'https://api.pagar.me/core/v5/customers/*/cards/*',
      'editPlan'       => 'https://api.pagar.me/core/v5/plans/*',
    ],
    'plans' => [
      'createPlan'     => 'https://api.pagar.me/core/v5/plans',
      'updatePlan'     => 'https://api.pagar.me/core/v5/plans/*',
    ],
    'subscriptions' => [
      'createPlanSubscription'        => 'https://api.pagar.me/core/v5/subscriptions',
      'cancelSubscription'            => 'https://api.pagar.me/core/v5/subscriptions/*',
      'editSubscriptionPaymentMethod' => 'https://api.pagar.me/core/v5/subscriptions/*/payment-method',
      'editSubscriptionCard'          => 'https://api.pagar.me/core/v5/subscriptions/*/card',
    ],
  ];

  public function getOrders()
  {
    $response = [
      "data" => [
        [
          "id" => "or_28dN9w7CLU79kDjL",
          "code" => "62LVFN7I4R",
          "amount" => 2990,
          "currency" => "BRL",
          "closed" => true,
          "items" => [
            [
              "id" => "oi_d478RMAS3bC74PrL",
              "description" => "Chaveiro do Tesseract",
              "amount" => 2990,
              "quantity" => 1,
              "status" => "active",
              "created_at" => "2017-04-19T16:01:09Z",
              "updated_at" => "2017-04-19T16:01:09Z"
            ]
          ],
          "customer" => [
            "id" => "cus_eaEXlZvhBfeGlDOm",
            "name" => "Tony Stark",
            "email" => "f1492621-4f39-45f7-adfb-82a373a0a85c@avengers.com",
            "delinquent" => false,
            "created_at" => "2017-04-19T16:01:09Z",
            "updated_at" => "2017-04-19T16:01:09Z"
          ],
          "status" => "paid",
          "created_at" => "2017-04-19T16:01:09Z",
          "updated_at" => "2017-04-19T16:01:11Z",
          "closed_at" => "2017-04-19T16:01:11Z",
          "charges" => [
            [
              "id" => "ch_gmnW101c9YTvQVLB",
              "code" => "62LVFN7I4R",
              "gateway_id" => "ef5e977b-93d2-485a-b15d-36e5eb3d8cf5",
              "amount" => 2990,
              "status" => "paid",
              "currency" => "BRL",
              "payment_method" => "credit_card",
              "paid_at" => "2017-04-19T16:01:11Z",
              "created_at" => "2017-04-19T16:01:09Z",
              "updated_at" => "2017-04-19T16:01:09Z",
              "customer" => [
                "id" => "cus_eaEXlZvhBfeGlDOm",
                "name" => "Tony Stark",
                "email" => "f1492621-4f39-45f7-adfb-82a373a0a85c@avengers.com",
                "delinquent" => false,
                "created_at" => "2017-04-19T16:01:09Z",
                "updated_at" => "2017-04-19T16:01:09Z"
              ]
            ]
          ]
        ],
        [
          "id" => "or_dW6vZoJfLhw3Rb10",
          "code" => "65JGU05FX0",
          "amount" => 2990,
          "currency" => "BRL",
          "closed" => true,
          "items" => [
            [
              "id" => "oi_zYGxV8rU36HPWQMg",
              "description" => "Chaveiro do Tesseract",
              "amount" => 2990,
              "quantity" => 1,
              "status" => "active",
              "created_at" => "2017-04-19T15:58:23Z",
              "updated_at" => "2017-04-19T15:58:23Z"
            ]
          ],
          "customer" => [
            "id" => "cus_rG3592i17uQgxK2Q",
            "name" => "Tony Stark",
            "email" => "5d76b138-2963-40f7-ba71-c9ae2cc65518@avengers.com",
            "delinquent" => false,
            "created_at" => "2017-04-19T15:58:23Z",
            "updated_at" => "2017-04-19T15:58:23Z"
          ],
          "status" => "failed",
          "created_at" => "2017-04-19T15:58:23Z",
          "updated_at" => "2017-04-19T15:58:24Z",
          "closed_at" => "2017-04-19T15:58:24Z",
          "charges" => [
            [
              "id" => "ch_nM5PkjcyLUa6Nr1w",
              "code" => "65JGU05FX0",
              "amount" => 2990,
              "status" => "failed",
              "currency" => "BRL",
              "payment_method" => "credit_card",
              "created_at" => "2017-04-19T15:58:23Z",
              "updated_at" => "2017-04-19T15:58:24Z",
              "customer" => [
                "id" => "cus_rG3592i17uQgxK2Q",
                "name" => "Tony Stark",
                "email" => "5d76b138-2963-40f7-ba71-c9ae2cc65518@avengers.com",
                "delinquent" => false,
                "created_at" => "2017-04-19T15:58:23Z",
                "updated_at" => "2017-04-19T15:58:23Z"
              ]
            ]
          ]
        ]
      ],
      "paging" => [
        "total" => 406,
        "next" => "https://api.pagar.me/core/v1/orders?page=2&size=2"
      ]
    ];

    $endpoint = $this->endpoints['orders']['listOrders'];
    Http::fake([$endpoint => Http::response($response, 200)]);
  }

  public function createOrder()
  {
    $response = [
      "id" => "or_28dN9w7CLU79kDjL",
      "code" => "62LVFN7I4R",
      "amount" => 2990,
      "currency" => "BRL",
      "closed" => true,
      "items" => [
        [
          "id" => "oi_d478RMAS3bC74PrL",
          "description" => "Chaveiro do Tesseract",
          "amount" => 2990,
          "quantity" => 1,
          "status" => "active",
          "code" => "abc"
        ]
      ],
      "customer" => [
        "name" => "Tony Stark",
        "email" => "avengerstark@ligadajustica.com.br",
        "document" => "12345678901",
        "type" => "individual",
        "address" => [
          "line_1" => "375, Av. General Justo, Centro",
          "line_2" => "8º andar",
          "zip_code" => "20021130",
          "city" => "Rio de Janeiro",
          "state" => "RJ",
          "country" => "BR"
        ],
        "phones" => [
          "home_phone" => [
            "country_code" => "55",
            "area_code" => "21",
            "number" => "000000000"
          ],
          "mobile_phone" => [
            "country_code" => "55",
            "area_code" => "21",
            "number" => "000000000"
          ]
        ]
      ],
      "shipping" => [
        "amount" => 100,
        "description" => "Stark",
        "recipient_name" => "Tony Stark",
        "recipient_phone" => "24586787867",
        "address" => [
          "line_1" => "10880, Malibu Point, Malibu Central",
          "zip_code" => "90265",
          "city" => "Malibu",
          "state" => "CA",
          "country" => "US"
        ]
      ],
      "status" => "paid",
      "created_at" => "2017-04-19T16:01:09Z",
      "updated_at" => "2017-04-19T16:01:11Z",
      "closed_at" => "2017-04-19T16:01:11Z",
      "charges" => [
        [
          "id" => "ch_gmnW101c9YTvQVLB",
          "code" => "62LVFN7I4R",
          "gateway_id" => "ef5e977b-93d2-485a-b15d-36e5eb3d8cf5",
          "amount" => 2990,
          "status" => "paid",
          "currency" => "BRL",
          "payment_method" => "credit_card",
          "paid_at" => "2017-04-19T16:01:11Z",
          "created_at" => "2017-04-19T16:01:09Z",
          "updated_at" => "2017-04-19T16:01:09Z",
          "customer" => [
            "id" => "cus_eaEXlZvhBfeGlDOm",
            "name" => "Tony Stark",
            "email" => "f1492621-4f39-45f7-adfb-82a373a0a85c@avengers.com",
            "delinquent" => false,
            "created_at" => "2017-04-19T16:01:09Z",
            "updated_at" => "2017-04-19T16:01:09Z"
          ],
          "last_transaction" => [
            "id" => "tran_3RYbBQjcnEcwrGp0",
            "transaction_type" => "credit_card",
            "gateway_id" => "ddab707d-72ba-49f9-b356-f0b9ebfa3039",
            "amount" => 2990,
            "status" => "captured",
            "success" => true,
            "installments" => 1,
            "statement_descriptor" => "AVENGERS",
            "acquirer_name" => "simulator",
            "acquirer_affiliation_code" => "MUNDI",
            "acquirer_tid" => "342793",
            "acquirer_nsu" => "512784",
            "acquirer_auth_code" => "233215",
            "acquirer_message" => "Pagarme|Transação autorizada com sucesso",
            "acquirer_return_code" => "0",
            "operation_type" => "auth_and_capture",
            "card" => [
              "id" => "card_6xk9deAu2I2MdPzJ",
              "first_six_digits" => "542501",
              "last_four_digits" => "8229",
              "brand" => "Amex",
              "holder_name" => "Tony Stark",
              "exp_month" => 1,
              "exp_year" => 2030,
              "status" => "active",
              "created_at" => "2017-04-19T16:01:09Z",
              "updated_at" => "2017-04-19T16:01:09Z",
              "billing_address" => [
                "line_1" => "10880, Malibu Point, Malibu Central",
                "zip_code" => "90265",
                "city" => "Malibu",
                "state" => "CA",
                "country" => "US"
              ],
              "type" => "credit"
            ],
            "created_at" => "2017-04-19T16:01:09Z",
            "updated_at" => "2017-04-19T16:01:09Z",
            "gateway_response" => [
              "code" => "201"
            ],
            "antifraud_response" => [
              "provider_name" => "clearsale",
              "status" => "pending",
              "return_code" => "",
              "return_message" => "",
              "score" => "1"
            ]
          ]
        ]
      ]
    ];

    $endpoint = "https://api.pagar.me/core/v5/orders";
    Http::fake([$endpoint => Http::response($response, 200)]);
  }

  public function createOrderFail()
  {

    $response = [
      "id" => "or_qVXBYKosdUvYmJ1g",
      "code" => "6ad1b3ff-8f66-47d0-b2b9-c665786f1264",
      "amount" => 7866,
      "currency" => "BRL",
      "closed" => true,
      "items" => [
        [
          "id" => "oi_EQ2pqwu12T35Pgy1",
          "type" => "product",
          "description" => "Appointment",
          "amount" => 7350,
          "quantity" => 1,
          "status" => "active",
          "created_at" => "2023-02-08T23:00:27Z",
          "updated_at" => "2023-02-08T23:00:27Z",
          "code" => "3f6e2369-58d2-4228-a17e-89a3afbdeb8c"
        ],
        [
          "id" => "oi_JPqB5lGhxIr6edMn",
          "type" => "product",
          "description" => "Plan",
          "amount" => 516,
          "quantity" => 1,
          "status" => "active",
          "created_at" => "2023-02-08T23:00:27Z",
          "updated_at" => "2023-02-08T23:00:27Z",
          "code" => "7a0d1b1b-fa94-443a-b617-ad962d1fc2ec"
        ]
      ],
      "customer" => [
        "id" => "cus_8Mo7AD5uYt8oKvjV",
        "name" => "Luiz Felipe",
        "email" => "lf.system@outlook.com",
        "code" => "6ad1b3ff-8f66-47d0-b2b9-c665786f1264",
        "document" => "11111111111",
        "document_type" => "cpf",
        "type" => "individual",
        "gender" => "male",
        "delinquent" => false,
        "address" => [
          "id" => "addr_A5WaMGlUwBfNVzDr",
          "line_1" => "Rua 1",
          "line_2" => "test",
          "zip_code" => "12345678",
          "city" => "test",
          "state" => "SP",
          "country" => "BR",
          "status" => "active",
          "created_at" => "2022-12-19T17:13:21Z",
          "updated_at" => "2023-02-08T23:00:26Z"
        ],
        "created_at" => "2022-12-19T17:13:21Z",
        "updated_at" => "2023-02-08T23:00:26Z",
        "phones" => [
          "home_phone" => [
            "country_code" => "55",
            "number" => "999999999",
            "area_code" => "11"
          ],
          "mobile_phone" => [
            "country_code" => "55",
            "number" => "999999999",
            "area_code" => "11"
          ]
        ]
      ],
      "status" => "failed",
      "created_at" => "2023-02-08T23:00:27Z",
      "updated_at" => "2023-02-08T23:00:28Z",
      "closed_at" => "2023-02-08T23:00:27Z",
      "charges" => [
        [
          "id" => "ch_J3j5Xbof0nCVLBEN",
          "code" => "6ad1b3ff-8f66-47d0-b2b9-c665786f1264",
          "gateway_id" => "21987731",
          "amount" => 7866,
          "status" => "failed",
          "currency" => "BRL",
          "payment_method" => "credit_card",
          "created_at" => "2023-02-08T23:00:27Z",
          "updated_at" => "2023-02-08T23:00:28Z",
          "customer" => [
            "id" => "cus_8Mo7AD5uYt8oKvjV",
            "name" => "Luiz Felipe",
            "email" => "lf.system@outlook.com",
            "code" => "6ad1b3ff-8f66-47d0-b2b9-c665786f1264",
            "document" => "11111111111",
            "document_type" => "cpf",
            "type" => "individual",
            "gender" => "male",
            "delinquent" => false,
            "address" => [
              "id" => "addr_A5WaMGlUwBfNVzDr",
              "line_1" => "Rua 1",
              "line_2" => "test",
              "zip_code" => "12345678",
              "city" => "test",
              "state" => "SP",
              "country" => "BR",
              "status" => "active",
              "created_at" => "2022-12-19T17:13:21Z",
              "updated_at" => "2023-02-08T23:00:26Z"
            ],
            "created_at" => "2022-12-19T17:13:21Z",
            "updated_at" => "2023-02-08T23:00:26Z",
            "phones" => [
              "home_phone" => [
                "country_code" => "55",
                "number" => "999999999",
                "area_code" => "11"
              ],
              "mobile_phone" => [
                "country_code" => "55",
                "number" => "999999999",
                "area_code" => "11"
              ]
            ]
          ],
          "last_transaction" => [
            "id" => "tran_dg1nowCqPhJJnrBk",
            "transaction_type" => "credit_card",
            "gateway_id" => "21987731",
            "amount" => 7866,
            "status" => "not_authorized",
            "success" => false,
            "installments" => 1,
            "acquirer_name" => "pagarme",
            "acquirer_tid" => "21987731",
            "acquirer_nsu" => "21987731",
            "acquirer_auth_code" => "449354",
            "acquirer_message" => "Transação aprovada com sucesso",
            "acquirer_return_code" => "0000",
            "operation_type" => "auth_and_capture",
            "card" => [
              "id" => "card_2vxbpNuavukdj6Gl",
              "first_six_digits" => "400000",
              "last_four_digits" => "0010",
              "brand" => "visa",
              "holder_name" => "John Doe",
              "holder_document" => "11111111111",
              "exp_month" => 12,
              "exp_year" => 2028,
              "status" => "active",
              "type" => "credit",
              "created_at" => "2023-02-08T14:53:13Z",
              "updated_at" => "2023-02-08T23:00:27Z",
              "billing_address" => [
                "zip_code" => "12345678",
                "city" => "test",
                "state" => "SP",
                "country" => "BR",
                "line_1" => "Rua 1",
                "line_2" => "test"
              ]
            ],
            "created_at" => "2023-02-08T23:00:27Z",
            "updated_at" => "2023-02-08T23:00:27Z",
            "gateway_response" => [
              "code" => "200",
              "errors" => []
            ],
            "antifraud_response" => [
              "status" => "reproved",
              "score" => "very_high",
              "provider_name" => "pagarme"
            ],
            "metadata" => []
          ]
        ]
      ],
      "checkouts" => []
    ];



    $endpoint = "https://api.pagar.me/core/v5/orders";
    Http::fake([$endpoint => Http::response($response, 200)]);
  }

  public function createCustomer()
  {
    $response = [
      "id" => "cus_agLAQDLHAH7QRrwj",
      "name" => "Luiz Felipe Fake",
      "email" => "lf.system@outlook.com",
      "code" => "6e3c80d6-0ea6-4b19-9d2a-f7f28b3b1b45",
      "document" => "00000000000",
      "document_type" => "cpf",
      "type" => "individual",
      "gender" => "male",
      "delinquent" => false,
      "address" => [
        "id" => "addr_4XVDNnYT2Ty5vpZA",
        "line_1" => "4375, Avenida Luiz Carlos Tunes",
        "zip_code" => "13606536",
        "city" => "Araras",
        "state" => "SP",
        "country" => "BR",
        "status" => "active",
        "created_at" => "2022-11-25T15:08:30Z",
        "updated_at" => "2022-11-25T19:35:47Z"
      ],
      "created_at" => "2022-11-25T15:08:30Z",
      "updated_at" => "2022-11-25T19:35:47Z",
      "birthdate" => "1993-11-04T00:00:00Z",
      "phones" => [
        "home_phone" => [
          "country_code" => "55",
          "number" => "000000000",
          "area_code" => "19"
        ],
        "mobile_phone" => [
          "country_code" => "55",
          "number" => "000000000",
          "area_code" => "19"
        ]
      ]
    ];

    $endpoint = $this->endpoints['customers']['createCustomer'];
    Http::fake([$endpoint => Http::response($response, 200)]);
  }

  public function listCustomers()
  {
    $response =   [
      "data" =>  [
        [
          "id" => "cus_agLAQDLHAH7QRrwj",
          "name" => "Luiz Felipe Fake",
          "email" => "lf.system@outlook.com",
          "code" => "6e3c80d6-0ea6-4b19-9d2a-f7f28b3b1b45",
          "document" => "00000000000",
          "document_type" => "cpf",
          "type" => "individual",
          "gender" => "male",
          "delinquent" => false,
          "address" =>  [
            "id" => "addr_4XVDNnYT2Ty5vpZA",
            "line_1" => "4375, Avenida Luiz Carlos Tunes",
            "zip_code" => "13606536",
            "city" => "Araras",
            "state" => "SP",
            "country" => "BR",
            "status" => "active",
            "created_at" => "2022-11-25T15:08:30Z",
            "updated_at" => "2022-11-25T19:47:29Z",
          ],
          "created_at" => "2022-11-25T15:08:30Z",
          "updated_at" => "2022-11-25T19:47:29Z",
          "birthdate" => "1993-11-04T00:00:00Z",
          "phones" =>  [
            "home_phone" =>  [
              "country_code" => "55",
              "number" => "000000000",
              "area_code" => "19",
            ],
            "mobile_phone" =>  [
              "country_code" => "55",
              "number" => "000000000",
              "area_code" => "19",
            ]
          ]
        ],
        [
          "id" => "cus_N1JGb3GhrhDbkYLD",
          "name" => "Tony Stark",
          "email" => "tonystarkk@avengers.com",
          "code" => "6e3c80d6-0ea6-4b19-9d2a-f7f28b3b1b45",
          "document" => "93095135270",
          "document_type" => "cpf",
          "type" => "individual",
          "gender" => "male",
          "delinquent" => false,
          "address" => [
            "id" => "addr_rZQj6qNTZTMR3dz2",
            "line_1" => "375, Av. General Justo, Centro",
            "line_2" => "8º andar",
            "zip_code" => "20021130",
            "city" => "Rio de Janeiro",
            "state" => "RJ",
            "country" => "BR",
            "status" => "active",
            "created_at" => "2022-11-25T14:45:56Z",
            "updated_at" => "2022-11-25T15:05:06Z",
          ],
          "created_at" => "2022-11-25T14:45:56Z",
          "updated_at" => "2022-11-25T15:05:06Z",
          "birthdate" => "1984-05-03T00:00:00Z",
          "phones" =>  [
            "home_phone" =>  [
              "country_code" => "55",
              "number" => "000000000",
              "area_code" => "21",
            ],
            "mobile_phone" =>  [
              "country_code" => "55",
              "number" => "000000000",
              "area_code" => "21",
            ]
          ],
          "metadata" =>  [
            "id" => "my_customer_id",
            "company" => "Avengers",
          ]
        ]
      ],
      "paging" =>  [
        "total" => 2,
      ]
    ];

    $endpoint = $this->endpoints['customers']['listCustomers'];
    Http::fake([$endpoint => Http::response($response, 200)]);
  }

  public function getCustomer()
  {
    $response = [
      "id" => "cus_agLAQDLHAH7QRrwj",
      "name" => "Luiz Felipe Fake",
      "email" => "lf.system@outlook.com",
      "code" => "6e3c80d6-0ea6-4b19-9d2a-f7f28b3b1b45",
      "document" => "00000000000",
      "document_type" => "cpf",
      "type" => "individual",
      "gender" => "male",
      "delinquent" => false,
      "address" => [
        "id" => "addr_4XVDNnYT2Ty5vpZA",
        "line_1" => "4375, Avenida Luiz Carlos Tunes",
        "zip_code" => "13606536",
        "city" => "Araras",
        "state" => "SP",
        "country" => "BR",
        "status" => "active",
        "created_at" => "2022-11-25T15:08:30Z",
        "updated_at" => "2022-11-25T19:47:29Z",
      ],
      "created_at" => "2022-11-25T15:08:30Z",
      "updated_at" => "2022-11-25T19:47:29Z",
      "birthdate" => "1993-11-04T00:00:00Z",
      "phones" => [
        "home_phone" => [
          "country_code" => "55",
          "number" => "000000000",
          "area_code" => "19",
        ],
        "mobile_phone" => [
          "country_code" => "55",
          "number" => "000000000",
          "area_code" => "19",
        ]
      ]
    ];

    $endpoint = $this->endpoints['customers']['getCustomer'];
    Http::fake([$endpoint => Http::response($response, 200)]);
  }

  public function editCustomer()
  {
    $response = [
      "name" => "Luiz Felipe Edited",
      "email" => "lf.system@outlook.com",
      "code" => "6e3c80d6-0ea6-4b19-9d2a-f7f28b3b1b45",
      "document" => "00000000000",
      "document_type" => "cpf",
      "type" => "individual",
      "gender" => "male",
      "delinquent" => false,
      "address" => [
        "id" => "addr_4XVDNnYT2Ty5vpZA",
        "line_1" => "4375, Avenida Luiz Carlos Tunes",
        "zip_code" => "13606536",
        "city" => "Araras",
        "state" => "SP",
        "country" => "BR",
        "status" => "active",
        "created_at" => "2022-11-25T15:08:30Z",
        "updated_at" => "2022-11-25T19:35:47Z"
      ],
      "created_at" => "2022-11-25T15:08:30Z",
      "updated_at" => "2022-11-28T15:56:41Z",
      "birthdate" => "1993-11-04T00:00:00Z",
      "phones" => [
        "home_phone" => [
          "country_code" => "55",
          "number" => "000000000",
          "area_code" => "19"
        ],
        "mobile_phone" => [
          "country_code" => "55",
          "number" => "000000000",
          "area_code" => "19"
        ]
      ]
    ];

    $endpoint = $this->endpoints['customers']['getCustomer'];
    Http::fake([$endpoint => Http::response($response, 200)]);
  }

  public function createCard()
  {
    $response = [
      "id" => "card_GzWmq91T0ToqedJ6",
      "first_six_digits" => "542501",
      "last_four_digits" => "7793",
      "brand" => "visa",
      "holder_name" => "Luiz Felipe",
      "holder_document" => "93095135270",
      "exp_month" => 1,
      "exp_year" => 2030,
      "status" => "active",
      "label" => "Sua bandeira",
      "created_at" => "2017-07-07T19:50:33Z",
      "updated_at" => "2017-07-07T19:50:33Z",
      "billing_address" => [
        "zip_code" => "220000111",
        "city" => "Rio de Janeiro",
        "state" => "RJ",
        "country" => "BR",
        "line_1" => "375, Av. General Osorio, Centro",
        "line_2" => "7º Andar"
      ],
      "customer" => [
        "id" => "cus_yoqONwOJI1IBNbjl",
        "name" => "Tony Stark",
        "email" => "0fee2dc5-e440-4dd1-9cd6-9c2bc90533d0@avengers.com",
        "document" => "93095135270",
        "type" => "individual",
        "delinquent" => false,
        "created_at" => "2017-07-07T19:50:23Z",
        "updated_at" => "2017-07-07T19:50:23Z",
        "phones" => [
          "home_phone" => [
            "country_code" => "55",
            "number" => "000000000",
            "area_code" => "21"
          ],
          "mobile_phone" => [
            "country_code" => "55",
            "number" => "000000000",
            "area_code" => "21"
          ]
        ],
        "metadata" => [
          "company" => "Avengers"
        ]
      ],
      "type" => "credit"
    ];

    $endpoint = $this->endpoints['customers']['createCard'];
    Http::fake([$endpoint => Http::response($response, 200)]);
  }

  public function deleteCard()
  {
    $response = [
      "id" => "card_OBojZD1IvGcPq8gl",
      "first_six_digits" => "542501",
      "last_four_digits" => "7793",
      "brand" => "Mastercard",
      "holder_name" => "Tony Stark",
      "exp_month" => 1,
      "exp_year" => 2022,
      "status" => "deleted",
      "created_at" => "2018-04-04T12:43:16Z",
      "updated_at" => "2018-04-04T12:43:30Z",
      "deleted_at" => "2018-04-04T12:43:30Z",
      "billing_address" => [
        "zip_code" => "90265",
        "city" => "Malibu",
        "state" => "CA",
        "country" => "US",
        "line_1" => "10880, Malibu Point, Malibu Central"
      ],
      "customer" => [
        "id" => "cus_9El4qnTEKFKQoV7r",
        "name" => "Tony Stark",
        "email" => "609671d7-7b1b-4b31-b3e0-1709cc8d9637@avengers.com",
        "document" => "93095135270",
        "type" => "individual",
        "delinquent" => false,
        "created_at" => "2018-04-04T12:05:08Z",
        "updated_at" => "2018-04-04T12:05:08Z",
        "phones" => [
          "home_phone" => [
            "country_code" => "55",
            "number" => "000000000",
            "area_code" => "21"
          ],
          "mobile_phone" => [
            "country_code" => "55",
            "number" => "000000000",
            "area_code" => "21"
          ]
        ],
        "metadata" => [
          "company" => "Avengers"
        ]
      ],
      "type" => "credit"
    ];

    $endpoint = "https://api.pagar.me/core/v5/customers/*/cards/*";
    Http::fake([$endpoint => Http::response($response, 200)]);
  }

  public function getCard()
  {
    $response = [
      "id" => "card_GzWmq91T0ToqedJ6",
      "first_six_digits" => "411111",
      "last_four_digits" => "666",
      "brand" => "visa",
      "holder_name" => "Luiz Felipe",
      "holder_document" => "00000000000",
      "exp_month" => 12,
      "exp_year" => 2022,
      "status" => "active",
      "type" => "credit",
      "label" => "Renner",
      "created_at" => "2022-11-28T22:07:22Z",
      "updated_at" => "2022-11-28T22:07:22Z",
      "billing_address" => [
        "zip_code" => "13606536",
        "city" => "Araras",
        "state" => "SP",
        "country" => "BR",
        "line_1" => "4375, Avenida Luiz Carlos Tunes",
      ],
      "customer" => [
        "id" => "cus_agLAQDLHAH7QRrwj",
        "name" => "Luiz Felipe Fake",
        "email" => "lf.system@outlook.com",
        "code" => "6e3c80d6-0ea6-4b19-9d2a-f7f28b3b1b45",
        "document" => "00000000000",
        "document_type" => "cpf",
        "type" => "individual",
        "gender" => "male",
        "delinquent" => false,
        "created_at" => "2022-11-25T15:08:30Z",
        "updated_at" => "2022-11-28T23:43:12Z",
        "birthdate" => "1993-11-04T00:00:00Z",
        "phones" => [
          "home_phone" => [
            "country_code" => "55",
            "number" => "000000000",
            "area_code" => "19",
          ],
          "mobile_phone" => [
            "country_code" => "55",
            "number" => "000000000",
            "area_code" => "19",
          ]
        ]
      ]
    ];

    $endpoint = $this->endpoints['customers']['getCard'];
    Http::fake([$endpoint => Http::response($response, 200)]);
  }

  public function listCards()
  {
    $payload = [
      "data" => [
        [
          "id" => "card_GzWmq91T0ToqedJ6",
          "first_six_digits" => "411111",
          "last_four_digits" => "1111",
          "brand" => "visa",
          "holder_name" => "Luiz Felipe",
          "holder_document" => "00000000000",
          "exp_month" => 12,
          "exp_year" => 2022,
          "status" => "active",
          "type" => "credit",
          "label" => "Renner",
          "created_at" => "2022-11-28T22:07:22Z",
          "updated_at" => "2022-11-28T22:07:22Z",
          "billing_address" => [
            "zip_code" => "13606536",
            "city" => "Araras",
            "state" => "SP",
            "country" => "BR",
            "line_1" => "4375, Avenida Luiz Carlos Tunes",
          ]
        ],
      ],
      "paging" => [
        "total" => 1
      ]
    ];

    $endpoint = $this->endpoints['customers']['createCard'];
    Http::fake([$endpoint => Http::response($payload, 200)]);
  }

  public function createTokenCard()
  {
    $response = [
      "id" => "token_xYvP6Y7uluwL0JEl",
      "type" => "card",
      "created_at" => "2022-11-29T15:07:53Z",
      "expires_at" => "2022-11-29T15:08:53Z",
      "card" => [
        "first_six_digits" => "411111",
        "last_four_digits" => "1111",
        "holder_name" => "Luiz Felipe",
        "holder_document" => "00000000000",
        "exp_month" => 12,
        "exp_year" => 2022,
        "brand" => "visa",
        "label" => "magazine",
      ]
    ];

    $endpoint = 'https://api.pagar.me/core/v5/tokens?appId=*';

    Http::fake([$endpoint => Http::response($response, 200)]);
  }

  public function createPlan()
  {
    $response = json_decode('{
      "id": "plan_DZOp75JH4PT3La3A",
      "name": "Plano 1 Mockery",
      "description": "Descri\u00e7\u00e3o Plano 1",
      "url": "plans/plan_DZOp75JH4PT3La3A/lf-dev-test/plano-1",
      "interval": "month",
      "interval_count": 1,
      "billing_type": "postpaid",
      "payment_methods": ["credit_card"],
      "installments": [1],
      "status": "active",
      "currency": "BRL",
      "created_at": "2023-03-17T22:18:38Z",
      "updated_at": "2023-03-17T22:18:38Z",
      "items": [
        {
          "id": "pi_ybl8aWvcPbS4gVYK",
          "name": "Plano 1",
          "quantity": 1,
          "status": "active",
          "created_at": "2023-03-17T22:18:38Z",
          "updated_at": "2023-03-17T22:18:38Z",
          "pricing_scheme": { 
            "price": 100, 
            "scheme_type": "unit" 
           }
        }
      ]
    }', true);

    $endpoint = $this->endpoints['plans']['createPlan'];
    Http::fake([$endpoint => Http::response($response, 200)]);
  }

  public function updatePlan()
  {
    $response = json_decode('{
      "id": "plan_DZOp75JH4PT3La3A",
      "name": "Plano updated Mocery",
      "description": "Descri\u00e7\u00e3o Plano Mockery updated",
      "url": "plans/plan_DZOp75JH4PT3La3A/lf-dev-test/plano-updated",
      "interval": "month",
      "interval_count": 1,
      "billing_type": "postpaid",
      "payment_methods": ["credit_card"],
      "installments": [1],
      "status": "active",
      "currency": "BRL",
      "created_at": "2023-03-17T22:18:38Z",
      "updated_at": "2023-03-18T00:59:41Z",
      "items": [
        {
          "id": "pi_ybl8aWvcPbS4gVYK",
          "name": "Plano 1",
          "quantity": 1,
          "status": "active",
          "created_at": "2023-03-17T22:18:38Z",
          "updated_at": "2023-03-17T22:18:38Z",
          "pricing_scheme": { "price": 100, "scheme_type": "unit" }
        }
      ]
    }', true);

    $endpoint = $this->endpoints['plans']['updatePlan'];
    Http::fake([$endpoint => Http::response($response, 200)]);
  }

  public function createPlanSubscription()
  {
    $response = json_decode('{"id":"sub_lz58jMlHjofkawGv","code":"d24935fb-3a3b-4b40-8719-cc2f5a1c42b7","start_at":"2023-03-22T00:00:00Z","interval":"month","interval_count":1,"billing_type":"postpaid","current_cycle":{"id":"cycle_X8RAR1nsNpiPprND","start_at":"2023-03-22T00:00:00Z","end_at":"2023-04-21T23:59:59Z","billing_at":"2023-04-22T00:00:00Z","status":"unbilled","cycle":1},"next_billing_at":"2023-05-22T00:00:00Z","payment_method":"credit_card","currency":"BRL","installments":1,"status":"active","created_at":"2023-03-22T17:39:02Z","updated_at":"2023-03-22T17:39:02Z","customer":{"id":"cus_agLAQDLHAH7QRrwj","name":"Luiz F","email":"lf.system@outlook.com","code":"d24935fb-3a3b-4b40-8719-cc2f5a1c42b7","document":"42520197889","document_type":"cpf","type":"individual","gender":"male","delinquent":false,"address":{"id":"addr_5bjWZDwgUqIY0lBK","line_1":"Rua 1","line_2":"test","zip_code":"12345678","city":"test","state":"SP","country":"BR","status":"active","created_at":"2023-03-19T16:19:58Z","updated_at":"2023-03-22T17:39:01Z"},"created_at":"2022-11-25T15:08:30Z","updated_at":"2023-03-22T17:39:01Z","birthdate":"1993-11-04T00:00:00Z","phones":{"home_phone":{"country_code":"55","number":"999999999","area_code":"11"},"mobile_phone":{"country_code":"55","number":"999999999","area_code":"11"}}},"card":{"id":"card_5L96dQ7CquY9MkK4","first_six_digits":"400000","last_four_digits":"0077","brand":"visa","holder_name":"John Doe","holder_document":"42520197889","exp_month":12,"exp_year":2028,"status":"active","type":"credit","created_at":"2023-03-19T19:59:10Z","updated_at":"2023-03-21T19:21:45Z","billing_address":{"zip_code":"12345678","city":"test","state":"SP","country":"BR","line_1":"Rua 1","line_2":"test"}},"plan":{"id":"plan_3p7NExvs4ULj51GP","name":"Test Plan","description":"Test Plan Description","url":"plans\/plan_3p7NExvs4ULj51GP\/lf-dev-test\/test-plan","interval":"month","interval_count":1,"billing_type":"postpaid","payment_methods":["credit_card"],"installments":[1],"status":"active","currency":"BRL","created_at":"2023-03-22T17:39:00Z","updated_at":"2023-03-22T17:39:00Z"},"items":[{"id":"si_ELz5w8zilFPLYmbK","name":"Test Plan","description":"Test Plan","quantity":1,"status":"active","created_at":"2023-03-22T17:39:02Z","updated_at":"2023-03-22T17:39:02Z","pricing_scheme":{"price":8000,"scheme_type":"unit"}}],"boleto":[]}', true);
    $endpoint = $this->endpoints['subscriptions']['createPlanSubscription'];
    Http::fake([$endpoint => Http::response($response, 200)]);
  }

  public function updateCardSubscription()
  {
    $response = json_decode('{"id":"sub_Aynpdw2UanhY37w1","code":"db6f8ae1-2c36-4d1a-93e5-b3204add693d","start_at":"2023-03-22T00:00:00Z","interval":"month","interval_count":1,"billing_type":"postpaid","current_cycle":{"id":"cycle_m6X42p8sMF512jOv","start_at":"2023-03-22T00:00:00Z","end_at":"2023-04-21T23:59:59Z","billing_at":"2023-04-22T00:00:00Z","status":"unbilled","cycle":1},"next_billing_at":"2023-05-22T00:00:00Z","payment_method":"credit_card","currency":"BRL","installments":1,"status":"active","created_at":"2023-03-23T02:29:22Z","updated_at":"2023-03-23T02:29:23Z","customer":{"id":"cus_agLAQDLHAH7QRrwj","name":"Luiz F","email":"lf.system@outlook.com","code":"db6f8ae1-2c36-4d1a-93e5-b3204add693d","document":"42520197889","document_type":"cpf","type":"individual","gender":"male","delinquent":false,"created_at":"2022-11-25T15:08:30Z","updated_at":"2023-03-23T02:29:21Z","birthdate":"1993-11-04T00:00:00Z","phones":{"home_phone":{"country_code":"55","number":"999999999","area_code":"11"},"mobile_phone":{"country_code":"55","number":"999999999","area_code":"11"}}},"card":{"id":"card_5L96dQ7CquY9MkK4","first_six_digits":"400000","last_four_digits":"0077","brand":"visa","holder_name":"John Doe","holder_document":"42520197889","exp_month":12,"exp_year":2028,"status":"active","type":"credit","created_at":"2023-03-19T19:59:10Z","updated_at":"2023-03-23T02:26:39Z","billing_address":{"zip_code":"12345678","city":"test","state":"SP","country":"BR","line_1":"Rua 1","line_2":"test"}},"plan":{"id":"plan_1LPrZBmH2miLxrjJ","name":"Test Plan","description":"Test Plan Description","url":"plans\/plan_1LPrZBmH2miLxrjJ\/lf-dev-test\/test-plan","interval":"month","interval_count":1,"billing_type":"postpaid","payment_methods":["credit_card"],"installments":[1],"status":"active","currency":"BRL","created_at":"2023-03-23T02:29:21Z","updated_at":"2023-03-23T02:29:21Z"},"items":[{"id":"si_ZBgxYwvwtztZK67p","name":"Test Plan","description":"Test Plan","quantity":1,"status":"active","created_at":"2023-03-23T02:29:22Z","updated_at":"2023-03-23T02:29:22Z","pricing_scheme":{"price":8000,"scheme_type":"unit"}}],"boleto":[]}', true);
    $endpoint = $this->endpoints['subscriptions']['editSubscriptionCard'];
    Http::fake([$endpoint => Http::response($response, 200)]);
  }

  public function cancelSubscription()
  {
    $response = '{"id":"sub_lMjnb7uyJuEaLWRv_MOCKERY","code":"444592bd-b197-4d7e-98cc-847a6fc73ee8","start_at":"2023-03-22T00:00:00Z","interval":"month","interval_count":1,"billing_type":"postpaid","current_cycle":{"id":"cycle_45gkKRxTrwhwA0bW","start_at":"2023-03-22T00:00:00Z","end_at":"2023-04-21T23:59:59Z","billing_at":"2023-04-22T00:00:00Z","status":"unbilled","cycle":1},"next_billing_at":"2023-05-22T00:00:00Z","payment_method":"credit_card","currency":"BRL","installments":1,"status":"canceled","created_at":"2023-03-22T20:02:35Z","updated_at":"2023-03-22T20:02:36Z","canceled_at":"2023-03-22T20:02:36Z","customer":{"id":"cus_agLAQDLHAH7QRrwj","name":"Luiz F","email":"lf.system@outlook.com","code":"444592bd-b197-4d7e-98cc-847a6fc73ee8","document":"42520197889","document_type":"cpf","type":"individual","gender":"male","delinquent":false,"created_at":"2022-11-25T15:08:30Z","updated_at":"2023-03-22T20:02:34Z","birthdate":"1993-11-04T00:00:00Z","phones":{"home_phone":{"country_code":"55","number":"999999999","area_code":"11"},"mobile_phone":{"country_code":"55","number":"999999999","area_code":"11"}}},"card":{"id":"card_5L96dQ7CquY9MkK4","first_six_digits":"400000","last_four_digits":"0077","brand":"visa","holder_name":"John Doe","holder_document":"42520197889","exp_month":12,"exp_year":2028,"status":"active","type":"credit","created_at":"2023-03-19T19:59:10Z","updated_at":"2023-03-21T19:21:45Z","billing_address":{"zip_code":"12345678","city":"test","state":"SP","country":"BR","line_1":"Rua 1","line_2":"test"}},"plan":{"id":"plan_kVDr2WuO9sJA7nYe","name":"Test Plan","description":"Test Plan Description","url":"plans\/plan_kVDr2WuO9sJA7nYe\/lf-dev-test\/test-plan","interval":"month","interval_count":1,"billing_type":"postpaid","payment_methods":["credit_card"],"installments":[1],"status":"active","currency":"BRL","created_at":"2023-03-22T20:02:34Z","updated_at":"2023-03-22T20:02:34Z"},"items":[{"id":"si_bKBkx74FrFGx16qW","name":"Test Plan","description":"Test Plan","quantity":1,"status":"active","created_at":"2023-03-22T20:02:35Z","updated_at":"2023-03-22T20:02:35Z","pricing_scheme":{"price":8000,"scheme_type":"unit"}}],"boleto":[]}';
    $endpoint = $this->endpoints['subscriptions']['cancelSubscription'];
    Http::fake([$endpoint => Http::response($response, 200)]);
  }
}
