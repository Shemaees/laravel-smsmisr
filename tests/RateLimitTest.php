<?php

namespace Ghanem\LaravelSmsmisr\Tests;

use Ghanem\LaravelSmsmisr\Exceptions\SmsmisrRateLimitException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;

class RateLimitTest extends TestCase
{
    public function test_no_rate_limit_by_default(): void
    {
        Http::fake(['*' => Http::response(['code' => 1901, 'message' => 'Success'])]);

        // Should not throw
        for ($i = 0; $i < 5; $i++) {
            app('smsmisr')->send('Hello', '201012345678');
        }

        $this->assertTrue(true);
    }

    public function test_rate_limit_throws_when_exceeded(): void
    {
        config(['smsmisr.rate_limit' => 2]);

        Http::fake(['*' => Http::response(['code' => 1901, 'message' => 'Success'])]);

        app('smsmisr')->send('Hello', '201012345678');
        app('smsmisr')->send('Hello', '201012345678');

        $this->expectException(SmsmisrRateLimitException::class);
        $this->expectExceptionMessage('Rate limit exceeded');

        app('smsmisr')->send('Hello', '201012345678');
    }

    public function test_rate_limit_applies_to_verify(): void
    {
        config(['smsmisr.rate_limit' => 1]);

        Http::fake(['*' => Http::response(['code' => 4901, 'message' => 'Success'])]);

        app('smsmisr')->sendVerify('1234', '201012345678');

        $this->expectException(SmsmisrRateLimitException::class);

        app('smsmisr')->sendVerify('5678', '201012345678');
    }

    public function test_rate_limit_applies_to_bulk(): void
    {
        config(['smsmisr.rate_limit' => 1]);

        Http::fake(['*' => Http::response(['code' => 1901, 'message' => 'Success'])]);

        app('smsmisr')->sendBulk('Hello', ['201012345678']);

        $this->expectException(SmsmisrRateLimitException::class);

        app('smsmisr')->sendBulk('Hello', ['201012345678']);
    }

    protected function setUp(): void
    {
        parent::setUp();

        RateLimiter::clear('smsmisr');
    }
}
