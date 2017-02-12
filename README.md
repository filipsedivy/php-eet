# PHP knihovna pro EET

[![Build Status](https://travis-ci.org/filipsedivy/PHP-EET.svg?branch=master)](https://travis-ci.org/filipsedivy/PHP-EET) [![Latest Stable Version](https://poser.pugx.org/filipsedivy/php-eet/v/stable)](https://packagist.org/packages/filipsedivy/php-eet) [![Total Downloads](https://poser.pugx.org/filipsedivy/php-eet/downloads)](https://packagist.org/packages/filipsedivy/php-eet) [![Latest Unstable Version](https://poser.pugx.org/filipsedivy/php-eet/v/unstable)](https://packagist.org/packages/filipsedivy/php-eet) [![License](https://poser.pugx.org/filipsedivy/php-eet/license)](https://packagist.org/packages/filipsedivy/php-eet)

## Instalace

### Composer

Pro instalaci balíčku je nutné jej instalovat skrze [Composer](https://getcomposer.org/).

```bash
composer require filipsedivy/php-eet
```

### Bez Composeru

S každou verzí, která bude vydána bude přiložen i ZIP balíček, pro možnost instalace bez Composeru. **Avšak tuto možnost nedoporučuji, neboť není v našich silách udržet aktualizovaný ZIP baliček a verzi pro Composer.** Proto ZIP balíček bude vždy vygenerován pro velké verze, nikoliv pro menší aktualizace.

#### Aktuální ZIP balíček

Verze: 2.0.0

Datum vygenerování: 17.11.2016

Stáhnout: https://github.com/filipsedivy/PHP-EET/releases/download/v2.0.0/php-eet-20161117.zip


## Ukázka užití

Ukázky naleznete ve složce **examples/**.

Certifikát **EET_CA1_Playground-CZ00000019.p12** byl vydán pro účel testování Daňovou správou. Tento certifikát nepoužívejte pro ostrou verzi. Svůj certifikát si vygenerujete skrze rozhraní Daňové správy.

```php
use FilipSedivy\EET\Certificate;
use FilipSedivy\EET\Dispatcher;
use FilipSedivy\EET\Receipt;
use FilipSedivy\EET\Utils\UUID;
use FilipSedivy\EET\Schema\Wsdl;

$certificate = new Certificate(__DIR__.'/EET_CA1_Playground-CZ00000019.p12', 'eet');
$dispatcher = new Dispatcher(Wsdl::playground(), $certificate);

$uuid = UUID::v4(); // Generování UUID

$r = new Receipt;
$r->uuid_zpravy = $uuid;
$r->id_provoz = '11';
$r->id_pokl = 'IP105';
$r->dic_popl = 'CZ1212121218';
$r->porad_cis = '1';
$r->dat_trzby = new \DateTime();
$r->celk_trzba = 500;

echo $dispatcher->send($r);
```

## Aktualizace

- 2.0.0
  - Úprava načítání certifikátu (*nyní není třeba certifikát převádět, třída jej převede sama*)

## Podpora / Implementace

Pokud potřebujete pomoc s implementací nebo poradit se okolo EET (registrace EET, generování certifikátů,...) můžete mě kontaktovat (*https://filipsedivy.cz*).

## Screencasty

- Generování certifikátu pro EET - https://youtu.be/N5Cb9SqeP6g

## Odkazy
- etržby.cz - http://www.etrzby.cz/
- Finanční správa - http://www.financnisprava.cz/cs/financni-sprava/eet
- Daňový portál - https://adisspr.mfcr.cz/adistc/adis/idpr_pub/eet/eet_sluzby.faces
- Informace o EET z pohledu podnikání - http://www.jakpodnikat.cz/eet-elektronicka-evidence-trzeb.php

## Licence

GNU GPL 3 - http://www.gnugpl.cz/v3/
