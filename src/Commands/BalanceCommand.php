<?php

namespace Ghanem\LaravelSmsmisr\Commands;

use Ghanem\LaravelSmsmisr\Smsmisr;
use Illuminate\Console\Command;

class BalanceCommand extends Command
{
    protected $signature = 'smsmisr:balance';

    protected $description = 'Check SMS Misr account balance';

    public function handle(Smsmisr $smsmisr): int
    {
        try {
            $sms = $smsmisr->balance();
            $verify = $smsmisr->balanceVerify();

            $this->table(
                ['Type', 'Balance'],
                [
                    ['SMS', $sms->raw['balance'] ?? 'N/A'],
                    ['Verify/OTP', $verify->raw['balance'] ?? 'N/A'],
                ],
            );

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error("Failed to fetch balance: {$e->getMessage()}");

            return self::FAILURE;
        }
    }
}
