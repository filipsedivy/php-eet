<?php declare(strict_types=1);

namespace Tests\Cases;

use DateTime;
use FilipSedivy\EET\Certificate;
use FilipSedivy\EET\Exceptions;
use FilipSedivy\EET\Utils\FileSystem;
use Tester\Assert;
use Tester\TestCase;

require_once __DIR__ . '/../bootstrap.php';

class CertificateTest extends TestCase
{
    public function testFileNotExists(): void
    {
        Assert::exception(static function (): void {
            Certificate::fromFile(__DIR__ . '/not-exists-certificate.p12', 'testPassword');
        }, Exceptions\Certificate\CertificateNotFoundException::class);
    }

    public function testFileExists(): void
    {
        $pkcs12 = FileSystem::read(DATA_DIR . '/EET_CA1_Playground-CZ00000019.p12');

        $certificate = Certificate::fromString($pkcs12, 'eet');

        Assert::type(Certificate::class, $certificate);
    }

    public function testCertificateFromString(): void
    {
        $certificate = Certificate::fromFile(DATA_DIR . '/EET_CA1_Playground-CZ00000019.p12', 'eet');

        Assert::type('string', $certificate->getPrivateKey());
        Assert::type('string', $certificate->getCertificate());
    }

    public function testCertificateFromFile(): void
    {
        $certificate = Certificate::fromFile(DATA_DIR . '/EET_CA1_Playground-CZ00000019.p12', 'eet');

        Assert::type('string', $certificate->getPrivateKey());
        Assert::type('string', $certificate->getCertificate());
    }

    public function testBadPassword(): void
    {
        Assert::exception(static function (): void {
            Certificate::fromFile(DATA_DIR . '/EET_CA1_Playground-CZ00000019.p12', 'password');
        }, Exceptions\Certificate\CertificateExportFailedException::class);
    }

    public function testCertificatePath(): void
    {
        try {
            Certificate::fromFile(DATA_DIR . '/EET_CA1_Playground-CZ00000019.p12', 'password');

            Assert::fail('Certificate have bad password');
        } catch (Exceptions\Certificate\CertificateExportFailedException $exception) {
            Assert::type('string', $exception->getPath());
        }
    }

    public function testCertificateValidation(): void
    {
        $certificate = Certificate::fromFile(DATA_DIR . '/EET_CA1_Playground-CZ00000019.p12', 'eet');
        $certificate2 = Certificate::fromFile(DATA_DIR . '/CA_TEST-01.p12', 'test');

        Assert::true($certificate->isIssuerOk());
        Assert::true($certificate->isValidOk());
        Assert::true($certificate->isCertificateOk());
        Assert::true($certificate->isOk());

        Assert::false($certificate2->isIssuerOk());
        Assert::true($certificate2->isValidOk());
        Assert::true($certificate2->isCertificateOk());
        Assert::false($certificate2->isOk());
    }

    public function testExport(): void
    {
        $certificate = Certificate::fromFile(DATA_DIR . '/EET_CA1_Playground-CZ00000019.p12', 'eet');

        Assert::type('array', $certificate->getExport());
        Assert::type('array', $certificate->getIssuer());
        Assert::type('array', $certificate->getSubject());

        Assert::type('bool', $certificate->isOk());
        Assert::type('bool', $certificate->isValidOk());
        Assert::type('bool', $certificate->isIssuerOk());
        Assert::type('bool', $certificate->isCertificateOk());

        Assert::type(DateTime::class, $certificate->getValidFrom());
        Assert::type(DateTime::class, $certificate->getValidTo());
    }
}

(new CertificateTest)->run();
