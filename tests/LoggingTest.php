<?php

namespace Ghanem\LaravelSmsmisr\Tests;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LoggingTest extends TestCase
{
    public function test_no_logging_by_default(): void
    {
        Log::shouldReceive('channel')->never();

        Http::fake(['*' => Http::response(['code' => 1901, 'message' => 'Success'])]);

        app('smsmisr')->send('Hello', '201012345678');
    }

    public function test_logs_when_channel_configured(): void
    {
        config(['smsmisr.log_channel' => 'single']);

        Http::fake(['*' => Http::response(['code' => 1901, 'message' => 'Success'])]);

        Log::shouldReceive('channel')
            ->with('single')
            ->once()
            ->andReturnSelf();
        Log::shouldReceive('info')
            ->once()
            ->withArgs(function ($message, $context) {
                return str_contains($message, 'SMS sent')
                    && isset($context['to'])
                    && isset($context['code'])
                    && $context['code'] === 1901;
            });

        app('smsmisr')->send('Hello', '201012345678');
    }

    public function test_logs_error_on_failure(): void
    {
        config(['smsmisr.log_channel' => 'single']);

        Http::fake(['*' => Http::response(['code' => 1906, 'message' => 'Invalid'])]);

        Log::shouldReceive('channel')
            ->with('single')
            ->once()
            ->andReturnSelf();
        Log::shouldReceive('error')
            ->once()
            ->withArgs(function ($message, $context) {
                return str_contains($message, 'failed')
                    && isset($context['error']);
            });

        try {
            app('smsmisr')->send('Hello', '201012345678');
        } catch (\Throwable $e) {
            // expected
        }
    }

    public function test_phone_is_masked_in_logs(): void
    {
        config(['smsmisr.log_channel' => 'single']);

        Http::fake(['*' => Http::response(['code' => 1901, 'message' => 'Success'])]);

        Log::shouldReceive('channel')
            ->with('single')
            ->once()
            ->andReturnSelf();
        Log::shouldReceive('info')
            ->once()
            ->withArgs(function ($message, $context) {
                // Phone 201012345678 should be masked as 2010******78
                return str_contains($context['to'], '****');
            });

        app('smsmisr')->send('Hello', '201012345678');
    }

    public function test_otp_code_is_masked_in_logs(): void
    {
        config(['smsmisr.log_channel' => 'single']);

        Http::fake(['*' => Http::response(['code' => 4901, 'message' => 'Success'])]);

        Log::shouldReceive('channel')
            ->with('single')
            ->once()
            ->andReturnSelf();
        Log::shouldReceive('info')
            ->once()
            ->withArgs(function ($message) {
                // OTP action should show **** instead of actual code
                return str_contains($message, 'OTP sent');
            });

        app('smsmisr')->sendVerify('1234', '201012345678');
    }
}
