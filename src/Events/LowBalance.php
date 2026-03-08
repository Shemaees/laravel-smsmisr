<?php

namespace Ghanem\LaravelSmsmisr\Events;

use Illuminate\Foundation\Events\Dispatchable;

class LowBalance
{
    use Dispatchable;

    public function __construct(
        public readonly int|float $smsBalance,
        public readonly int|float $verifyBalance,
        public readonly int $threshold,
    ) {
    }
}
