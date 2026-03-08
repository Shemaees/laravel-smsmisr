<?php

namespace Ghanem\LaravelSmsmisr\Facades;

use Ghanem\LaravelSmsmisr\SmsmisrFake;
use Ghanem\LaravelSmsmisr\SmsmisrResponse;
use Illuminate\Support\Facades\Facade;

/**
 * @method static SmsmisrResponse send(string $message, string $to, ?string $sender = null, int $language = 1, ?\DateTimeInterface $scheduledAt = null)
 * @method static SmsmisrResponse sendBulk(string $message, array $recipients, ?string $sender = null, int $language = 1, ?\DateTimeInterface $scheduledAt = null)
 * @method static SmsmisrResponse sendVerify(string $code, string $to, ?string $sender = null, ?string $template = null)
 * @method static SmsmisrResponse balance()
 * @method static SmsmisrResponse balanceVerify()
 * @method static bool health()
 * @method static bool isSuccessful(?array $response)
 * @method static void queue(string $message, string $to, ?string $sender = null, int $language = 1, ?\DateTimeInterface $scheduledAt = null)
 * @method static void queueBulk(string $message, array $recipients, ?string $sender = null, int $language = 1, ?\DateTimeInterface $scheduledAt = null)
 * @method static void queueVerify(string $code, string $to, ?string $sender = null, ?string $template = null)
 *
 * @method static void assertSent(string $to, ?string $message = null)
 * @method static void assertSentCount(int $count)
 * @method static void assertNothingSent()
 * @method static void assertNothingQueued()
 * @method static void assertVerifySent(string $to, ?string $code = null)
 * @method static void assertVerifySentCount(int $count)
 * @method static void assertBulkSent(?string $message = null)
 * @method static void assertBulkSentCount(int $count)
 * @method static void assertBulkSentTo(array $recipients)
 * @method static void assertSentWithSchedule(string $to)
 * @method static void assertQueued(string $to, ?string $message = null)
 * @method static void assertQueuedCount(int $count)
 * @method static void assertVerifyQueued(string $to, ?string $code = null)
 * @method static array getSent()
 * @method static array getBulk()
 * @method static array getVerified()
 * @method static array getQueued()
 * @method static array getQueuedBulk()
 * @method static array getQueuedVerify()
 *
 * @see \Ghanem\LaravelSmsmisr\Smsmisr
 * @see \Ghanem\LaravelSmsmisr\SmsmisrFake
 */
class Smsmisr extends Facade
{
    /**
     * Replace the bound instance with a fake for testing.
     */
    public static function fake(): SmsmisrFake
    {
        $fake = new SmsmisrFake();

        static::swap($fake);

        return $fake;
    }

    protected static function getFacadeAccessor(): string
    {
        return 'smsmisr';
    }
}
