<?php

namespace Ghanem\LaravelSmsmisr\Tests;

use Ghanem\LaravelSmsmisr\Facades\Smsmisr;
use Ghanem\LaravelSmsmisr\Jobs\SendBulkSmsJob;
use Ghanem\LaravelSmsmisr\Jobs\SendSmsJob;
use Ghanem\LaravelSmsmisr\Jobs\SendVerifySmsJob;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;

class QueueTest extends TestCase
{
    public function test_queue_dispatches_send_sms_job(): void
    {
        Queue::fake();

        app('smsmisr')->queue('Hello', '201012345678');

        Queue::assertPushed(SendSmsJob::class, function ($job) {
            return $job->message === 'Hello' && $job->to === '201012345678';
        });
    }

    public function test_queue_bulk_dispatches_bulk_sms_job(): void
    {
        Queue::fake();

        app('smsmisr')->queueBulk('Hello', ['201012345678', '201112345678']);

        Queue::assertPushed(SendBulkSmsJob::class, function ($job) {
            return $job->message === 'Hello' && count($job->recipients) === 2;
        });
    }

    public function test_queue_verify_dispatches_verify_sms_job(): void
    {
        Queue::fake();

        app('smsmisr')->queueVerify('1234', '201012345678', null, 'template');

        Queue::assertPushed(SendVerifySmsJob::class, function ($job) {
            return $job->code === '1234' && $job->to === '201012345678' && $job->template === 'template';
        });
    }

    public function test_send_sms_job_calls_send(): void
    {
        Http::fake(['*' => Http::response(['code' => 1901, 'message' => 'Success'])]);

        $job = new SendSmsJob('Hello', '201012345678');
        $job->handle(app('smsmisr'));

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'SMS') && str_contains($request->url(), 'Hello');
        });
    }

    public function test_send_bulk_sms_job_calls_send_bulk(): void
    {
        Http::fake(['*' => Http::response(['code' => 1901, 'message' => 'Success'])]);

        $job = new SendBulkSmsJob('Hello', ['201012345678', '201112345678']);
        $job->handle(app('smsmisr'));

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'SMS');
        });
    }

    public function test_send_verify_sms_job_calls_send_verify(): void
    {
        Http::fake(['*' => Http::response(['code' => 4901, 'message' => 'Success'])]);

        $job = new SendVerifySmsJob('1234', '201012345678', null, 'template');
        $job->handle(app('smsmisr'));

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'OTP');
        });
    }

    public function test_queue_with_custom_sender(): void
    {
        Queue::fake();

        app('smsmisr')->queue('Hello', '201012345678', 'CustomSender');

        Queue::assertPushed(SendSmsJob::class, function ($job) {
            return $job->sender === 'CustomSender';
        });
    }

    public function test_queue_with_schedule(): void
    {
        Queue::fake();

        $date = new \DateTime('2026-04-01');
        app('smsmisr')->queue('Hello', '201012345678', scheduledAt: $date);

        Queue::assertPushed(SendSmsJob::class, function ($job) {
            return $job->scheduledAt !== null;
        });
    }

    // --- Fake assertions ---

    public function test_fake_assert_queued(): void
    {
        Smsmisr::fake();

        Smsmisr::queue('Hello', '201012345678');

        Smsmisr::assertQueued('201012345678');
        Smsmisr::assertQueued('201012345678', 'Hello');
        Smsmisr::assertQueuedCount(1);
    }

    public function test_fake_assert_verify_queued(): void
    {
        Smsmisr::fake();

        Smsmisr::queueVerify('1234', '201012345678');

        Smsmisr::assertVerifyQueued('201012345678');
        Smsmisr::assertVerifyQueued('201012345678', '1234');
    }

    public function test_fake_assert_nothing_queued(): void
    {
        Smsmisr::fake();

        Smsmisr::assertNothingQueued();
    }

    public function test_fake_get_queued(): void
    {
        Smsmisr::fake();

        Smsmisr::queue('First', '201012345678');
        Smsmisr::queue('Second', '201112345678');

        $queued = Smsmisr::getQueued();

        $this->assertCount(2, $queued);
        $this->assertEquals('First', $queued[0]['message']);
    }
}
