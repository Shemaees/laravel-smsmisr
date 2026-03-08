<?php

namespace Ghanem\LaravelSmsmisr\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Throwable;

class SmsFailed
{
    use Dispatchable;

    public function __construct(
        public readonly string $to,
        public readonly string $message,
        public readonly string $sender,
        public readonly Throwable $exception,
        public readonly string $type = 'sms',
    ) {
    }
}
