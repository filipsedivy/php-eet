<?php declare(strict_types=1);

namespace FilipSedivy\EET;

use DateTime;
use FilipSedivy\EET\Exceptions;
use FilipSedivy\EET\Utils;

class Certificate
{
    /** @var string */
    private $privateKey;

    /** @var string */
    private $certificate;

    /** @var array */
    private $export;

    public static function fromString(string $pkcs12, string $password): Certificate
    {
        return self::readPkcs12($pkcs12, $password, "unknown");
    }

    public static function fromFile(string $file, string $password): Certificate
    {
        if (!Utils\FileSystem::isFileExists($file)) {
            throw new Exceptions\Certificate\CertificateNotFoundException($file);
        }

        $pkcs12 = Utils\FileSystem::read($file);

        return self::readPkcs12($pkcs12, $password, $file);
    }

    private static function readPkcs12(string $pkcs12, string $password, string $identifier): Certificate
    {
        if (!function_exists('openssl_pkcs12_read')) {
            throw new Exceptions\ExtensionNotFound('OpenSSL');
        }

        $openSSL = openssl_pkcs12_read($pkcs12, $certs, $password);

        if (!$openSSL) {
            throw new Exceptions\Certificate\CertificateExportFailedException($identifier);
        }

        $privateKey = $certs['pkey'];
        $certificate = $certs['cert'];
        $export = openssl_x509_parse($certificate);

        return new Certificate($privateKey, $certificate, $export);
    }


    private function __construct(string $privateKey, string $certificate, array $export)
    {
        $this->privateKey = $privateKey;
        $this->certificate = $certificate;
        $this->export = $export;
    }

    public function getPrivateKey(): string
    {
        return $this->privateKey;
    }

    public function getCertificate(): string
    {
        return $this->certificate;
    }

    public function getExport(): array
    {
        return $this->export;
    }

    public function getIssuer(): array
    {
        $export = $this->getExport();

        return $export['issuer'];
    }

    public function getSubject(): array
    {
        $export = $this->getExport();

        return $export['subject'];
    }

    public function getValidFrom(): DateTime
    {
        $export = $this->getExport();

        return date_create_from_format('ymdHise', $export['validFrom']);
    }

    public function getValidTo(): DateTime
    {
        $export = $this->getExport();

        return date_create_from_format('ymdHise', $export['validTo']);
    }

    public function isIssuerOk(): bool
    {
        $production = [
            'DC' => 'CZ',
            'O' => 'Česká Republika – Generální finanční ředitelství',
            'CN' => 'EET CA 1'
        ];

        $playground = [
            'DC' => 'CZ',
            'O' => 'Česká Republika – Generální finanční ředitelství',
            'CN' => 'EET CA 1 Playground'
        ];

        return count(array_diff_assoc($this->getIssuer(), $production, $playground)) === 0;
    }

    public function isCertificateOk(): bool
    {
        $export = $this->getExport();

        return $export['signatureTypeSN'] === 'RSA-SHA256';
    }

    public function isValidOk(): bool
    {
        $now = new DateTime('now');

        return ($this->getValidTo() > $now) && ($this->getValidFrom() < $now);
    }

    public function isOk(): bool
    {
        return $this->isIssuerOk() &&
            $this->isCertificateOk() &&
            $this->isValidOk();
    }
}
