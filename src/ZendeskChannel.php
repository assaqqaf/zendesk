<?php

namespace NotificationChannels\Zendesk;

use Zendesk\API\HttpClient;
use Illuminate\Notifications\Notification;
use NotificationChannels\Zendesk\Exceptions\CouldNotSendNotification;

class ZendeskChannel
{
    /** @var HttpClient */
    protected $client;

    /** @param HttpClient $client */
    public function __construct(HttpClient $client)
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

        try {
            $response = $this->client->tickets()->create($zendeskParameters);
        } catch (\Zendesk\API\Exceptions\ApiResponseException $e) {
            throw CouldNotSendNotification::serviceRespondedWithAnError($e->getMessage());
        }
    }
}
