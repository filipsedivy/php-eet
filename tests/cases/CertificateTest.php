<?php declare(strict_types=1);

namespace Tests\Cases;

use FilipSedivy\EET\Certificate;
use FilipSedivy\EET\Exceptions;
use Tester\Assert;
use Tester\TestCase;

require_once __DIR__ . '/../bootstrap.php';

class CertificateTest extends TestCase
{
    public function testFileNotExists(): void
    {
        Assert::exception(static function () {
            new Certificate(__DIR__ . '/not-exists-certificate.p12', 'testPassword');
        }, Exceptions\Certificate\CertificateNotFoundException::class);
    }

    public function testFileExists(): void
    {
        $certificate = new Certificate(DATA_DIR . '/EET_CA1_Playground-CZ00000019.p12', 'eet');

        Assert::type(Certificate::class, $certificate);
    }

    public function testCertificate(): void
    {
        $certificate = new Certificate(DATA_DIR . '/EET_CA1_Playground-CZ00000019.p12', 'eet');

        Assert::type('string', $certificate->getPrivateKey());
        Assert::type('string', $certificate->getCertificate());
    }

    public function testBadPassword(): void
    {
        Assert::exception(static function () {
            new Certificate(DATA_DIR . '/EET_CA1_Playground-CZ00000019.p12', 'password');
        }, Exceptions\Certificate\CertificateExportFailedException::class);
    }

    public function testCertificatePath(): void
    {
        try {
            new Certificate(DATA_DIR . '/EET_CA1_Playground-CZ00000019.p12', 'password');

            Assert::fail('Certificate have bad password');
        } catch (Exceptions\Certificate\CertificateExportFailedException $exception) {
            Assert::type('string', $exception->getPath());
        }
    }
}

(new CertificateTest)->run();
