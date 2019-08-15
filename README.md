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
require_once __DIR__.'/vendor/autoload.php';

use FilipSedivy\EET\Certificate;
use FilipSedivy\EET\Dispatcher;
use FilipSedivy\EET\Receipt;

$certificate = new Certificate(__DIR__.'/EET_CA1_Playground-CZ00000019.p12', 'eet');
$dispatcher = new Dispatcher($certificate);
$dispatcher->setPlaygroundService();

$uuid = Ramsey\Uuid\Uuid::v4();

$r = new Receipt;
$r->uuid_zpravy = $uuid;
$r->id_provoz = '11';
$r->id_pokl = 'IP105';
$r->dic_popl = 'CZ1212121218';
$r->porad_cis = '1';
$r->dat_trzby = new \DateTime();
$r->celk_trzba = 500;

echo '<h2>---REQUEST---</h2>';
echo '<pre>';

try {
    $dispatcher->send($r);

    echo sprintf('FIK: %s <br>', $dispatcher->getFik());
    echo sprintf('BKP: %s <br>', $dispatcher->getBkp());
}catch(\FilipSedivy\EET\Exceptions\EetException $ex){
    echo sprintf('BKP: %s <br>', $dispatcher->getBkp());
    echo sprintf('PKP: %s <br>', $dispatcher->getPkp());
}catch(Exception $ex){
    var_dump($ex);
}
```

## Links
- http://www.etrzby.cz/
- http://www.financnisprava.cz/cs/financni-sprava/eet
- https://adisspr.mfcr.cz/adistc/adis/idpr_pub/eet/eet_sluzby.faces
- http://www.jakpodnikat.cz/eet-elektronicka-evidence-trzeb.php