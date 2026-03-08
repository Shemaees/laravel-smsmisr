<?php

namespace Ghanem\LaravelSmsmisr\Tests;

use Ghanem\LaravelSmsmisr\Events\SmsFailed;
use Ghanem\LaravelSmsmisr\Events\SmsSending;
use Ghanem\LaravelSmsmisr\Events\SmsSent;
use Ghanem\LaravelSmsmisr\Exceptions\SmsmisrApiException;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;

class EventsTest extends TestCase
{
    public function test_sms_sending_event_is_dispatched_before_send(): void
    {
        Event::fake([SmsSending::class]);
        Http::fake(['*' => Http::response(['code' => 1901, 'message' => 'Success'])]);

        app('smsmisr')->send('Hello', '201010101010');

        Event::assertDispatched(SmsSending::class, function ($event) {
            return $event->to === '201010101010'
                && $event->message === 'Hello'
                && $event->type === 'sms';
        });
    }

    public function test_sms_sent_event_is_dispatched_after_success(): void
    {
        Event::fake([SmsSent::class]);
        Http::fake(['*' => Http::response(['code' => 1901, 'message' => 'Success'])]);

        app('smsmisr')->send('Hello', '201010101010');

        Event::assertDispatched(SmsSent::class, function ($event) {
            return $event->to === '201010101010'
                && $event->message === 'Hello'
                && $event->response->isSuccessful()
                && $event->type === 'sms';
        });
    }

    public function test_sms_failed_event_is_dispatched_on_api_error(): void
    {
        Event::fake([SmsFailed::class]);
        Http::fake(['*' => Http::response(['code' => 1906, 'message' => 'Invalid'])]);

        try {
            app('smsmisr')->send('Hello', '201010101010');
        } catch (SmsmisrApiException $e) {
            // expected
        }

        Event::assertDispatched(SmsFailed::class, function ($event) {
            return $event->to === '201010101010'
                && $event->exception instanceof SmsmisrApiException
                && $event->type === 'sms';
        });
    }

    public function test_sms_sending_event_dispatched_for_otp(): void
    {
        Event::fake([SmsSending::class]);
        Http::fake(['*' => Http::response(['code' => 4901, 'message' => 'Success'])]);

        app('smsmisr')->sendVerify('1234', '201010101010', null, 'template');

        Event::assertDispatched(SmsSending::class, function ($event) {
            return $event->to === '201010101010'
                && $event->type === 'otp';
        });
    }

    public function test_sms_sent_event_dispatched_for_otp(): void
    {
        Event::fake([SmsSent::class]);
        Http::fake(['*' => Http::response(['code' => 4901, 'message' => 'Success'])]);

        app('smsmisr')->sendVerify('1234', '201010101010', null, 'template');

        Event::assertDispatched(SmsSent::class, function ($event) {
            return $event->to === '201010101010'
                && $event->type === 'otp'
                && $event->response->code === 4901;
        });
    }

    public function test_sms_failed_event_dispatched_for_otp(): void
    {
        Event::fake([SmsFailed::class]);
        Http::fake(['*' => Http::response(['code' => 4906, 'message' => 'Invalid'])]);

        try {
            app('smsmisr')->sendVerify('1234', '201010101010');
        } catch (SmsmisrApiException $e) {
            // expected
        }

        Event::assertDispatched(SmsFailed::class, function ($event) {
            return $event->type === 'otp';
        });
    }

    public function test_events_carry_sender_info(): void
    {
        Event::fake([SmsSending::class, SmsSent::class]);
        Http::fake(['*' => Http::response(['code' => 1901, 'message' => 'Success'])]);

        app('smsmisr')->send('Hello', '201010101010', 'MySender');

        Event::assertDispatched(SmsSending::class, function ($event) {
            return $event->sender === 'MySender';
        });

        Event::assertDispatched(SmsSent::class, function ($event) {
            return $event->sender === 'MySender';
        });
    }

    public function test_no_sent_event_on_failure(): void
    {
        Event::fake([SmsSent::class, SmsFailed::class]);
        Http::fake(['*' => Http::response(['code' => 1906, 'message' => 'Error'])]);

        try {
            app('smsmisr')->send('Hello', '201010101010');
        } catch (SmsmisrApiException $e) {
            // expected
        }

        Event::assertNotDispatched(SmsSent::class);
        Event::assertDispatched(SmsFailed::class);
    }
}
