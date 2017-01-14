<?php

namespace NotificationChannels\Zendesk;

use Zendesk\API\Client;
use Illuminate\Support\Arr;
use Illuminate\Notifications\Notification;
use NotificationChannels\Zendesk\Exceptions\CouldNotSendNotification;

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

        if (!isset($zendeskParameters['requester']['name']) || $zendeskParameters['requester']['name'] ==='') {
            $routing = collect($notifiable->routeNotificationFor('Zendesk'));
            if (! Arr::has($routing, ['name', 'email'])) {
                return;
            }

            $zendeskParameters['requester']['name'] = $routing['name'];
            $zendeskParameters['requester']['email'] = $routing['email'];
        }

        $response = $this->client->tickets()->create($zendeskParameters);

        if ($response->getStatusCode() !== 200) {
            throw CouldNotSendNotification::serviceRespondedWithAnError($response);
        }
    }
}
