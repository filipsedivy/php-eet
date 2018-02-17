<?php
/**
 * This file is part of the PHP-EET package.
 *
 * (c) Filip Sedivy <mail@filipsedivy.cz>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license MIT
 * @author  Filip Sedivy <mail@filipsedivy.cz>
 */

/*
 * -= Základní ukázka kódu =-
 * Základní kód, ukazuje jak odesílat opakované platby, v případě
 * výpadku internetu a vystavení BKP a PKP kódu
*/

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/helpers.php';

use FilipSedivy\EET\Dispatcher;
use FilipSedivy\EET\Receipt;
use FilipSedivy\EET\Utils\UUID;
use FilipSedivy\EET\Certificate;

$certificate = new Certificate(__DIR__ . '/EET_CA1_Playground-CZ00000019.p12', 'eet');
$dispatcher = new Dispatcher($certificate);
$dispatcher->setPlaygroundService();

/* Uložený poslední request request */
$file = file_get_contents(__DIR__ . '/Receipt.json');
$json = json_decode($file, true);

/* BKP a PKP kód z posledního requestu */
$bkp = $json['codes']['bkp'];
$pkp = $json['codes']['pkp'];

/* Vygeneruje se nové UUID, kterým se přepíše původní */
$uuid = UUID::v4();

/** @var Receipt $r */
$r = unserialize($json['receipt']);

$r->uuid_zpravy = $uuid;
$r->prvni_zaslani = false;      // Jelikož se zpráva zasílá opakovaně, nastavíme (bool) false
$r->bkp = $bkp;                 // Nastavení původního BKP, provážeme offline účtenku
$r->pkp = base64_decode($pkp);  // Nastavení původního podpisu, je nutné jej převést do binární podoby

try
{
    output('UUID: ', $uuid);

    $dispatcher->send($r);

    output('FIK: ', $dispatcher->getFik());
    output('BKP: ', $dispatcher->getBkp());
}
catch (\FilipSedivy\EET\Exceptions\EetException $e)
{
    output('Error: ', $e->getMessage());
    output('FIK: ', $dispatcher->getFik());
    output('BKP: ', $dispatcher->getBkp());
}
catch (Exception $e)
{
    output('Error: ', $e->getMessage());
}