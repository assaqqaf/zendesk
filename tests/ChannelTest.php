<?php

namespace NotificationChannels\Zendesk\Test;

use Mockery;
use Zendesk\API\HttpClient;
use GuzzleHttp\Psr7\Response;
use Orchestra\Testbench\TestCase;
use Illuminate\Notifications\Notification;
use NotificationChannels\Zendesk\ZendeskChannel;
use NotificationChannels\Zendesk\ZendeskMessage;

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
        $client = Mockery::mock(HttpClient::class);
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
                    'description' => 'This will be sent as ticket body',
                    'type' => null,
                    'status' => 'new',
                    'tags' => [],
                    'priority' => 'normal',
                ])
            ->andReturn($response);
        $channel = new ZendeskChannel($client);
        $channel->send(new TestNotifiable(), new TestNotification());
    }
}

class TestNotifiable
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
