<?php

return [
  'secrete_key' => env('PAGARME_SECRET_KEY'),
  'public_key'  => env('PAGARME_PUBLIC_KEY'),
  'account_id'  => env('PAGARME_ACCOUNT_ID'),
  'base_url'    => env('PAGARME_BASE_URL', 'https://api.pagar.me/core/v5'),
  'endpoints' => [
    'changes' => [
      'captureChange' => [
        'endpoint' => '/charges/{charge_id}/capture',
        'method'   => 'POST'
      ],
      'getChange' => [
        'endpoint' => '/charges/{charge_id}',
        'method'   => 'GET'
      ],
      'editBillingCard' => [
        'endpoint' => '/charges/{charge_id}/card',
        'method'   => 'PATCH'
      ],
      'editBillingDueDate' => [
        'endpoint' => '/charges/{charge_id}/due-date',
        'method'   => 'PATCH'
      ],
      'editPaymentMethod' => [
        'endpoint' => '/charges/{charge_id}/payment-method',
        'method'   => 'PATCH'
      ],
      'cancelCharge' => [
        'endpoint' => '/charges/{charge_id}',
        'method'   => 'DELETE'
      ],
      'listChanges' => [
        'endpoint' => '/charges',
        'method'   => 'GET'
      ],
      'holdChargeManually' => [
        'endpoint' => '/charges/{charge_id}/retry',
        'method'   => 'POST'
      ],
      'confirmCash' => [
        'endpoint' => '/charges/{charge_id}/confirm-payment',
        'method'   => 'POST'
      ],

    ],
    'customers' => [
      'createCustomer' => [
        'endpoint' => '/customers',
        'method'   => 'POST'
      ],
      'getCustomer' => [
        'endpoint' => '/customers/{customer_id}',
        'method'   => 'GET'
      ],
      'editCustomer' => [
        'endpoint' => '/customers/{customer_id}',
        'method'   => 'PUT'
      ],
      'listCustomers' => [
        'endpoint' => '/customers',
        'method'   => 'GET'
      ],
      'createCard' => [
        'endpoint' => '/customers/{customer_id}/cards',
        'method'   => 'POST'
      ],
      'getCard' => [
        'endpoint' => '/customers/{customer_id}/cards/{card_id}',
        'method'   => 'GET'
      ],
      'listCards' => [
        'endpoint' => '/customers/{customer_id}/cards',
        'method'   => 'GET'
      ],
      'editCard' => [
        'endpoint' => '/customers/{customer_id}/cards/{card_id}',
        'method'   => 'PUT'
      ],
      'deleteCard' => [
        'endpoint' => '/customers/{customer_id}/cards/{card_id}',
        'method'   => 'DELETE'
      ],
      'renew' => [
        'endpoint' => '/customers/{customer_id}/cards/{card_id}renew',
        'method'   => 'POST'
      ],
      'createTokenCard' => [
        'endpoint' => '/tokens?appId='.env('PAGARME_PUBLIC_KEY', 'APP_ID'),
        'method'   => 'POST'
      ],
    ],
    'orders' => [
      'createOrder' => [
        'endpoint' => '/orders',
        'method'   => 'POST'
      ],
      'getOrder' => [
        'endpoint' => '/orders/{order_id}',
        'method'   => 'GET'
      ],
      'closeOrder' => [
        'endpoint' => '/orders/{order_id}/closed',
        'method'   => 'POST'
      ],
      'listOrders' => [
        'endpoint' => '/orders',
        'method'   => 'GET'
      ],
      'createOrderWithPaymentMethodCheckout' => [
        'endpoint' => '/orders',
        'method'   => 'POST'
      ],
      'includeChanges' => [
        'endpoint' => '/changes',
        'method'   => 'POST'
      ],
    ],
    'plans' => [
      'createPlan' => [
        'endpoint' => '/plans',
        'method'   => 'POST'
      ],
      'editPlan' => [
        'endpoint' => '/plans/{plan_id}',
        'method'   => 'PUT'
      ],
    ],
    'subscriptions' => [
      'createPlanSubscription' => [
        'endpoint' => '/subscriptions',
        'method'   => 'POST'
      ],
      'getPlanSubscription' => [
        'endpoint' => '/subscriptions/{subscription_id}',
        'method'   => 'GET'
      ],
      'cancelSubscription' => [
        'endpoint' => '/subscriptions/{subscription_id}',
        'method'   => 'DELETE'
      ],
      'editSubscriptionCard' => [
        'endpoint' => '/subscriptions/{subscription_id}/card',
        'method'   => 'PATCH'
      ],
      'editSubscriptionPaymentMethod' => [
        'endpoint' => '/subscriptions/{subscription_id}/payment-method',
        'method'   => 'PATCH'
      ],
    ]
  ],
];