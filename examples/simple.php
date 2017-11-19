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
 * @author Filip Sedivy <mail@filipsedivy.cz>
 */

/*
 * -= Základní ukázka kódu =-
 * Základní kód, ukazuje jak odesílat platby do EET
*/

require_once __DIR__.'/../vendor/autoload.php';

use FilipSedivy\EET\Dispatcher;
use FilipSedivy\EET\Receipt;
use FilipSedivy\EET\Utils\UUID;
use FilipSedivy\EET\Certificate;

$certificate = new Certificate(__DIR__.'/EET_CA1_Playground-CZ00000019.p12', 'eet');
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

echo '-= REQUEST =-'.PHP_EOL;

try {
    // Unikátní ID
    echo sprintf('UUID: %s', addslashes($uuid)).PHP_EOL;

    // Tržbu klasicky odešleme
    $dispatcher->send($r);

    // Tržba byla úspěšně odeslána
    echo sprintf('FIK: %s', addslashes($dispatcher->getFik())).PHP_EOL;
    echo sprintf('BKP: %s', addslashes($dispatcher->getBkp())).PHP_EOL;
}catch(\FilipSedivy\EET\Exceptions\EetException $ex){
    // Tržba nebyla odeslána
    echo sprintf('BKP: %s', addslashes($dispatcher->getBkp())).PHP_EOL;
    echo sprintf('PKP: %s', addslashes($dispatcher->getPkp())).PHP_EOL;
}catch(Exception $ex){
    // Obecná chyba
    echo $ex->getMessage();
}