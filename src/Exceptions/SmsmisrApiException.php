<?php

namespace Ghanem\LaravelSmsmisr\Exceptions;

class SmsmisrApiException extends SmsmisrException
{
    protected static array $errorMessages = [
        1902 => 'Invalid username or password',
        1903 => 'Insufficient balance',
        1904 => 'Invalid sender',
        1905 => 'Invalid mobile number',
        1906 => 'Invalid message',
        1907 => 'Duplicate message ID',
        1908 => 'Invalid schedule date',
        1909 => 'Invalid environment',
        4902 => 'OTP: Invalid username or password',
        4903 => 'OTP: Insufficient balance',
        4904 => 'OTP: Invalid sender',
        4905 => 'OTP: Invalid mobile number',
        4906 => 'OTP: Invalid template',
        4907 => 'OTP: Invalid OTP code',
    ];

    public static function fromResponse(array $response): static
    {
        $code = $response['code'] ?? 0;
        $message = static::$errorMessages[$code]
            ?? $response['message']
            ?? "SMS Misr API error (code: {$code})";

        return new static($message, $code, $response);
    }

    public static function getKnownErrors(): array
    {
        return static::$errorMessages;
    }
}
