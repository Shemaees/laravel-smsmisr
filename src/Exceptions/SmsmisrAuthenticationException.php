<?php

namespace Ghanem\LaravelSmsmisr\Exceptions;

class SmsmisrAuthenticationException extends SmsmisrApiException
{
    public static function fromResponse(array $response): static
    {
        $code = $response['code'] ?? 0;
        $message = $response['message'] ?? 'Invalid username or password';

        return new static($message, $code, $response);
    }
}
