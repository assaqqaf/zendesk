<?php

namespace NotificationChannels\Zendesk\Events;

class ZendeskTicketWasUpdated
{
    public $ticket;

    /**
     * Create a new event instance.
     *
     * @param $ticket
     */
    public function __construct($ticket)
    {
        $this->ticket = $ticket;
    }
}
