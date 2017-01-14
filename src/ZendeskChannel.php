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

    /** @var array */
    protected $parameters;

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
        $this->parameters = $notification->toZendesk($notifiable)->toArray();

        $this->prepareParameter($notifiable);

        $response = $this->client->tickets()->create($this->parameters);

        if ($response->getStatusCode() !== 200) {
            throw CouldNotSendNotification::serviceRespondedWithAnError($response);
        }
    }

    /**
     * Prepare the parameters before to be send.
     *
     * @param mixed $notifiable
     */
    private function prepareParameter($notifiable)
    {
        // Check if the requester data is not set
        if (! isset($this->parameters['requester']['name']) || $this->parameters['requester']['name'] === '') {
            $routing = collect($notifiable->routeNotificationFor('Zendesk'));
            if (! Arr::has($routing, ['name', 'email'])) {
                return;
            }

            $this->parameters['requester']['name'] = $routing['name'];
            $this->parameters['requester']['email'] = $routing['email'];
        }
    }
}
