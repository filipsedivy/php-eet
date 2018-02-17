<?php
/**
 * Test: FilipSedivy\EET\Dispatcher.
 *
 * @testCase
 */

namespace EetTest\Dispatcher;

use FilipSedivy\EET\Certificate;
use FilipSedivy\EET\Dispatcher;
use FilipSedivy\EET\Exceptions\EetException;
use FilipSedivy\EET\Receipt;
use FilipSedivy\EET\Utils\UUID;
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
        $r->uuid_zpravy = UUID::v4();
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

        for ($i = 0; $i < rand(4, 9); $i++)
        {
            $r = new Receipt();
            $r->uuid_zpravy = UUID::v4();
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
        $r->uuid_zpravy = UUID::v4();
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
        $r->uuid_zpravy = UUID::v4();
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
}

(new DispatcherTest())->run();