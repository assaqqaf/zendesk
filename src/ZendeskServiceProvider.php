<?php

namespace NotificationChannels\Clickatell;

use NotificationChannels\Zendesk\Exceptions\InvalidConfiguration;
use NotificationChannels\Zendesk\ZendeskChannel;
use Illuminate\Support\ServiceProvider;
use Zendesk\API\Client;

class ZendeskServiceProvider extends ServiceProvider
{
    /**
     * Register the application services.
     */
    public function register()
    {
        $this->app->when(ZendeskChannel::class)
            ->needs(Client::class)
            ->give(function () {
                $config = config('services.zendesk');
                if (! isset($config['subdomin'], $config['username'], $config['token'])) {
                    throw InvalidConfiguration::configurationNotSet();
                }

                $client = new Client($config['subdomin'], $config['username']);
                $client->setAuth('token', $config['token']);

                return $client;
            });
    }
}
