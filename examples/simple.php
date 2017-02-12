<?php

require_once __DIR__.'/../../../../vendor/autoload.php';

use FilipSedivy\EET\Dispatcher;
use FilipSedivy\EET\Receipt;
use FilipSedivy\EET\Utils\UUID;
use FilipSedivy\EET\Certificate;
use FilipSedivy\EET\Schema\Wsdl;

$certificate = new Certificate(__DIR__.'/EET_CA1_Playground-CZ00000019.p12', 'eet');
$dispatcher = new Dispatcher(Wsdl::playground(), $certificate);

$dispatcher->trace = true;

$uuid = UUID::v4();

$r = new Receipt;
$r->uuid_zpravy = $uuid;
$r->id_provoz = '11';
$r->id_pokl = 'IP105';
$r->dic_popl = 'CZ1212121218';
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