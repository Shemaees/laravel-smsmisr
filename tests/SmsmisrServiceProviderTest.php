<?php

namespace Ghanem\LaravelSmsmisr\Tests;

use Ghanem\LaravelSmsmisr\Smsmisr;

class SmsmisrServiceProviderTest extends TestCase
{
    public function test_it_registers_smsmisr_singleton(): void
    {
        $smsmisr = $this->app->make('smsmisr');

        $this->assertInstanceOf(Smsmisr::class, $smsmisr);
    }

    public function test_it_returns_same_instance(): void
    {
        $instance1 = $this->app->make('smsmisr');
        $instance2 = $this->app->make('smsmisr');

        $this->assertSame($instance1, $instance2);
    }

    public function test_it_resolves_by_class_name(): void
    {
        $smsmisr = $this->app->make(Smsmisr::class);

        $this->assertInstanceOf(Smsmisr::class, $smsmisr);
    }

    public function test_config_is_loaded(): void
    {
        $this->assertEquals('test_user', config('smsmisr.username'));
        $this->assertEquals('test_pass', config('smsmisr.password'));
        $this->assertEquals('TestSender', config('smsmisr.sender'));
    }

    public function test_default_config_values(): void
    {
        $this->assertNotNull(config('smsmisr.endpoint'));
        $this->assertNotNull(config('smsmisr.environment'));
    }
}
