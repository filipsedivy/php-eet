<?php declare(strict_types=1);
/**
 * Test: FilipSedivy\EET\Dispatcher.
 *
 * @testCase
 */

namespace EetTest\Dispatcher;

use FilipSedivy\EET\Certificate;
use FilipSedivy\EET\Dispatcher;
use FilipSedivy\EET\Exceptions\ClientException;
use FilipSedivy\EET\Receipt;
use Tester\Assert;
use Tester\TestCase;

require_once __DIR__ . '/../bootstrap.php';

class DispatcherTest extends TestCase
{
    public function testSendReceipt()
    {
        $certificate = new Certificate(__DIR__ . '/../../examples/EET_CA1_Playground-CZ00000019.p12', 'eet');
        $dispatcher = new Dispatcher($certificate);
        $dispatcher->setPlaygroundService();

        $r = new Receipt();
        $r->uuid_zpravy = \Ramsey\Uuid\Uuid::uuid4()->toString();
        $r->id_provoz = '11';
        $r->id_pokl = 'IP105';
        $r->dic_popl = 'CZ1212121218';
        $r->porad_cis = '1';
        $r->dat_trzby = new \DateTime();
        $r->celk_trzba = 500;

        $dispatcher->send($r);

        Assert::type('string', $dispatcher->getFik());
        Assert::type('string', $dispatcher->getBkp());
    }

    public function testSendReceipts()
    {
        $certificate = new Certificate(__DIR__ . '/../../examples/EET_CA1_Playground-CZ00000019.p12', 'eet');
        $dispatcher = new Dispatcher($certificate);
        $dispatcher->setPlaygroundService();

        for ($i = 0, $iMax = random_int(4, 9); $i < $iMax; $i++) {
            $r = new Receipt();
            $r->uuid_zpravy = \Ramsey\Uuid\Uuid::uuid4()->toString();
            $r->id_provoz = '11';
            $r->id_pokl = 'IP105';
            $r->dic_popl = 'CZ1212121218';
            $r->porad_cis = '1';
            $r->dat_trzby = new \DateTime();
            $r->celk_trzba = 500;

            $dispatcher->send($r);

            Assert::type('string', $dispatcher->getFik());
            Assert::type('string', $dispatcher->getBkp());
        }
    }

    public function testPermeableErrors()
    {
        $certificate = new Certificate(__DIR__ . '/../../examples/EET_CA1_Playground-CZ00000019.p12', 'eet');
        $dispatcher = new Dispatcher($certificate);
        $dispatcher->setPlaygroundService();

        $r = new Receipt();
        $r->uuid_zpravy = \Ramsey\Uuid\Uuid::uuid4()->toString();
        $r->id_provoz = '11';
        $r->id_pokl = 'IP105';
        $r->dic_popl = 'CZ1212121218';
        $r->porad_cis = '1';
        $r->dat_trzby = (new \DateTime())->setDate(2000, 1, 1);
        $r->celk_trzba = 500;

        $dispatcher->send($r);

        Assert::type('array', $dispatcher->getWarnings());
        Assert::count(2, $dispatcher->getWarnings());
    }

    public function testNotPermeableErrors()
    {
        $certificate = new Certificate(__DIR__ . '/../../examples/EET_CA1_Playground-CZ00000019.p12', 'eet');
        $dispatcher = new Dispatcher($certificate);
        $dispatcher->setPlaygroundService();

        $r = new Receipt();
        $r->uuid_zpravy = \Ramsey\Uuid\Uuid::uuid4()->toString();
        $r->id_provoz = '11';
        $r->id_pokl = 'IP105';
        $r->dic_popl = 'CZ00000019';
        $r->porad_cis = '1';
        $r->dat_trzby = new \DateTime();
        $r->celk_trzba = 500;

        $dispatcher->send($r);

        Assert::type('array', $dispatcher->getWarnings());
        Assert::count(0, $dispatcher->getWarnings());
    }

    public function testRepeatSend()
    {
        static $proxy = array('127.0.0.1', 8888);

        $certificate = new Certificate(__DIR__ . '/../../examples/EET_CA1_Playground-CZ00000019.p12', 'eet');

        $proxyDispatcher = new Dispatcher($certificate);
        $proxyDispatcher->setPlaygroundService();

        // Setting not valid proxy
        $proxyDispatcher->setCurlOption(CURLOPT_PROXY, implode($proxy, ':'));

        $dispatcher = new Dispatcher($certificate);
        $dispatcher->setPlaygroundService();

        $r = new Receipt();
        $r->uuid_zpravy = \Ramsey\Uuid\Uuid::uuid4()->toString();
        $r->id_provoz = '11';
        $r->id_pokl = 'IP105';
        $r->dic_popl = 'CZ00000019';
        $r->porad_cis = '1';
        $r->dat_trzby = new \DateTime();
        $r->celk_trzba = 500;

        Assert::type('null', $proxyDispatcher->getFik());

        Assert::exception(function () use ($proxyDispatcher, $r) {
            $proxyDispatcher->send($r);
        }, ClientException::class, 'Failed to connect to ' . $proxy[0] . ' port ' . $proxy[1] . ': Connection refused');

        Assert::type('string', $proxyDispatcher->getBkp());
        Assert::type('null', $proxyDispatcher->getFik());

        $r->pkp = $proxyDispatcher->getPkp(false);
        $r->bkp = $proxyDispatcher->getBkp();
        $r->prvni_zaslani = false;
        $r->uuid_zpravy = \Ramsey\Uuid\Uuid::uuid4()->toString();

        Assert::type('null', $dispatcher->getFik());

        $dispatcher->send($r);

        Assert::type('string', $dispatcher->getFik());
        Assert::type('string', $dispatcher->getBkp());
        Assert::type('string', $dispatcher->getPkp());
    }

    public function testBaseReceipt()
    {
        $certificate = new Certificate(__DIR__ . '/../../examples/EET_CA1_Playground-CZ00000019.p12', 'eet');

        $dispatcher = new Dispatcher($certificate);
        $dispatcher->setPlaygroundService();

        Assert::exception(function () use ($dispatcher) {
            $receipt = new Receipt();
            $dispatcher->send($receipt);
        }, ClientException::class, 'Property \'dat_trzby\' is not instance of DateTime');
    }
}

(new DispatcherTest())->run();