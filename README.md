# Slam Zend\Mail extensions

[![Build Status](https://travis-ci.org/Slamdunk/zend-mail-extensions.svg?branch=master)](https://travis-ci.org/Slamdunk/zend-mail-extensions)
[![Code Coverage](https://scrutinizer-ci.com/g/Slamdunk/zend-mail-extensions/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/Slamdunk/zend-mail-extensions/?branch=master)
[![Packagist](https://img.shields.io/packagist/v/slam/zend-mail-extensions.svg)](https://packagist.org/packages/slam/zend-mail-extensions)

Extensions for [Zend\Mail](https://github.com/zendframework/zend-mail)

## Installation

Execute:

`composer require slam/zend-mail-extensions`

## Usage

The main functionality of this package is the `Protocol\TimeKeeperSmtpProxy`,
which allows to handle SMTP server that implements a `reuse_time_limit` like
Postfix (ref: http://www.postfix.org/postconf.5.html#smtp_connection_reuse_time_limit).

References:

1. https://github.com/zendframework/zend-mail/pull/27
1. https://github.com/zendframework/zend-mail/pull/117
1. https://github.com/zendframework/zend-mail/pull/131

Example:

```php
/**
 * QUIT instruction is disabled by default
 * @see https://github.com/zendframework/zend-mail/pull/117
 *
 * All the Zend defaults options are still available
 * @see https://docs.zendframework.com/zend-mail/transport/smtp-authentication/
 */
$transport = new \Slam\Zend\Mail\Transport\Smtp();

$message = new \Zend\Mail\Message();
$message->setFrom('test@test.com');
$message->addTo('test@test.com');
$message->setSubject('Test');
$message->setBody('Test');

$transport->send($message);

sleep(305);

// $transport will automatically reconnect to the SMTP server
$transport->send($message);
```
