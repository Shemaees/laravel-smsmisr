<?php

namespace Ghanem\LaravelSmsmisr;

use DateTimeInterface;

class SmsmisrMessage
{
    protected string $message;
    protected string $sender;
    protected string $to;
    protected bool $unicode = true;
    protected bool $verification = false;
    protected ?string $verificationCode = null;
    protected ?string $template = null;
    protected ?DateTimeInterface $scheduledAt = null;

    public function __construct(string $message = '', string $to = '')
    {
        $this->message = $message;
        $this->to = $to;
        $this->sender = config('smsmisr.sender') ?? '';
    }

    public function message(string $message): static
    {
        $this->message = $message;

        return $this;
    }

    public function sender(string $sender): static
    {
        $this->sender = $sender;

        return $this;
    }

    public function to(string $to): static
    {
        $this->to = $to;

        return $this;
    }

    public function unicode(bool $unicode = true): static
    {
        $this->unicode = $unicode;

        return $this;
    }

    /**
     * Mark this message as an OTP/verification SMS.
     */
    public function asVerification(string $code, ?string $template = null): static
    {
        $this->verification = true;
        $this->verificationCode = $code;
        $this->template = $template;

        return $this;
    }

    /**
     * Schedule the SMS for later delivery.
     */
    public function scheduledAt(DateTimeInterface $dateTime): static
    {
        $this->scheduledAt = $dateTime;

        return $this;
    }

    public function getMessage(): string
    {
        return $this->unicode ? $this->message : $this->messageToGsmFormat($this->message);
    }

    public function getTo(): string
    {
        return $this->to;
    }

    public function getSender(): string
    {
        return $this->sender;
    }

    public function isUnicode(): bool
    {
        return $this->unicode;
    }

    public function isVerification(): bool
    {
        return $this->verification;
    }

    public function getVerificationCode(): ?string
    {
        return $this->verificationCode;
    }

    public function getTemplate(): ?string
    {
        return $this->template;
    }

    public function getScheduledAt(): ?DateTimeInterface
    {
        return $this->scheduledAt;
    }

    public function __get(string $name): mixed
    {
        return match ($name) {
            'message' => $this->getMessage(),
            'to' => $this->getTo(),
            'sender' => $this->getSender(),
            default => throw new \InvalidArgumentException("Property [{$name}] does not exist."),
        };
    }

    protected function messageToGsmFormat(string $message, string $replace = '?'): string
    {
        $dict = [
            '@' => "\x00", '£' => "\x01", '$' => "\x02", '¥' => "\x03",
            'è' => "\x04", 'é' => "\x05", 'ù' => "\x06", 'ì' => "\x07",
            'ò' => "\x08", 'Ç' => "\x09", 'Ø' => "\x0B", 'ø' => "\x0C",
            'Å' => "\x0E", 'å' => "\x0F", 'Δ' => "\x10", '_' => "\x11",
            'Φ' => "\x12", 'Γ' => "\x13", 'Λ' => "\x14", 'Ω' => "\x15",
            'Π' => "\x16", 'Ψ' => "\x17", 'Σ' => "\x18", 'Θ' => "\x19",
            'Ξ' => "\x1A", 'Æ' => "\x1C", 'æ' => "\x1D", 'ß' => "\x1E",
            'É' => "\x1F", 'Ä' => "\x5B", 'Ö' => "\x5C", 'Ñ' => "\x5D",
            'Ü' => "\x5E", '§' => "\x5F", '¿' => "\x60", 'ä' => "\x7B",
            'ö' => "\x7C", 'ñ' => "\x7D", 'ü' => "\x7E", 'à' => "\x7F",
            '^' => "\x1B\x14", '{' => "\x1B\x28", '}' => "\x1B\x29",
            '\\' => "\x1B\x2F", '[' => "\x1B\x3C", '~' => "\x1B\x3D",
            ']' => "\x1B\x3E", '|' => "\x1B\x40", '€' => "\x1B\x65",
        ];

        $converted = strtr($message, $dict);

        return preg_replace('/([\\xC0-\\xDF].)|([\\xE0-\\xEF]..)|([\\xF0-\\xFF]...)/m', $replace, $converted);
    }
}
