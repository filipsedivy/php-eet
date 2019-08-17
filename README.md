# Client for electronic records of sale

[![Codacy Badge](https://api.codacy.com/project/badge/Grade/07f653430c254d0bbf3f40c8595f0c89)](https://www.codacy.com/app/mail_72/PHP-EET?utm_source=github.com&utm_medium=referral&utm_content=filipsedivy/PHP-EET&utm_campaign=badger)
[![Build Status](https://travis-ci.org/filipsedivy/PHP-EET.svg?branch=master)](https://travis-ci.org/filipsedivy/PHP-EET) [![License](https://img.shields.io/badge/license-MIT-blue.svg)](https://packagist.org/packages/filipsedivy/php-eet)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/58a3ede2-9585-4e37-95ed-ca7726617ea8/mini.png)](https://insight.sensiolabs.com/projects/58a3ede2-9585-4e37-95ed-ca7726617ea8)

If the library is useful, **[please make a donation now](https://filipsedivy.cz/donation?to=PHP-EET)**. Thank you!

## Installation

### Composer

The recommended way to install is via Composer:

```bash
composer require filipsedivy/php-eet
```

## Usage

```php
<?php
use FilipSedivy\EET\Certificate;
use FilipSedivy\EET\Dispatcher;
use FilipSedivy\EET\Receipt;
use Ramsey\Uuid\Uuid;

$receipt = new Receipt;
$receipt->uuid_zpravy = Uuid::uuid4();
$receipt->id_provoz = '141';
$receipt->id_pokl = '1patro-vpravo';
$receipt->porad_cis = '141-18543-05';
$receipt->dic_popl = 'CZ00000019';
$receipt->dat_trzby = new DateTime;
$receipt->celk_trzba = 500;

$certificate = new Certificate('EET_CA1_Playground-CZ00000019.p12', 'eet');
$dispatcher = new Dispatcher($certificate, Dispatcher::PLAYGROUND_SERVICE);

try {
    $dispatcher->send($receipt);

    echo 'FIK: ' . $dispatcher->getFik();
    echo 'BKP: ' . $dispatcher->getBkp();
} catch (FilipSedivy\EET\Exceptions\EET\ClientException $exception) {
    echo 'BKP: ' . $exception->getBkp();
    echo 'PKP:' . $exception->getPkp();
} catch (FilipSedivy\EET\Exceptions\EET\ErrorException $exception) {
    echo '(' . $exception->getCode() . ') ' . $exception->getMessage();
}
```

## Links
- http://www.etrzby.cz/
- http://www.financnisprava.cz/cs/financni-sprava/eet
- https://adisspr.mfcr.cz/adistc/adis/idpr_pub/eet/eet_sluzby.faces
- http://www.jakpodnikat.cz/eet-elektronicka-evidence-trzeb.php