<?php

namespace Ghanem\LaravelSmsmisr\Tests;

use Illuminate\Support\Facades\Http;

class HealthCheckTest extends TestCase
{
    public function test_health_returns_true_when_api_is_reachable(): void
    {
        Http::fake(['*' => Http::response(['code' => 6000, 'message' => 'Success', 'balance' => 100])]);

        $this->assertTrue(app('smsmisr')->health());
    }

    public function test_health_returns_false_when_api_fails(): void
    {
        Http::fake(['*' => Http::response(['code' => 1902, 'message' => 'Invalid credentials'])]);

        $this->assertFalse(app('smsmisr')->health());
    }

    public function test_health_returns_false_on_connection_error(): void
    {
        Http::fake(['*' => fn () => throw new \Exception('Connection refused')]);

        $this->assertFalse(app('smsmisr')->health());
    }

    public function test_fake_health_returns_true(): void
    {
        \Ghanem\LaravelSmsmisr\Facades\Smsmisr::fake();

        $this->assertTrue(\Ghanem\LaravelSmsmisr\Facades\Smsmisr::health());
    }
}
