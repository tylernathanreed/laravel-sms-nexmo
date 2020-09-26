<?php

namespace Reedware\LaravelSMS\Nexmo;

use Reedware\LaravelSMS\Contracts\Message as MessageContract;
use Reedware\LaravelSMS\Transport\Transport;
use Vonage\Client;
use Vonage\Message\Message;

class NexmoTransport extends Transport
{
    /**
     * The nexmo client.
     *
     * @var \Vonage\Client
     */
    protected $nexmo;

    /**
     * Creates a new nexmo transport instance.
     *
     * @param  \Vonage\Client  $nexmo
     *
     * @return $this
     */
    public function __construct(Client $nexmo)
    {
        $this->nexmo = $nexmo;
    }

    /**
     * Sends the given message; returns the number of recipients who were accepted for delivery.
     *
     * @param  \Reedware\LaravelSMS\Contracts\Message  $message
     * @param  string[]                                $failedRecipients
     *
     * @return integer
     */
    public function send(MessageContract $message, &$failedRecipients = null)
    {
        foreach ($message->getTo() as $to) {
            $this->nexmo->message()->send(new Message($to['number'], $message->getFrom()[0]['number'], ['text' => $message->getBody()]));
        }

        return $this->getRecipientCount($message);
    }

    /**
     * Returns the nexmo client.
     *
     * @return \Vonage\Client
     */
    public function nexmo()
    {
        return $this->nexmo;
    }
}