<?php

namespace NotificationChannels\Zendesk;

use Zendesk\API\Client;
use Illuminate\Support\Arr;
use NotificationChannels\Zendesk\Exceptions\CouldNotSendNotification;
use Illuminate\Notifications\Notification;
use NotificationChannels\Zendesk\Exceptions\InvalidConfiguration;

class ZendeskChannel
{
    /** @var Client */
    protected $client;

    /** @param Client $client */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Send the given notification.
     *
     * @param mixed $notifiable
     * @param \Illuminate\Notifications\Notification $notification
     *
     * @throws \NotificationChannels\Zendesk\Exceptions\InvalidConfiguration
     * @throws \NotificationChannels\Zendesk\Exceptions\CouldNotSendNotification
     */
    public function send($notifiable, Notification $notification)
    {
        $zendeskParameters = $notification->toZendesk($notifiable)->toArray();

        $response = $this->client->tickets()->create($zendeskParameters);

        if ($response->getStatusCode() !== 200) {
            throw CouldNotSendNotification::serviceRespondedWithAnError($response);
        }
    }
}
