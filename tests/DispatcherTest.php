<?php declare(strict_types=1);

namespace FilipSedivy\EET\Tests;

use FilipSedivy\EET\Certificate;
use FilipSedivy\EET\Dispatcher;
use FilipSedivy\EET\Exceptions;
use FilipSedivy\EET\Receipt;
use FilipSedivy\EET\SoapClient;
use PHPUnit\Framework\TestCase;
use DateTime;
use Ramsey\Uuid\Uuid;

class DispatcherTest extends TestCase
{
    private Certificate $certificate;

    protected function setUp(): void
    {
        $this->certificate = Certificate::fromFile(__DIR__ . '/data/EET_CA1_Playground-CZ00000019.p12', 'eet');
    }

    public function testService(): void
    {
        $dispatcher = new Dispatcher($this->certificate, 'Personal/MySchema/MyService.wsdl');

        $this->assertStringContainsString('Personal/MySchema/MyService.wsdl', $dispatcher->getService());

        $schemaPath = 'Schema';

        $dispatcher->setProductionService();

        $this->assertStringContainsString($schemaPath . '/ProductionService.wsdl', $dispatcher->getService());

        $dispatcher->setPlaygroundService();

        $this->assertStringContainsString($schemaPath . '/PlaygroundService.wsdl', $dispatcher->getService());

        $dispatcher->setService('Personal/MySchema/MyService.wsdl');

        $this->assertStringContainsString('Personal/MySchema/MyService.wsdl', $dispatcher->getService());

        $dispatcher = new Dispatcher($this->certificate, Dispatcher::PRODUCTION_SERVICE);

        $this->assertStringContainsString($schemaPath . '/ProductionService.wsdl', $dispatcher->getService());
    }

    public function testSendReceipt(): void
    {
        $dispatcher = new Dispatcher($this->certificate, Dispatcher::PLAYGROUND_SERVICE);

        $dispatcher->send($this->getValidReceipt());

        $this->assertIsString($dispatcher->getFik());
        $this->assertIsString($dispatcher->getBkp());
        $this->assertIsString($dispatcher->getSoapClient()->lastResponse);
        $this->assertEquals(200, $dispatcher->getSoapClient()->getLastResponseHttpCode());
        $this->assertInstanceOf(DateTime::class, $dispatcher->getSentDateTime());
    }

    public function testFailed(): void
    {
        $dispatcher = new Dispatcher($this->certificate, Dispatcher::PLAYGROUND_SERVICE);
        $dispatcher->setCurlOption(CURLOPT_PROXY, '127.0.0.1:8888');

        $this->expectException(Exceptions\EET\ClientException::class);
        $dispatcher->send($this->getValidReceipt());
    }

    public function testFailed2(): void
    {
        $dispatcher = new Dispatcher($this->certificate, Dispatcher::PLAYGROUND_SERVICE);
        $dispatcher->setCurlOption(CURLOPT_PROXY, '127.0.0.1:8888');

        try {
            $dispatcher->send($this->getValidReceipt());
        } catch (Exceptions\EET\ClientException $client) {
            $this->assertIsString($client->getBkp());
            $this->assertIsString($client->getPkp());
            $this->assertNull($dispatcher->getSoapClient()->getLastResponseHttpCode());
            $this->assertInstanceOf(DateTime::class, $dispatcher->getSentDateTime());

            if (!$client->getReceipt() instanceof Receipt) {
                $this->fail('Client->getReceipt() is not instanceof Receipt');
            }
        }
    }

    public function testCheck(): void
    {
        $dispatcher = new Dispatcher($this->certificate, Dispatcher::PLAYGROUND_SERVICE);

        $receipt = $this->getValidReceipt();
        $this->assertTrue($dispatcher->check($receipt));

        $receipt->dic_popl = 'CZ00000018';
        $this->assertFalse($dispatcher->check($receipt));
    }

    public function testGetCheckCodes(): void
    {
        $dispatcher = new Dispatcher($this->certificate, Dispatcher::PLAYGROUND_SERVICE);

        $receipt = $this->getValidReceipt();

        $this->assertIsArray($dispatcher->getCheckCodes($receipt));

        $receipt->bkp = $dispatcher->getBkp();
        $receipt->pkp = $dispatcher->getPkp(false);

        $this->assertIsArray($dispatcher->getCheckCodes($receipt));
    }

    public function testLastReceipt(): void
    {
        $dispatcher = new Dispatcher($this->certificate, Dispatcher::PLAYGROUND_SERVICE);

        $this->assertNull($dispatcher->getLastReceipt());

        $dispatcher->send($this->getValidReceipt());

        $this->assertInstanceOf(Receipt::class, $dispatcher->getLastReceipt());
    }

    public function testGetWarnings(): void
    {
        $dispatcher = new Dispatcher($this->certificate, Dispatcher::PLAYGROUND_SERVICE);

        $this->assertIsArray($dispatcher->getWarnings());
        $this->assertCount(0, $dispatcher->getWarnings());
    }

    public function testGetPkp(): void
    {
        $dispatcher = new Dispatcher($this->certificate, Dispatcher::PLAYGROUND_SERVICE);

        $this->assertNull($dispatcher->getPkp());

        $dispatcher->send($this->getValidReceipt());

        $this->assertIsString($dispatcher->getPkp());
    }

    public function testGetSentDateTime(): void
    {
        $dispatcher = new Dispatcher($this->certificate, Dispatcher::PLAYGROUND_SERVICE);

        $this->assertNull($dispatcher->getSentDateTime());

        $dispatcher->send($this->getValidReceipt());

        $this->assertInstanceOf(DateTime::class, $dispatcher->getSentDateTime());
    }

    public function testGetSoapClient(): void
    {
        $dispatcher = new Dispatcher($this->certificate, Dispatcher::PLAYGROUND_SERVICE);

        $this->assertInstanceOf(SoapClient::class, $dispatcher->getSoapClient());
    }

    private function getValidReceipt(): Receipt
    {
        $receipt = new Receipt();
        $receipt->uuid_zpravy = Uuid::uuid4()->toString();
        $receipt->id_provoz = '11';
        $receipt->id_pokl = 'IP105';
        $receipt->dic_popl = 'CZ00000019';
        $receipt->porad_cis = '1';
        $receipt->dat_trzby = new DateTime;
        $receipt->celk_trzba = 500;

        return $receipt;
    }
}