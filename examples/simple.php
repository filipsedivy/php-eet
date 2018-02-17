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
 * Základní kód, ukazuje jak odesílat platby do EET
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

// Generování UUID
$uuid = UUID::v4();

// Vytvoření receiptu
$r = new Receipt;
$r->uuid_zpravy = $uuid;
$r->id_provoz = '11';
$r->id_pokl = 'IP105';
$r->dic_popl = 'CZ1212121218';
$r->porad_cis = '1';
$r->dat_trzby = new \DateTime();
$r->celk_trzba = 500;

output('-= REQUEST =-');

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