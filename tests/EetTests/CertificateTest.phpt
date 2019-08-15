<?php declare(strict_types=1);
/**
 * Test: FilipSedivy\EET\Certificate.
 *
 * @testCase
 */

namespace EetTest\Certificate;

use FilipSedivy\EET\Certificate;
use FilipSedivy\EET\Exceptions\Certificate\CertificateNotFoundException;
use Tester\Assert;

require_once __DIR__ . '/../bootstrap.php';

class CertificateTest extends \Tester\TestCase
{
    public function testFileNotExists(): void
    {
        Assert::exception(function () {
            new Certificate(__DIR__ . '/not-exists-certificate.p12', 'testPassword');
        }, CertificateNotFoundException::class);
    }

    public function testFileExists(): void
    {
        $certificate = new Certificate(__DIR__ . '/../Data/EET_CA1_Playground-CZ00000019.p12', 'eet');

        Assert::type(Certificate::class, $certificate);
    }

    public function testCertificate(): void
    {
        $certificate = new Certificate(__DIR__ . '/../Data/EET_CA1_Playground-CZ00000019.p12', 'eet');

        Assert::type('string', $certificate->getPrivateKey());
        Assert::type('string', $certificate->getCert());
    }
}

(new CertificateTest())->run();