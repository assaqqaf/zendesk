# Zendesk notifications channel for Laravel 5.3 [WIP]

[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://travis-ci.org/assaqqaf/zendesk.svg?branch=master)](https://travis-ci.org/assaqqaf/zendesk)
[![StyleCI](https://styleci.io/repos/78912239/shield?branch=master)](https://styleci.io/repos/78912239)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/15d138a5-3c27-4167-a5ce-1c559ae5311f/mini.png)](https://insight.sensiolabs.com/projects/15d138a5-3c27-4167-a5ce-1c559ae5311f)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/assaqqaf/zendesk/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/assaqqaf/zendesk/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/assaqqaf/zendesk/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/assaqqaf/zendesk/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/assaqqaf/zendesk/badges/build.png?b=master)](https://scrutinizer-ci.com/g/assaqqaf/zendesk/build-status/master)

This package makes it easy to create [Zendesk API](https://developer.zendesk.com/) with Laravel 5.3.

## Contents

- [Installation](#installation)
    - [Setting up the Zendesk service](#setting-up-the-zendesk-service)
- [Usage](#usage)
	- [Available Message methods](#available-message-methods)
- [Changelog](#changelog)
- [Testing](#testing)
- [Security](#security)
- [Contributing](#contributing)
- [Credits](#credits)
- [License](#license)


## Installation

You can install the package via composer:

``` bash
composer require laravel-notification-channels/zendesk
```

### Setting up the Zendesk service

Add your Zendesk REST API Key to your `config/services.php`:

```php
// config/services.php
...
'zendesk' => [
    'subdomin' => env('ZENDESK_API_SUBDOMIN'),
    'username' => env('ZENDESK_API_USERNAME'),
    'token' => env('ZENDESK_API_TOKEN'),
],
...
```


## Usage

Now you can use the channel in your `via()` method inside the notification:

``` php
use NotificationChannels\Zendesk\ZendeskChannel;
use NotificationChannels\Zendesk\ZendeskMessage;
use Illuminate\Notifications\Notification;

class ProjectCreated extends Notification
{
    public function via($notifiable)
    {
        return [ZendeskChannel::class];
    }

    public function toZendesk($notifiable)
    {
        return ZendeskMessage::create()
            ->subject("Zendesk Ticket Subject")
            ->description("This is the Zendesk Ticket description");
    }
}
```

### Available methods

- `subject('')`: Accepts a string value for the Zendesk ticket name.
- `description('')`: Accepts a string value for the Zendesk ticket description.


## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Testing

``` bash
$ composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Credits

- [Abdullah Mohammed](https://github.com/assaqqaf)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
