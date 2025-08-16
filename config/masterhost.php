<?php

return [
    'webhook_secret' => env('masterhosteg_WEBHOOK_SECRET', ''),
    'webhook_route' => env('masterhosteg_WEBHOOK_ROUTE', '/masterhost/webhook'),
    'webhook_signature_header' => env('masterhosteg_WEBHOOK_SIGNATURE_HEADER', 'x-webhook-signature'),
    'api_key' => env('masterhosteg_API_KEY', ''),
    'personal_access_token' => env('masterhosteg_PERSONAL_ACCESS_TOKEN', ''),
]; 