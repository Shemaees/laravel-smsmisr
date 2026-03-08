<?php

namespace Ghanem\LaravelSmsmisr\Tests;

use Ghanem\LaravelSmsmisr\Facades\Smsmisr;
use Ghanem\LaravelSmsmisr\SmsmisrResponse;
use Illuminate\Support\Facades\Http;

class BulkSmsTest extends TestCase
{
    public function test_send_bulk_returns_response(): void
    {
        Http::fake(['*' => Http::response(['code' => 1901, 'message' => 'Success'])]);

        $response = app('smsmisr')->sendBulk('Hello', ['201012345678', '201112345678']);

        $this->assertInstanceOf(SmsmisrResponse::class, $response);
        $this->assertTrue($response->isSuccessful());
    }

    public function test_send_bulk_joins_recipients(): void
    {
        Http::fake(['*' => Http::response(['code' => 1901, 'message' => 'Success'])]);

        app('smsmisr')->sendBulk('Hello', ['201012345678', '201112345678']);

        Http::assertSent(function ($request) {
            $url = $request->url();

            return str_contains($url, '201012345678') && str_contains($url, '201112345678');
        });
    }

    public function test_send_bulk_normalizes_numbers(): void
    {
        Http::fake(['*' => Http::response(['code' => 1901, 'message' => 'Success'])]);

        app('smsmisr')->sendBulk('Hello', ['01012345678', '01112345678']);

        Http::assertSent(function ($request) {
            $url = $request->url();

            return str_contains($url, '201012345678') && str_contains($url, '201112345678');
        });
    }

    public function test_send_bulk_with_schedule(): void
    {
        Http::fake(['*' => Http::response(['code' => 1901, 'message' => 'Success'])]);

        $scheduledAt = new \DateTime('2026-04-01 10:00:00');
        $response = app('smsmisr')->sendBulk('Hello', ['201012345678'], scheduledAt: $scheduledAt);

        $this->assertTrue($response->isSuccessful());

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'DelayUntil=');
        });
    }

    // --- Fake assertions ---

    public function test_fake_assert_bulk_sent(): void
    {
        Smsmisr::fake();

        Smsmisr::sendBulk('Hello', ['201012345678', '201112345678']);

        Smsmisr::assertBulkSent();
        Smsmisr::assertBulkSent('Hello');
        Smsmisr::assertBulkSentCount(1);
    }

    public function test_fake_assert_bulk_sent_to(): void
    {
        Smsmisr::fake();

        Smsmisr::sendBulk('Hello', ['201012345678', '201112345678']);

        Smsmisr::assertBulkSentTo(['201012345678', '201112345678']);
    }

    public function test_fake_get_bulk(): void
    {
        Smsmisr::fake();

        Smsmisr::sendBulk('First', ['201012345678']);
        Smsmisr::sendBulk('Second', ['201112345678']);

        $bulk = Smsmisr::getBulk();

        $this->assertCount(2, $bulk);
        $this->assertEquals('First', $bulk[0]['message']);
        $this->assertEquals('Second', $bulk[1]['message']);
    }

    public function test_fake_nothing_sent_includes_bulk(): void
    {
        Smsmisr::fake();

        Smsmisr::assertNothingSent();
    }
}
