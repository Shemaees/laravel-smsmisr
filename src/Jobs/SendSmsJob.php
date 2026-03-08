<?php

namespace Ghanem\LaravelSmsmisr\Jobs;

use DateTimeInterface;
use Ghanem\LaravelSmsmisr\Smsmisr;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendSmsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public readonly string $message,
        public readonly string $to,
        public readonly ?string $sender = null,
        public readonly int $language = 1,
        public readonly ?DateTimeInterface $scheduledAt = null,
    ) {
        $this->onQueue(config('smsmisr.queue'));
    }

    public function handle(Smsmisr $smsmisr): void
    {
        $smsmisr->send(
            $this->message,
            $this->to,
            $this->sender,
            $this->language,
            $this->scheduledAt,
        );
    }
}
