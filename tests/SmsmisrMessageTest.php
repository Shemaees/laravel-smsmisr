<?php

namespace Ghanem\LaravelSmsmisr\Tests;

use Ghanem\LaravelSmsmisr\SmsmisrMessage;

class SmsmisrMessageTest extends TestCase
{
    public function test_it_can_be_instantiated_with_message_and_to(): void
    {
        $msg = new SmsmisrMessage('Hello', '201010101010');

        $this->assertEquals('Hello', $msg->getMessage());
        $this->assertEquals('201010101010', $msg->getTo());
    }

    public function test_it_can_set_message_fluently(): void
    {
        $msg = (new SmsmisrMessage())->message('Test message');

        $this->assertEquals('Test message', $msg->getMessage());
    }

    public function test_it_can_set_to_fluently(): void
    {
        $msg = (new SmsmisrMessage())->to('201234567890');

        $this->assertEquals('201234567890', $msg->getTo());
    }

    public function test_it_can_set_sender_fluently(): void
    {
        $msg = (new SmsmisrMessage())->sender('MySender');

        $this->assertEquals('MySender', $msg->getSender());
    }

    public function test_it_defaults_sender_from_config(): void
    {
        $msg = new SmsmisrMessage('Hello', '201010101010');

        $this->assertEquals('TestSender', $msg->getSender());
    }

    public function test_it_supports_unicode_by_default(): void
    {
        $msg = new SmsmisrMessage('Hello', '201010101010');

        $this->assertTrue($msg->isUnicode());
    }

    public function test_it_can_disable_unicode(): void
    {
        $msg = (new SmsmisrMessage())->unicode(false);

        $this->assertFalse($msg->isUnicode());
    }

    public function test_it_returns_message_as_is_when_unicode(): void
    {
        $msg = new SmsmisrMessage('مرحبا بالعالم', '201010101010');

        $this->assertEquals('مرحبا بالعالم', $msg->getMessage());
    }

    public function test_it_converts_to_gsm_when_not_unicode(): void
    {
        $msg = (new SmsmisrMessage('Hello @world', '201010101010'))->unicode(false);

        $message = $msg->getMessage();
        $this->assertNotEquals('Hello @world', $message);
        $this->assertStringContainsString('Hello', $message);
    }

    public function test_magic_get_returns_message(): void
    {
        $msg = new SmsmisrMessage('Hello', '201010101010');

        $this->assertEquals('Hello', $msg->message);
        $this->assertEquals('201010101010', $msg->to);
        $this->assertEquals('TestSender', $msg->sender);
    }

    public function test_magic_get_throws_on_invalid_property(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $msg = new SmsmisrMessage();
        $msg->invalid;
    }

    public function test_fluent_chaining(): void
    {
        $msg = (new SmsmisrMessage())
            ->message('Chained message')
            ->to('201234567890')
            ->sender('ChainSender')
            ->unicode(false);

        $this->assertEquals('201234567890', $msg->getTo());
        $this->assertEquals('ChainSender', $msg->getSender());
        $this->assertFalse($msg->isUnicode());
    }

    // --- Verification / OTP ---

    public function test_it_is_not_verification_by_default(): void
    {
        $msg = new SmsmisrMessage('Hello', '201010101010');

        $this->assertFalse($msg->isVerification());
        $this->assertNull($msg->getVerificationCode());
        $this->assertNull($msg->getTemplate());
    }

    public function test_as_verification_sets_otp_properties(): void
    {
        $msg = (new SmsmisrMessage())
            ->to('201010101010')
            ->asVerification('1234', 'my-template');

        $this->assertTrue($msg->isVerification());
        $this->assertEquals('1234', $msg->getVerificationCode());
        $this->assertEquals('my-template', $msg->getTemplate());
    }

    public function test_as_verification_without_template(): void
    {
        $msg = (new SmsmisrMessage())->asVerification('5678');

        $this->assertTrue($msg->isVerification());
        $this->assertEquals('5678', $msg->getVerificationCode());
        $this->assertNull($msg->getTemplate());
    }

    // --- Scheduled SMS ---

    public function test_it_has_no_schedule_by_default(): void
    {
        $msg = new SmsmisrMessage('Hello', '201010101010');

        $this->assertNull($msg->getScheduledAt());
    }

    public function test_scheduled_at_sets_datetime(): void
    {
        $date = new \DateTime('2026-04-01 10:00:00');
        $msg = (new SmsmisrMessage('Hello', '201010101010'))->scheduledAt($date);

        $this->assertSame($date, $msg->getScheduledAt());
    }
}
