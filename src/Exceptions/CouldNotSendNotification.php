<?php

namespace NotificationChannels\Zendesk\Exceptions;

class CouldNotSendNotification extends \Exception
{
    public static function serviceRespondedWithAnError($response)
    {
        return new static('Zendesk responded with an error: `'.$response->getBody()->getContents().'`');
    }
}
