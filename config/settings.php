<?php

return [
  'url_front' => env('URL_FRONT', 'http://localhost:3000'),
  'url_verification_notice' => env('URL_VERIFICATION_NOTICE', 'http://localhost:3000/auth/email/verification-notice'),
  'requests-logs' => env('DEBUG_REQUESTS', false),
  'paginate_photos' => env('PAGINATE_PHOTOS', 30),
  'paginate' => env('PAGINATE', 30),
  'url' => [
    'verify_code_email' => env('URL_VERIFY_CODE_EMAIL', 'http://localhost:3000/auth/email/verify-code'),
  ],
  'token' => [
    'internal' => env('INTERNAL_TOKEN', '5b74dabd2bdb2362d88f5eb37babb9fe97eee487'),
  ],
  'days_of_week' => [
    0 => 'sunday',
    1 => 'monday',
    2 => 'tuesday',
    3 => 'wednesday',
    4 => 'thursday',
    5 => 'friday',
    6 => 'saturday',
  ],
  'sanctum' => env('SANCTUM', ''),
  'billing_type' => env('BILLING_CHARGE_TYPE', 'monthly'),

];