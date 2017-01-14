# Zendesk notifications channel for Laravel 5.3

[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/laravel-notification-channels/zendesk/master.svg?style=flat-square)](https://travis-ci.org/assaqqaf/zendesk)
[![StyleCI](https://styleci.io/repos/65379321/shield)](https://styleci.io/repos/65379321)
[![SensioLabsInsight](https://img.shields.io/sensiolabs/i/9015691f-130d-4fca-8710-72a010abc684.svg?style=flat-square)](https://insight.sensiolabs.com/projects/9015691f-130d-4fca-8710-72a010abc684)
[![Quality Score](https://img.shields.io/scrutinizer/g/laravel-notification-channels/zendesk.svg?style=flat-square)](https://scrutinizer-ci.com/g/assaqqaf/zendesk)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/laravel-notification-channels/zendesk/master.svg?style=flat-square)](https://scrutinizer-ci.com/g/assaqqaf/zendesk/?branch=master)
[![Total Downloads](https://img.shields.io/packagist/dt/laravel-notification-channels/zendesk.svg?style=flat-square)](https://packagist.org/packages/assaqqaf/zendesk)

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

## Security

If you discover any security related issues, please email m.pociot@gmail.com instead of using the issue tracker.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Credits

- [Marcel Pociot](https://github.com/mpociot)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
