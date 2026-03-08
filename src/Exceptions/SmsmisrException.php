<?php

namespace Ghanem\LaravelSmsmisr\Exceptions;

use Exception;

class SmsmisrException extends Exception
{
    protected ?array $response;

    public function __construct(string $message = '', int $code = 0, ?array $response = null, ?\Throwable $previous = null)
    {
        $this->response = $response;
        parent::__construct($message, $code, $previous);
    }

    public function getResponse(): ?array
    {
        return $this->response;
    }
}
