<?php

namespace Ghanem\LaravelSmsmisr;

use Ghanem\LaravelSmsmisr\Exceptions\SmsmisrException;
use Illuminate\Notifications\Notification;

class SmsmisrChannel
{
    public function __construct(protected Smsmisr $client)
    {
    }

    /**
     * Send the given notification.
     *
     * @throws SmsmisrException
     */
    public function send(mixed $notifiable, Notification $notification): SmsmisrResponse
    {
        $message = $notification->toSmsmisr($notifiable);

        if ($message->isVerification()) {
            return $this->client->sendVerify(
                $message->getVerificationCode(),
                $message->getTo(),
                $message->getSender(),
                $message->getTemplate(),
            );
        }

        return $this->client->send(
            $message->getMessage(),
            $message->getTo(),
            $message->getSender(),
            scheduledAt: $message->getScheduledAt(),
        );
    }
}
