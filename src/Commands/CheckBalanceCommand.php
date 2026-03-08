<?php

namespace Ghanem\LaravelSmsmisr\Commands;

use Ghanem\LaravelSmsmisr\Events\LowBalance;
use Ghanem\LaravelSmsmisr\Smsmisr;
use Illuminate\Console\Command;

class CheckBalanceCommand extends Command
{
    protected $signature = 'smsmisr:check-balance
                            {--threshold= : Override the low balance threshold}';

    protected $description = 'Check SMS Misr balance and alert if below threshold';

    public function handle(Smsmisr $smsmisr): int
    {
        $threshold = (int) ($this->option('threshold') ?? config('smsmisr.low_balance_threshold', 100));

        try {
            $sms = $smsmisr->balance();
            $verify = $smsmisr->balanceVerify();

            $smsBalance = $sms->raw['balance'] ?? 0;
            $verifyBalance = $verify->raw['balance'] ?? 0;

            $this->info("SMS Balance: {$smsBalance}");
            $this->info("Verify Balance: {$verifyBalance}");
            $this->info("Threshold: {$threshold}");

            $alerts = [];

            if ($smsBalance < $threshold) {
                $alerts[] = "SMS balance ({$smsBalance}) is below threshold ({$threshold})";
            }

            if ($verifyBalance < $threshold) {
                $alerts[] = "Verify balance ({$verifyBalance}) is below threshold ({$threshold})";
            }

            if (!empty($alerts)) {
                foreach ($alerts as $alert) {
                    $this->warn($alert);
                }

                LowBalance::dispatch($smsBalance, $verifyBalance, $threshold);

                return self::FAILURE;
            }

            $this->info('Balance is OK.');

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error("Failed to check balance: {$e->getMessage()}");

            return self::FAILURE;
        }
    }
}
