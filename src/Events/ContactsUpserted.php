<?php

namespace masterhosteg\Events;

use Illuminate\Foundation\Events\Dispatchable;

class ContactsUpserted
{
    use Dispatchable;
    public array $payload;
    public function __construct(array $payload) { $this->payload = $payload; }
} 