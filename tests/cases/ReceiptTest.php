<?php declare(strict_types=1);

namespace Tests\Cases;

use Exception;
use XSuchy09\EET;
use Symfony\Component\Validator;
use Tester\Assert;
use Tester\TestCase;

require_once __DIR__ . '/../bootstrap.php';

class ReceiptTest extends TestCase
{
    public function testHeader(): void
    {
        $receipt = new EET\Receipt;
        $receipt->uuid_zpravy = '8f5138bf-49e2-4ee9-9509-d75d01095609';

        $header = [
            'uuid_zpravy' => '8f5138bf-49e2-4ee9-9509-d75d01095609',
            'prvni_zaslani' => true
        ];

        Assert::same($receipt->buildHeader(), $header);
    }

    public function testSendEmptyReceipt(): void
    {
        $certificate = new EET\Certificate(DATA_DIR . '/EET_CA1_Playground-CZ00000019.p12', 'eet');
        $dispatcher = new EET\Dispatcher($certificate, EET\Dispatcher::PLAYGROUND_SERVICE);

        Assert::exception(static function () use ($dispatcher) {
            $receipt = new EET\Receipt;
            $dispatcher->send($receipt);
        }, EET\Exceptions\Receipt\ConstraintViolationException::class);
    }

    public function testEmptyCodes(): void
    {
        $receipt = new EET\Receipt;
        $exception = new EET\Exceptions\EET\ClientException($receipt, null, null, new Exception);

        Assert::null($exception->getPkp());
        Assert::null($exception->getBkp());
    }

    public function testConstraintViolation(): void
    {
        $certificate = new EET\Certificate(DATA_DIR . '/EET_CA1_Playground-CZ00000019.p12', 'eet');
        $dispatcher = new EET\Dispatcher($certificate, EET\Dispatcher::PLAYGROUND_SERVICE, true);

        $receipt = new EET\Receipt;
        $receipt->dic_popl = 'BadValue';

        try {
            $dispatcher->getCheckCodes($receipt);
        } catch (EET\Exceptions\Receipt\ConstraintViolationException $exception) {
            Assert::type('array', $exception->getErrors());
            Assert::type('array', $exception->getProperties());
            Assert::type(Validator\ConstraintViolationListInterface::class, $exception->getConstraintViolationList());
        }
    }
}

(new ReceiptTest)->run();
