# PHP knihovna pro EET

[![Build Status](https://travis-ci.org/filipsedivy/PHP-EET.svg?branch=master)](https://travis-ci.org/filipsedivy/PHP-EET) [![Latest Stable Version](https://poser.pugx.org/filipsedivy/php-eet/v/stable)](https://packagist.org/packages/filipsedivy/php-eet) [![Total Downloads](https://poser.pugx.org/filipsedivy/php-eet/downloads)](https://packagist.org/packages/filipsedivy/php-eet) [![Latest Unstable Version](https://poser.pugx.org/filipsedivy/php-eet/v/unstable)](https://packagist.org/packages/filipsedivy/php-eet) [![License](https://poser.pugx.org/filipsedivy/php-eet/license)](https://packagist.org/packages/filipsedivy/php-eet)

## Instalace

### Composer

Pro instalaci balíčku je nutné jej instalovat skrze [Composer](https://getcomposer.org/).

```bash
composer require filipsedivy/php-eet
```

## Ukázka užití

Ukázky naleznete ve složce **examples/**.

Ve složce **examples/cert/** je certifikát pro testovacího uživatele.

```php
use FilipSedivy\EET\Dispatcher;
use FilipSedivy\EET\Receipt;
use FilipSedivy\EET\Utils\UUID;

$dispatcher = new Dispatcher(
    Playground,
    __DIR__.'/cert/eet.key',
    __DIR__.'/cert/eet.pem'
);

$uuid = UUID::v4(); // Generování UUID

$r = new Receipt;
$r->uuid_zpravy = $uuid;
$r->id_provoz = '100';
$r->id_pokl = '1';
$r->dic_popl = 'CZ72080043';
$r->porad_cis = '1';
$r->dat_trzby = new \DateTime();
$r->celk_trzba = 500;

echo $dispatcher->send($r);
```

## Podpora / Implementace

Pokud potřebujete pomoc s implementací nebo poradit se okolo EET (registrace EET, generování certifikátů,...) můžete mě kontaktovat (*https://filipsedivy.cz*).

## Odkazy
- etržby.cz - http://www.etrzby.cz/
- Finanční správa - http://www.financnisprava.cz/cs/financni-sprava/eet
- Daňový portál - https://adisspr.mfcr.cz/adistc/adis/idpr_pub/eet/eet_sluzby.faces

## Licence

Licence bude doplněna do vydání první stabilní verze