<?php

use Illuminate\Support\Facades\Route;

Route::post(config('masterhosteg.webhook_route'), [
    masterhosteg\Http\Controllers\WebhookController::class,
    'handle',
]); 