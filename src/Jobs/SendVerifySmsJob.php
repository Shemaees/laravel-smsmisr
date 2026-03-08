<?php

namespace Ghanem\LaravelSmsmisr\Jobs;

use Ghanem\LaravelSmsmisr\Smsmisr;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendVerifySmsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public readonly string $code,
        public readonly string $to,
        public readonly ?string $sender = null,
        public readonly ?string $template = null,
    ) {
        $this->onQueue(config('smsmisr.queue'));
    }

    public function handle(Smsmisr $smsmisr): void
    {
        $smsmisr->sendVerify(
            $this->code,
            $this->to,
            $this->sender,
            $this->template,
        );
    }
}
