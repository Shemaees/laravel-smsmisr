<?php

namespace Ghanem\LaravelSmsmisr\Tests;

use Ghanem\LaravelSmsmisr\Events\LowBalance;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;

class CommandsTest extends TestCase
{
    public function test_balance_command_displays_balance(): void
    {
        Http::fake([
            '*' => Http::sequence()
                ->push(['code' => 6000, 'message' => 'Success', 'balance' => 500])
                ->push(['code' => 6000, 'message' => 'Success', 'balance' => 200]),
        ]);

        $this->artisan('smsmisr:balance')
            ->expectsTable(
                ['Type', 'Balance'],
                [
                    ['SMS', 500],
                    ['Verify/OTP', 200],
                ],
            )
            ->assertExitCode(0);
    }

    public function test_balance_command_handles_error(): void
    {
        Http::fake(['*' => Http::response(['code' => 1902, 'message' => 'Invalid'])]);

        $this->artisan('smsmisr:balance')
            ->assertExitCode(1);
    }

    public function test_check_balance_command_passes_when_above_threshold(): void
    {
        Http::fake([
            '*' => Http::sequence()
                ->push(['code' => 6000, 'message' => 'Success', 'balance' => 500])
                ->push(['code' => 6000, 'message' => 'Success', 'balance' => 200]),
        ]);

        $this->artisan('smsmisr:check-balance')
            ->assertExitCode(0);
    }

    public function test_check_balance_command_fails_when_below_threshold(): void
    {
        Event::fake([LowBalance::class]);

        Http::fake([
            '*' => Http::sequence()
                ->push(['code' => 6000, 'message' => 'Success', 'balance' => 50])
                ->push(['code' => 6000, 'message' => 'Success', 'balance' => 200]),
        ]);

        $this->artisan('smsmisr:check-balance')
            ->assertExitCode(1);

        Event::assertDispatched(LowBalance::class, function ($event) {
            return $event->smsBalance === 50 && $event->threshold === 100;
        });
    }

    public function test_check_balance_command_custom_threshold(): void
    {
        Http::fake([
            '*' => Http::sequence()
                ->push(['code' => 6000, 'message' => 'Success', 'balance' => 500])
                ->push(['code' => 6000, 'message' => 'Success', 'balance' => 200]),
        ]);

        $this->artisan('smsmisr:check-balance --threshold=1000')
            ->assertExitCode(1);
    }

    public function test_check_balance_command_handles_error(): void
    {
        Http::fake(['*' => Http::response(['code' => 1902, 'message' => 'Invalid'])]);

        $this->artisan('smsmisr:check-balance')
            ->assertExitCode(1);
    }

    public function test_check_balance_dispatches_low_balance_event(): void
    {
        Event::fake([LowBalance::class]);

        Http::fake([
            '*' => Http::sequence()
                ->push(['code' => 6000, 'message' => 'Success', 'balance' => 10])
                ->push(['code' => 6000, 'message' => 'Success', 'balance' => 5]),
        ]);

        $this->artisan('smsmisr:check-balance');

        Event::assertDispatched(LowBalance::class, function ($event) {
            return $event->smsBalance === 10
                && $event->verifyBalance === 5
                && $event->threshold === 100;
        });
    }
}
