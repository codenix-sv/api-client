# PHP API Client

[![Build Status](https://travis-ci.org/codenix-sv/api-client.svg?branch=master)](https://travis-ci.org/codenix-sv/api-client)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/codenix-sv/api-client/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/codenix-sv/api-client/?branch=master)
[![Test Coverage](https://api.codeclimate.com/v1/badges/b494f68f06e75d45fc68/test_coverage)](https://codeclimate.com/github/codenix-sv/api-client/test_coverage)
[![Maintainability](https://api.codeclimate.com/v1/badges/b494f68f06e75d45fc68/maintainability)](https://codeclimate.com/github/codenix-sv/api-client/maintainability)
[![License: MIT](https://img.shields.io/github/license/codenix-sv/api-client)](https://github.com/codenix-sv/api-client/blob/master/LICENSE)


A simple API client, written with PHP that's easy to use.

## Requirements

* PHP >= 7.2
* A [HTTP client](https://packagist.org/providers/php-http/client-implementation)
* A [PSR-7 implementation](https://packagist.org/providers/psr/http-message-implementation)

## Installation

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```bash
$ composer require codenix-sv/api-client
```
or add

```json
"codenix-sv/api-client": "^1.0"
```

to the require section of your application's `composer.json` file.

Make sure you have installed a PSR-18 HTTP Client and PSR-7 message implementation before you install this package. For example:

```bash
$ composer require php-http/curl-client nyholm/psr7
```

## Examples

*Send GET request*

```php
use Codenixsv\ApiClient\BaseClient;

$client = new BaseClient();
$response = $client->get('https://httpbin.org/get');
```

*Send POST request*

```php
use Codenixsv\ApiClient\BaseClient;

$client = new BaseClient();
$response = $client->post('https://httpbin.org/post', 'foo=bar');
```

## License

`codenix-sv/api-client` is released under the MIT License. See the bundled [LICENSE](./LICENSE) for details.
