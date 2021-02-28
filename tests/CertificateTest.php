<?php declare(strict_types=1);

namespace FilipSedivy\EET\Tests;

use FilipSedivy\EET\Certificate;
use FilipSedivy\EET\Exceptions;
use FilipSedivy\EET\Utils\FileSystem;
use PHPUnit\Framework\TestCase;
use DateTime;

class CertificateTest extends TestCase
{
    public function testFileNotExists(): void
    {
        $this->expectException(Exceptions\Certificate\CertificateNotFoundException::class);
        Certificate::fromFile(__DIR__ . '/not-exists-certificate.p12', 'testPassword');
    }

    public function testFileExists(): void
    {
        $pkcs12 = FileSystem::read(__DIR__ . '/data/EET_CA1_Playground-CZ00000019.p12');
        $certificate = Certificate::fromString($pkcs12, 'eet');

        $this->assertInstanceOf(Certificate::class, $certificate);
    }

    public function testCertificateFromString(): void
    {
        $certificate = Certificate::fromFile(__DIR__ . '/data/EET_CA1_Playground-CZ00000019.p12', 'eet');

        $this->assertIsString($certificate->getPrivateKey());
        $this->assertIsString($certificate->getCertificate());
    }

    public function testBadPassword(): void
    {
        $this->expectException(Exceptions\Certificate\CertificateExportFailedException::class);
        Certificate::fromFile(__DIR__ . '/data/EET_CA1_Playground-CZ00000019.p12', 'password');
    }

    public function testCertificatePath(): void
    {
        try {
            Certificate::fromFile(__DIR__ . '/data/EET_CA1_Playground-CZ00000019.p12', 'password');

            $this->fail('Certificate have bad password');
        } catch (Exceptions\Certificate\CertificateExportFailedException $exception) {
            $this->assertIsString($exception->getPath());
        }
    }

    public function testCertificateValidation(): void
    {
        $certificate = Certificate::fromFile(__DIR__ . '/data/EET_CA1_Playground-CZ00000019.p12', 'eet');

        $this->assertTrue($certificate->isIssuerOk());
        $this->assertTrue($certificate->isValidOk());
        $this->assertTrue($certificate->isCertificateOk());
        $this->assertTrue($certificate->isOk());
    }

    public function testCertificateValidation2(): void
    {
        $certificate = Certificate::fromFile(__DIR__ . '/data/CA_TEST-01.p12', 'test');

        $this->assertFalse($certificate->isIssuerOk());
        $this->assertTrue($certificate->isValidOk());
        $this->assertTrue($certificate->isCertificateOk());
        $this->assertFalse($certificate->isOk());
    }

    public function testExport(): void
    {
        $certificate = Certificate::fromFile(__DIR__ . '/data/EET_CA1_Playground-CZ00000019.p12', 'eet');

        $this->assertIsArray($certificate->getExport());
        $this->assertIsArray($certificate->getIssuer());
        $this->assertIsArray($certificate->getSubject());

        $this->assertIsBool($certificate->isOk());
        $this->assertIsBool($certificate->isValidOk());
        $this->assertIsBool($certificate->isIssuerOk());
        $this->assertIsBool($certificate->isCertificateOk());

        $this->assertInstanceOf(DateTime::class, $certificate->getValidFrom());
        $this->assertInstanceOf(DateTime::class, $certificate->getValidTo());
    }
}