<?php

namespace Ghanem\LaravelSmsmisr\Exceptions;

class SmsmisrInsufficientBalanceException extends SmsmisrApiException
{
    public static function fromResponse(array $response): static
    {
        $code = $response['code'] ?? 0;
        $message = $response['message'] ?? 'Insufficient SMS balance';

        return new static($message, $code, $response);
    }
}
