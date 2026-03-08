<?php

namespace Ghanem\LaravelSmsmisr\Tests;

use Ghanem\LaravelSmsmisr\Exceptions\SmsmisrApiException;
use Ghanem\LaravelSmsmisr\SmsmisrChannel;
use Ghanem\LaravelSmsmisr\SmsmisrMessage;
use Ghanem\LaravelSmsmisr\SmsmisrResponse;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;

class SmsmisrChannelTest extends TestCase
{
    public function test_it_sends_notification_successfully(): void
    {
        Http::fake(['*' => Http::response(['code' => 1901, 'message' => 'Success'])]);

        $channel = app(SmsmisrChannel::class);
        $response = $channel->send(new TestNotifiable(), new TestNotification());

        $this->assertInstanceOf(SmsmisrResponse::class, $response);
        $this->assertTrue($response->isSuccessful());
    }

    public function test_it_throws_on_api_failure(): void
    {
        Http::fake(['*' => Http::response(['code' => 1906, 'message' => 'Failed'])]);

        $channel = app(SmsmisrChannel::class);

        $this->expectException(SmsmisrApiException::class);

        $channel->send(new TestNotifiable(), new TestNotification());
    }

    public function test_it_sends_verification_notification(): void
    {
        Http::fake(['*' => Http::response(['code' => 4901, 'message' => 'Success'])]);

        $channel = app(SmsmisrChannel::class);
        $response = $channel->send(new TestNotifiable(), new TestVerificationNotification());

        $this->assertTrue($response->isSuccessful());
        $this->assertEquals(4901, $response->code);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'OTP');
        });
    }

    public function test_it_sends_scheduled_notification(): void
    {
        Http::fake(['*' => Http::response(['code' => 1901, 'message' => 'Success'])]);

        $channel = app(SmsmisrChannel::class);
        $response = $channel->send(new TestNotifiable(), new TestScheduledNotification());

        $this->assertTrue($response->isSuccessful());

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'DelayUntil=');
        });
    }
}

class TestNotifiable
{
    public string $phone = '201010101010';
}

class TestNotification extends Notification
{
    public function toSmsmisr($notifiable): SmsmisrMessage
    {
        return new SmsmisrMessage('Test message', $notifiable->phone);
    }
}

class TestVerificationNotification extends Notification
{
    public function toSmsmisr($notifiable): SmsmisrMessage
    {
        return (new SmsmisrMessage())
            ->to($notifiable->phone)
            ->asVerification('1234', 'my-template');
    }
}

class TestScheduledNotification extends Notification
{
    public function toSmsmisr($notifiable): SmsmisrMessage
    {
        return (new SmsmisrMessage('Scheduled message', $notifiable->phone))
            ->scheduledAt(new \DateTime('2026-04-01 10:00:00'));
    }
}
