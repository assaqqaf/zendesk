<?php

namespace NotificationChannels\Zendesk\Events;

class ZendeskTicketWasCreated
{
    public $ticket;

    /**
     * Create a new event instance.
     *
     * @param $ticket
     * @return void
     */
    public function __construct($ticket)
    {
        $this->ticket = $ticket;
    }
}
