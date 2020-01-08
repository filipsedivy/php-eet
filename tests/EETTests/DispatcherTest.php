<?php declare(strict_types=1);

namespace EETTests;

use FilipSedivy\EET\Certificate;
use FilipSedivy\EET\Dispatcher;
use FilipSedivy\EET\Exceptions\EET\ClientException;
use FilipSedivy\EET\Receipt;
use Ramsey\Uuid\Uuid;
use Tester\Assert;
use Tester\TestCase;

require_once __DIR__ . '/../bootstrap.php';

class DispatcherTest extends TestCase
{
    public function testService(): void
    {
        $certificate = new Certificate(__DIR__ . '/../Data/EET_CA1_Playground-CZ00000019.p12', 'eet');

        $dispatcher = new Dispatcher($certificate, 'Personal/MySchema/MyService.wsdl');

        Assert::contains('Personal/MySchema/MyService.wsdl', $dispatcher->getService());

        $schemaPath = 'FilipSedivy/EET/Schema';

        $dispatcher->setProductionService();

        Assert::contains($schemaPath . '/ProductionService.wsdl', $dispatcher->getService());

        $dispatcher->setPlaygroundService();

        Assert::contains($schemaPath . '/PlaygroundService.wsdl', $dispatcher->getService());

        $dispatcher->setService('Personal/MySchema/MyService.wsdl');

        Assert::contains('Personal/MySchema/MyService.wsdl', $dispatcher->getService());

        $dispatcher = new Dispatcher($certificate, Dispatcher::PRODUCTION_SERVICE);

        Assert::contains($schemaPath . '/ProductionService.wsdl', $dispatcher->getService());
    }

    public function testSendReceipt(): void
    {
        $certificate = new Certificate(__DIR__ . '/../Data/EET_CA1_Playground-CZ00000019.p12', 'eet');
        $dispatcher = new Dispatcher($certificate, Dispatcher::PLAYGROUND_SERVICE);

        $dispatcher->send($this->getValidReceipt());

        Assert::type('string', $dispatcher->getFik());
        Assert::type('string', $dispatcher->getBkp());
    }

    public function testFailed(): void
    {
        static $proxy = ['127.0.0.1', 8888];
        $certificate = new Certificate(__DIR__ . '/../Data/EET_CA1_Playground-CZ00000019.p12', 'eet');

        $dispatcher = new Dispatcher($certificate, Dispatcher::PLAYGROUND_SERVICE);
        $dispatcher->setCurlOption(CURLOPT_PROXY, implode($proxy, ':'));

        Assert::exception(function () use ($dispatcher) {
            $dispatcher->send($this->getValidReceipt());
        }, ClientException::class);

        try {
            $dispatcher->send($this->getValidReceipt());
        } catch (ClientException $client) {
            Assert::type('string', $client->getBkp());
            Assert::type('string', $client->getPkp());

            if (!$client->getReceipt() instanceof Receipt) {
                Assert::fail('Client->getReceipt() is not instanceof Receipt');
            }
        }
    }

    public function testCheck(): void
    {
        $certificate = new Certificate(__DIR__ . '/../Data/EET_CA1_Playground-CZ00000019.p12', 'eet');
        $dispatcher = new Dispatcher($certificate, Dispatcher::PLAYGROUND_SERVICE);

        $receipt = $this->getValidReceipt();
        Assert::true($dispatcher->check($receipt));

        $receipt->dic_popl = 'CZ00000018';
        Assert::false($dispatcher->check($receipt));
    }

    public function testGetCheckCodes(): void
    {
        $certificate = new Certificate(__DIR__ . '/../Data/EET_CA1_Playground-CZ00000019.p12', 'eet');
        $dispatcher = new Dispatcher($certificate, Dispatcher::PLAYGROUND_SERVICE);

        $receipt = $this->getValidReceipt();

        Assert::type('array', $dispatcher->getCheckCodes($receipt));

        $receipt->bkp = $dispatcher->getBkp();
        $receipt->pkp = $dispatcher->getPkp(false);

        Assert::type('array', $dispatcher->getCheckCodes($receipt));
    }

    private function getValidReceipt(): Receipt
    {
        $receipt = new Receipt;
        $receipt->uuid_zpravy = Uuid::uuid4()->toString();
        $receipt->id_provoz = '11';
        $receipt->id_pokl = 'IP105';
        $receipt->dic_popl = 'CZ00000019';
        $receipt->porad_cis = '1';
        $receipt->dat_trzby = new \DateTime;
        $receipt->celk_trzba = 500;

        return $receipt;
    }
}

(new DispatcherTest)->run();
