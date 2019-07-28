<?php declare(strict_types=1);
/**
 * Test: FilipSedivy\EET\Certificate.
 *
 * @testCase
 */

namespace EetTest\Certificate;

use FilipSedivy\EET\Certificate;
use FilipSedivy\EET\Exceptions\CertificateException;
use Tester\Assert;

require_once __DIR__ . '/../bootstrap.php';

class CertificateTest extends \Tester\TestCase
{
    public function testFileNotExists()
    {
        Assert::exception(function() {
            new Certificate(__DIR__ . '/certificate.p12', 'testPassword');
        }, CertificateException::class, 'Certificate was not found');
    }

    public function testFileExists()
    {
        $certificate = new Certificate(__DIR__ . '/../../examples/EET_CA1_Playground-CZ00000019.p12', 'eet');

        Assert::type(Certificate::class, $certificate);
    }

    public function testCertificate()
    {
        $certificate = new Certificate(__DIR__ . '/../../examples/EET_CA1_Playground-CZ00000019.p12', 'eet');

        Assert::type('string', $certificate->getPrivateKey());
        Assert::type('string', $certificate->getCert());
    }
}

(new CertificateTest())->run();