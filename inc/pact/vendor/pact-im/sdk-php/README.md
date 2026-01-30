# Pact.im PHP SDK

A PHP library for Pact.im API

## Install

Via Composer

```bash
composer require pact-im/sdk-php
```

To use the bindings, use Composer's autoload:

```php
require_once('vendor/autoload.php');
```

## Usage

```php
<?php
$token = '<your super secret token>';
$client = new \Pact\PactClient($token);
```

And you ready to go!
See documentation [here](https://pact-im.github.io/api-doc/#introduction).
