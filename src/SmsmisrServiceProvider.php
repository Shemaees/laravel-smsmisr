<?php

namespace Ghanem\LaravelSmsmisr;

use Ghanem\LaravelSmsmisr\Commands\BalanceCommand;
use Ghanem\LaravelSmsmisr\Commands\CheckBalanceCommand;
use Illuminate\Support\ServiceProvider;

class SmsmisrServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->publishes([
            $this->configPath() => config_path('smsmisr.php'),
        ], 'smsmisr-config');

        if ($this->app->runningInConsole()) {
            $this->commands([
                BalanceCommand::class,
                CheckBalanceCommand::class,
            ]);
        }
    }

    public function register(): void
    {
        $this->mergeConfigFrom($this->configPath(), 'smsmisr');

        $this->app->singleton('smsmisr', function () {
            return new Smsmisr();
        });

        $this->app->alias('smsmisr', Smsmisr::class);
    }

    public function provides(): array
    {
        return ['smsmisr', Smsmisr::class];
    }

    protected function configPath(): string
    {
        return __DIR__ . '/../config/smsmisr.php';
    }
}
