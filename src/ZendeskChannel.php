<?php

namespace NotificationChannels\Zendesk;

use Illuminate\Support\Arr;
use Zendesk\API\HttpClient;
use Illuminate\Notifications\Notification;
use NotificationChannels\Zendesk\Exceptions\CouldNotSendNotification;
use NotificationChannels\Zendesk\Events\ZendeskTicketWasCreated;
use NotificationChannels\Zendesk\Events\ZendeskTicketWasUpdated;

class ZendeskChannel
{
    /** @var HttpClient */
    protected $client;

    /** @var array */
    protected $parameters;

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
        $this->parameters = $notification->toZendesk($notifiable)->toArray();

        $id = $this->parameters['ticket'];

        if (! is_null($id)) {
            return $this->updateTicket($id);
        }

        return $this->createNewTicket($notifiable);
    }

    /**
     * Send update ticket request.
     *
     * @param int $id
     */
    private function updateTicket($id)
    {
        $this->prepareUpdateParameters();

        try {
            $this->client->tickets()->update($id, $this->parameters);

            event(new ZendeskTicketWasUpdated($response));
        } catch (\Exception $e) {
            throw CouldNotSendNotification::serviceRespondedWithAnError($e->getMessage());
        }
    }

    /**
     * Send create ticket request.
     *
     * @param mixed $notifiable
     */
    private function createNewTicket($notifiable)
    {
        $this->prepareCreateParameter($notifiable);

        try {
            $response = $this->client->tickets()->create($this->parameters);

            event(new ZendeskTicketWasCreated($response));
        } catch (\Exception $e) {
            throw CouldNotSendNotification::serviceRespondedWithAnError($e->getMessage());
        }
    }

    /**
     * Prepare the parameters before update request send.
     */
    public function prepareUpdateParameters()
    {
        unset($this->parameters['subject'], $this->parameters['requester'], $this->parameters['description'], $this->parameters['ticket']);
    }

    /**
     * Prepare the parameters before create request send.
     *
     * @param mixed $notifiable
     */
    private function prepareCreateParameter($notifiable)
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

        unset($this->parameters['ticket']);
    }
}
