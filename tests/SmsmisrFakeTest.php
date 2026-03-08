<?php

namespace Ghanem\LaravelSmsmisr\Tests;

use Ghanem\LaravelSmsmisr\Facades\Smsmisr;
use Ghanem\LaravelSmsmisr\SmsmisrFake;
use Ghanem\LaravelSmsmisr\SmsmisrResponse;

class SmsmisrFakeTest extends TestCase
{
    public function test_fake_replaces_instance(): void
    {
        $fake = Smsmisr::fake();

        $this->assertInstanceOf(SmsmisrFake::class, $fake);
        $this->assertInstanceOf(SmsmisrFake::class, app('smsmisr'));
    }

    public function test_fake_send_returns_success(): void
    {
        Smsmisr::fake();

        $response = Smsmisr::send('Hello', '201010101010');

        $this->assertInstanceOf(SmsmisrResponse::class, $response);
        $this->assertTrue($response->isSuccessful());
    }

    public function test_fake_send_verify_returns_success(): void
    {
        Smsmisr::fake();

        $response = Smsmisr::sendVerify('1234', '201010101010');

        $this->assertTrue($response->isSuccessful());
        $this->assertEquals(4901, $response->code);
    }

    public function test_assert_sent(): void
    {
        Smsmisr::fake();

        Smsmisr::send('Hello world', '201010101010');

        Smsmisr::assertSent('201010101010');
        Smsmisr::assertSent('201010101010', 'Hello world');
    }

    public function test_assert_sent_count(): void
    {
        Smsmisr::fake();

        Smsmisr::send('Hello', '201010101010');
        Smsmisr::send('World', '201020202020');

        Smsmisr::assertSentCount(2);
    }

    public function test_assert_nothing_sent(): void
    {
        Smsmisr::fake();

        Smsmisr::assertNothingSent();
    }

    public function test_assert_verify_sent(): void
    {
        Smsmisr::fake();

        Smsmisr::sendVerify('1234', '201010101010', null, 'template');

        Smsmisr::assertVerifySent('201010101010');
        Smsmisr::assertVerifySent('201010101010', '1234');
    }

    public function test_assert_verify_sent_count(): void
    {
        Smsmisr::fake();

        Smsmisr::sendVerify('1234', '201010101010');
        Smsmisr::sendVerify('5678', '201020202020');

        Smsmisr::assertVerifySentCount(2);
    }

    public function test_assert_sent_with_schedule(): void
    {
        Smsmisr::fake();

        Smsmisr::send('Hello', '201010101010', scheduledAt: new \DateTime('2026-04-01'));

        Smsmisr::assertSentWithSchedule('201010101010');
    }

    public function test_get_sent_returns_all_sent_sms(): void
    {
        Smsmisr::fake();

        Smsmisr::send('First', '201010101010');
        Smsmisr::send('Second', '201020202020');

        $sent = Smsmisr::getSent();

        $this->assertCount(2, $sent);
        $this->assertEquals('First', $sent[0]['message']);
        $this->assertEquals('Second', $sent[1]['message']);
    }

    public function test_get_verified_returns_all_verified_sms(): void
    {
        Smsmisr::fake();

        Smsmisr::sendVerify('1111', '201010101010');
        Smsmisr::sendVerify('2222', '201020202020');

        $verified = Smsmisr::getVerified();

        $this->assertCount(2, $verified);
        $this->assertEquals('1111', $verified[0]['code']);
        $this->assertEquals('2222', $verified[1]['code']);
    }

    public function test_fake_balance_returns_success(): void
    {
        Smsmisr::fake();

        $response = Smsmisr::balance();

        $this->assertTrue($response->isSuccessful());
        $this->assertEquals(1000, $response->raw['balance']);
    }

    public function test_fake_balance_verify_returns_success(): void
    {
        Smsmisr::fake();

        $response = Smsmisr::balanceVerify();

        $this->assertTrue($response->isSuccessful());
    }

    public function test_fake_does_not_make_http_requests(): void
    {
        Smsmisr::fake();

        // No Http::fake() — if a real request is made, it would fail
        Smsmisr::send('Hello', '201010101010');
        Smsmisr::sendVerify('1234', '201010101010');
        Smsmisr::balance();
        Smsmisr::balanceVerify();

        Smsmisr::assertSentCount(1);
        Smsmisr::assertVerifySentCount(1);
    }
}
