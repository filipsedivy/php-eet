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

require_once __DIR__.'/../vendor/autoload.php';

use FilipSedivy\EET\Dispatcher;
use FilipSedivy\EET\Receipt;
use FilipSedivy\EET\Utils\UUID;
use FilipSedivy\EET\Certificate;

$certificate = new Certificate(__DIR__.'/EET_CA1_Playground-CZ00000019.p12', 'eet');
$dispatcher = new Dispatcher($certificate);
$dispatcher->setPlaygroundService();

// UUID se generuje nové
$uuid = UUID::v4();

// Předává se původní datum
$dat_trzby = (new DateTime())
    ->setDate(2017, 5, 1)
    ->setTime(12, 33, 30);

$r = new Receipt;
$r->uuid_zpravy = $uuid;    // UUID se generuje nové
$r->id_provoz = '11';
$r->id_pokl = 'IP105';
$r->dic_popl = 'CZ1212121218';
$r->porad_cis = '1';
$r->dat_trzby = $dat_trzby;
$r->celk_trzba = 500;
$r->prvni_zaslani = false; // Mění se hodnota prvního zaslání z TRUE na FALSE

// Předává se původně vygenerovaný BKP a PKP kód
$r->bkp = 'e245f2ac-52d74aef-50a9334b-dc0bcdf9-f676113f';
$r->pkp = 'TGUBKTLy5Fmq32yNlKUyTuQ7F5kKAL2nSbtu71tufYqQU7QE8RAAv63xGrUiOMtfPidFLrMfrVzRn1WF7RFDiOxIGPahVOX6j4ZdBXQx67OMhCCmeAsZM4wnVDFLq+25VlhoM7cENRg0n7JXdvRGUu3zrDD7jqgSr6RJYcilicLDR20pJVF4ML5fY\/rs7naGmh\/ZloNT2kU6LXWfsnilbz4esizfYubZBULHAoeNUfObxRkPmfMR+7KY3LwRsNFISAS+SS2lEAhMNrlJdvNHZNjL1770izMsjqIPMBJNRS+NtqjICHdXhFYb8ukU1sKaq9FaFX4sQ0bvCPMKcIlgrw==';

echo '<h2>---REQUEST---</h2>';
echo "<pre>";

try {

    $dispatcher->send($r);

    // Tržba byla úspěšně odeslána
    echo sprintf("FIK: %s <br>", addslashes($dispatcher->getFik()));
    echo sprintf("BKP: %s <br>", addslashes($dispatcher->getBkp()));

}catch(\FilipSedivy\EET\Exceptions\EetException $ex){
    // Tržba nebyla odeslána

    echo sprintf("BKP: %s <br>", addslashes($dispatcher->getBkp()));
    echo sprintf("PKP: %s <br>", addslashes($dispatcher->getPkp()));

}catch(Exception $ex){
    // Obecná chyba
    var_dump($ex);

}