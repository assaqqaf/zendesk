<?php

namespace NotificationChannels\Zendesk\Test;

use Mockery;
use Zendesk\API\Client;
use GuzzleHttp\Psr7\Response;
use Orchestra\Testbench\TestCase;
use Illuminate\Notifications\Notification;
use NotificationChannels\Zendesk\ZendeskChannel;
use NotificationChannels\Zendesk\ZendeskMessage;
use NotificationChannels\Zendesk\Exceptions\CouldNotSendNotification;

class ChannelTest extends TestCase
{
    /** @test */
    public function it_can_send_a_notification()
    {
        $this->app['config']->set('services.zendesk', [
            'subdomin' => 'ZENDESK_API_SUBDOMIN',
            'username' => 'ZENDESK_API_USERNAME',
            'token' => 'ZENDESK_API_TOKEN',
        ]);

        $response = new Response(200);
        $client = Mockery::mock(Client::class);
        $client->shouldReceive('tickets')
            ->once()
            ->andReturn($client)
            ->shouldReceive('create')
            ->once()
            ->with(
                [
                    'subject' => 'Ticket Subject',
                    'comment' => [
                        'body' => 'This will be sent as ticket body',
                        'public' => false,
                    ],
                    'requester' => [
                        'name' => 'Test User',
                        'email' => 'user@example.org',
                    ],
                    'description' => '',
                    'type' => null,
                    'status' => 'new',
                    'tags' => [],
                    'priority' => 'normal',
                ])
            ->andReturn($response);
        $channel = new ZendeskChannel($client);
        $channel->send(new TestNotifiableWithoutRoute(), new TestNotification());
    }

    /** @test */
    public function it_can_send_a_notification_and_add_requester_data_if_not_set()
    {
        $this->app['config']->set('services.zendesk', [
            'subdomin' => 'ZENDESK_API_SUBDOMIN',
            'username' => 'ZENDESK_API_USERNAME',
            'token' => 'ZENDESK_API_TOKEN',
        ]);

        $response = new Response(200);
        $client = Mockery::mock(Client::class);
        $client->shouldReceive('tickets')
            ->once()
            ->andReturn($client)
            ->shouldReceive('create')
            ->once()
            ->with(
                [
                    'subject' => 'Ticket Subject',
                    'comment' => [
                        'body' => 'This will be sent as ticket body',
                        'public' => false,
                    ],
                    'requester' => [
                        'name' => 'Name',
                        'email' => 'email@example.org',
                    ],
                    'description' => '',
                    'type' => null,
                    'status' => 'new',
                    'tags' => [],
                    'priority' => 'normal',
                ])
            ->andReturn($response);
        $channel = new ZendeskChannel($client);
        $channel->send(new TestNotifiable(), new TestNotificationWithoutFrom());
    }

    /** @test */
    public function it_throws_an_exception_when_it_could_not_send_the_notification()
    {
        $this->setExpectedException(CouldNotSendNotification::class);

        $this->app['config']->set('services.zendesk', [
            'subdomin' => 'ZENDESK_API_SUBDOMIN',
            'username' => 'ZENDESK_API_USERNAME',
            'token' => 'ZENDESK_API_TOKEN',
        ]);

        $response = new Response(500);
        $client = Mockery::mock(Client::class);
        $client->shouldReceive('tickets')
            ->once()
            ->andReturn($client)
            ->shouldReceive('create')
            ->once()
            ->with(
                [
                    'subject' => 'Ticket Subject',
                    'comment' => [
                        'body' => 'This will be sent as ticket body',
                        'public' => false,
                    ],
                    'requester' => [
                        'name' => 'Test User',
                        'email' => 'user@example.org',
                    ],
                    'description' => '',
                    'type' => null,
                    'status' => 'new',
                    'tags' => [],
                    'priority' => 'normal',
                ])
            ->andReturn($response);
        $channel = new ZendeskChannel($client);
        $channel->send(new TestNotifiableWithoutRoute(), new TestNotification());
    }
}

class TestNotifiable
{
    use \Illuminate\Notifications\Notifiable;

    /**
     * @return int
     */
    public function routeNotificationForZendesk()
    {
        return [
            'name' => 'Name',
            'email' => 'email@example.org',
        ];
    }
}

class TestNotifiableWithoutRoute
{
    use \Illuminate\Notifications\Notifiable;
}

class TestNotification extends Notification
{
    public function toZendesk($notifiable)
    {
        return
            (new ZendeskMessage('Ticket Subject'))
                ->from('Test User', 'user@example.org')
                ->content('This will be sent as ticket body');
    }
}

class TestNotificationWithoutFrom extends Notification
{
    public function toZendesk($notifiable)
    {
        return
            (new ZendeskMessage('Ticket Subject'))
                ->content('This will be sent as ticket body');
    }
}
