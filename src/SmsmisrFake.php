<?php

namespace Ghanem\LaravelSmsmisr;

use DateTimeInterface;
use PHPUnit\Framework\Assert as PHPUnit;

class SmsmisrFake extends Smsmisr
{
    protected array $sent = [];
    protected array $bulk = [];
    protected array $verified = [];
    protected array $queued = [];
    protected array $queuedBulk = [];
    protected array $queuedVerify = [];
    protected int $balanceChecks = 0;
    protected int $balanceVerifyChecks = 0;
    protected int $healthChecks = 0;

    public function __construct()
    {
        parent::__construct(null);
    }

    public function send(
        string $message,
        string $to,
        ?string $sender = null,
        int $language = 1,
        ?DateTimeInterface $scheduledAt = null,
    ): SmsmisrResponse {
        $this->sent[] = [
            'message' => $message,
            'to' => $to,
            'sender' => $sender ?? config('smsmisr.sender'),
            'language' => $language,
            'scheduledAt' => $scheduledAt,
        ];

        return SmsmisrResponse::fromArray([
            'code' => self::SMSMISR_SUCCESS_CODE,
            'message' => 'Success',
        ]);
    }

    public function sendBulk(
        string $message,
        array $recipients,
        ?string $sender = null,
        int $language = 1,
        ?DateTimeInterface $scheduledAt = null,
    ): SmsmisrResponse {
        $this->bulk[] = [
            'message' => $message,
            'recipients' => $recipients,
            'sender' => $sender ?? config('smsmisr.sender'),
            'language' => $language,
            'scheduledAt' => $scheduledAt,
        ];

        return SmsmisrResponse::fromArray([
            'code' => self::SMSMISR_SUCCESS_CODE,
            'message' => 'Success',
        ]);
    }

    public function sendVerify(
        string $code,
        string $to,
        ?string $sender = null,
        ?string $template = null,
    ): SmsmisrResponse {
        $this->verified[] = [
            'code' => $code,
            'to' => $to,
            'sender' => $sender ?? config('smsmisr.sender'),
            'template' => $template,
        ];

        return SmsmisrResponse::fromArray([
            'code' => self::SMSMISR_VERIFY_SUCCESS_CODE,
            'message' => 'Success',
        ]);
    }

    public function balance(): SmsmisrResponse
    {
        $this->balanceChecks++;

        return SmsmisrResponse::fromArray([
            'code' => self::SMSMISR_BALANCE_SUCCESS_CODE,
            'message' => 'Success',
            'balance' => 1000,
        ]);
    }

    public function balanceVerify(): SmsmisrResponse
    {
        $this->balanceVerifyChecks++;

        return SmsmisrResponse::fromArray([
            'code' => self::SMSMISR_BALANCE_SUCCESS_CODE,
            'message' => 'Success',
            'balance' => 1000,
        ]);
    }

    public function health(): bool
    {
        $this->healthChecks++;

        return true;
    }

    public function queue(
        string $message,
        string $to,
        ?string $sender = null,
        int $language = 1,
        ?DateTimeInterface $scheduledAt = null,
    ): void {
        $this->queued[] = [
            'message' => $message,
            'to' => $to,
            'sender' => $sender ?? config('smsmisr.sender'),
            'language' => $language,
            'scheduledAt' => $scheduledAt,
        ];
    }

    public function queueBulk(
        string $message,
        array $recipients,
        ?string $sender = null,
        int $language = 1,
        ?DateTimeInterface $scheduledAt = null,
    ): void {
        $this->queuedBulk[] = [
            'message' => $message,
            'recipients' => $recipients,
            'sender' => $sender ?? config('smsmisr.sender'),
            'language' => $language,
            'scheduledAt' => $scheduledAt,
        ];
    }

    public function queueVerify(
        string $code,
        string $to,
        ?string $sender = null,
        ?string $template = null,
    ): void {
        $this->queuedVerify[] = [
            'code' => $code,
            'to' => $to,
            'sender' => $sender ?? config('smsmisr.sender'),
            'template' => $template,
        ];
    }

    // --- Assertions ---

    public function assertSent(string $to, ?string $message = null): void
    {
        $found = collect($this->sent)->contains(function ($sms) use ($to, $message) {
            return $sms['to'] === $to && ($message === null || $sms['message'] === $message);
        });

        PHPUnit::assertTrue($found, "SMS was not sent to [{$to}]" . ($message ? " with message [{$message}]" : '') . '.');
    }

    public function assertSentCount(int $count): void
    {
        PHPUnit::assertCount($count, $this->sent, "Expected {$count} SMS to be sent, but " . count($this->sent) . ' were sent.');
    }

    public function assertNothingSent(): void
    {
        PHPUnit::assertEmpty($this->sent, count($this->sent) . ' SMS were sent unexpectedly.');
        PHPUnit::assertEmpty($this->verified, count($this->verified) . ' verification SMS were sent unexpectedly.');
        PHPUnit::assertEmpty($this->bulk, count($this->bulk) . ' bulk SMS were sent unexpectedly.');
    }

    public function assertNothingQueued(): void
    {
        PHPUnit::assertEmpty($this->queued, count($this->queued) . ' SMS were queued unexpectedly.');
        PHPUnit::assertEmpty($this->queuedBulk, count($this->queuedBulk) . ' bulk SMS were queued unexpectedly.');
        PHPUnit::assertEmpty($this->queuedVerify, count($this->queuedVerify) . ' verification SMS were queued unexpectedly.');
    }

    public function assertVerifySent(string $to, ?string $code = null): void
    {
        $found = collect($this->verified)->contains(function ($sms) use ($to, $code) {
            return $sms['to'] === $to && ($code === null || $sms['code'] === $code);
        });

        PHPUnit::assertTrue($found, "Verification SMS was not sent to [{$to}]" . ($code ? " with code [{$code}]" : '') . '.');
    }

    public function assertVerifySentCount(int $count): void
    {
        PHPUnit::assertCount($count, $this->verified, "Expected {$count} verification SMS to be sent, but " . count($this->verified) . ' were sent.');
    }

    public function assertBulkSent(?string $message = null): void
    {
        if ($message === null) {
            PHPUnit::assertNotEmpty($this->bulk, 'No bulk SMS were sent.');

            return;
        }

        $found = collect($this->bulk)->contains(fn ($sms) => $sms['message'] === $message);

        PHPUnit::assertTrue($found, "Bulk SMS with message [{$message}] was not sent.");
    }

    public function assertBulkSentCount(int $count): void
    {
        PHPUnit::assertCount($count, $this->bulk, "Expected {$count} bulk SMS to be sent, but " . count($this->bulk) . ' were sent.');
    }

    public function assertBulkSentTo(array $recipients): void
    {
        $found = collect($this->bulk)->contains(function ($sms) use ($recipients) {
            return empty(array_diff($recipients, $sms['recipients']));
        });

        PHPUnit::assertTrue($found, 'Bulk SMS was not sent to the expected recipients.');
    }

    public function assertSentWithSchedule(string $to): void
    {
        $found = collect($this->sent)->contains(function ($sms) use ($to) {
            return $sms['to'] === $to && $sms['scheduledAt'] !== null;
        });

        PHPUnit::assertTrue($found, "No scheduled SMS was sent to [{$to}].");
    }

    public function assertQueued(string $to, ?string $message = null): void
    {
        $found = collect($this->queued)->contains(function ($sms) use ($to, $message) {
            return $sms['to'] === $to && ($message === null || $sms['message'] === $message);
        });

        PHPUnit::assertTrue($found, "SMS was not queued for [{$to}]" . ($message ? " with message [{$message}]" : '') . '.');
    }

    public function assertQueuedCount(int $count): void
    {
        $total = count($this->queued) + count($this->queuedBulk) + count($this->queuedVerify);

        PHPUnit::assertEquals($count, $total, "Expected {$count} queued SMS, but {$total} were queued.");
    }

    public function assertVerifyQueued(string $to, ?string $code = null): void
    {
        $found = collect($this->queuedVerify)->contains(function ($sms) use ($to, $code) {
            return $sms['to'] === $to && ($code === null || $sms['code'] === $code);
        });

        PHPUnit::assertTrue($found, "Verification SMS was not queued for [{$to}]" . ($code ? " with code [{$code}]" : '') . '.');
    }

    public function getSent(): array
    {
        return $this->sent;
    }

    public function getBulk(): array
    {
        return $this->bulk;
    }

    public function getVerified(): array
    {
        return $this->verified;
    }

    public function getQueued(): array
    {
        return $this->queued;
    }

    public function getQueuedBulk(): array
    {
        return $this->queuedBulk;
    }

    public function getQueuedVerify(): array
    {
        return $this->queuedVerify;
    }
}
