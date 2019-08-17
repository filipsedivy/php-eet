# Client for electronic records of sale

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
} catch (FilipSedivy\EET\Exceptions\Receipt\ConstraintViolationException $violationException) {
    echo implode('<br>', $violationException->getErrors());
}
```

## Certificate

These are classes that allow the certificate to be exported without further processing.

Received file in p12 format, the one that etrzby.cz will export.

```php
$certificate = FilipSedivy\EET\Certificate(string $file, string $password);
```

**Parameters:**
- `string $file`: File path
- `string $password`: Certificate password

## Dispatcher

Dispatcher is a class that takes care of recipe validation and communication with SoapClient.

```php
$dispatcher = FilipSedivy\EET\Dispatcher(Certificate $certificate, ?string $service = self::PRODUCTION_SERVICE, bool $validate = true);
```

**Parameters:**
- `Certificate $certificate`: Certificate class instance
- `?string $service`: Setting services (`self::PLAYGROUND_SERVICE` OR `self::PRODUCTION_SERVICE`)
- `bool $validate`: Enable offline Receipt validation

**Methods:**
- `check(Receipt $receipt): bool`: Receipt verification without registering EET
- `send(Receipt $receipt, bool $check = false): ?string`: Send EET to the server. If everything is OK, the string with FIK is returned. If `$check = true` is enabled, FIK is not returned

## Exceptions

All exceptions have a common namespace `FilipSedivy\EET\Exceptions`

### EET\ClientException

This is an exception that is thrown when there is a problem communicating with the target server.

**This exception allows you to get the latest BKP, PKP and receipt.**

```php
try {
    $dispatcher->send($receipt);
} catch (FilipSedivy\EET\Exceptions\EET\ClientException $exception) {
    echo 'BKP: ' . $exception->getBkp();
    echo 'PKP:' . $exception->getPkp();
    print_r($exception->getReceipt());
}
```

### EET\ErrorException

This error occurs when an error is returned directly from the EET server. In this case, the entire message is invalid and not recorded. For this reason it is not possible to get PKP and BKP code from the exception.

`getCode()` returns the error code from the target server. `getMessage()` returns the translated error according to etrzby.cz documentation.

```php
try {
    $dispatcher->send($receipt);
} catch (FilipSedivy\EET\Exceptions\EET\ErrorException $exception) {
     echo '(' . $exception->getCode() . ') ' . $exception->getMessage();
}
```

### Receipt\ConstraintViolationException

If validation is enabled, this exception is thrown in case of invalid value according to the scheme.

In this case, the EET is not sent to the destination server and the Receipt is invalid and it is not possible to obtain BKP and PKP codes.

```php
try {
    $dispatcher->send($receipt);
} catch (FilipSedivy\EET\Exceptions\Receipt\ConstraintViolationException $violationException) {
      echo implode('<br>', $violationException->getErrors());
  }
```