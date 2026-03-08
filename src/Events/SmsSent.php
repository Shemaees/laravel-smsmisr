<?php

namespace Ghanem\LaravelSmsmisr\Events;

use Ghanem\LaravelSmsmisr\SmsmisrResponse;
use Illuminate\Foundation\Events\Dispatchable;

class SmsSent
{
    use Dispatchable;

    public function __construct(
        public readonly string $to,
        public readonly string $message,
        public readonly string $sender,
        public readonly SmsmisrResponse $response,
        public readonly string $type = 'sms',
    ) {
    }
}
