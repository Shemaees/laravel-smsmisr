<?php

namespace Ghanem\LaravelSmsmisr\Events;

use Illuminate\Foundation\Events\Dispatchable;

class SmsSending
{
    use Dispatchable;

    public function __construct(
        public readonly string $to,
        public readonly string $message,
        public readonly string $sender,
        public readonly string $type = 'sms',
    ) {
    }
}
