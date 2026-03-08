<?php

namespace Ghanem\LaravelSmsmisr\Tests;

use Ghanem\LaravelSmsmisr\SmsmisrServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            SmsmisrServiceProvider::class,
        ];
    }

    protected function getPackageAliases($app): array
    {
        return [
            'Smsmisr' => \Ghanem\LaravelSmsmisr\Facades\Smsmisr::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('smsmisr.username', 'test_user');
        $app['config']->set('smsmisr.password', 'test_pass');
        $app['config']->set('smsmisr.sender', 'TestSender');
        $app['config']->set('smsmisr.endpoint', 'https://smsmisr.com/api/');
        $app['config']->set('smsmisr.environment', 2);
    }
}
