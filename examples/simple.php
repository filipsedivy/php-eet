<?php

require_once __DIR__.'/../vendor/autoload.php';

define('Playground', __DIR__.'/../src/Schema/PlaygroundService.wsdl');
define('Production', __DIR__.'/../src/Schema/ProductionService.wsdl');

use FilipSedivy\EET\Dispatcher;
use FilipSedivy\EET\Receipt;
use FilipSedivy\EET\Utils\UUID;

$dispatcher = new Dispatcher(
    Playground,
    __DIR__.'/cert/eet.key',
    __DIR__.'/cert/eet.pem'
);

$dispatcher->trace = true;

$uuid = UUID::v4();

$r = new Receipt;
$r->uuid_zpravy = $uuid;
$r->id_provoz = '100';
$r->id_pokl = '1';
$r->dic_popl = 'CZ72080043';
$r->porad_cis = '1';
$r->dat_trzby = new \DateTime();
$r->celk_trzba = 500;

echo '<h2>---REQUEST---</h2>';
echo "<pre>";
try {
    $fik = $dispatcher->send($r);
    echo sprintf('<b>Returned FIK code: %s</b><br />', $fik);
} catch (\FilipSedivy\EET\Exceptions\ServerException $e) {
    var_dump($e); // See exception
} catch (\Exception $e) {
    var_dump($e); // Fatal error
}
echo sprintf('Request size: %d bytes | Response size: %d bytes | Response time: %f ms | Connection time: %f ms<br />', $dispatcher->getLastRequestSize(), $dispatcher->getLastResponseSize(), $dispatcher->getLastResponseTime(), $dispatcher->getConnectionTime()); // Size of transferred data