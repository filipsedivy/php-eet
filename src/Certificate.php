<?php declare(strict_types=1);

namespace XSuchy09\EET;

use DateTime;
use XSuchy09\EET\Exceptions;
use XSuchy09\EET\Utils;

class Certificate
{
    /** @var string */
    private $privateKey;

    /** @var string */
    private $certificate;

    /** @var array */
    private $export;

    public function __construct(string $file, string $password)
    {
        if (!Utils\FileSystem::isFileExists($file)) {
            throw new Exceptions\Certificate\CertificateNotFoundException($file);
        }

        $pkcs12 = Utils\FileSystem::read($file);

        if (!function_exists('openssl_pkcs12_read')) {
            throw new Exceptions\ExtensionNotFound('OpenSSL');
        }

        $openSSL = openssl_pkcs12_read($pkcs12, $certs, $password);

        if (!$openSSL) {
            throw new Exceptions\Certificate\CertificateExportFailedException($file);
        }

        $this->privateKey = $certs['pkey'];
        $this->certificate = $certs['cert'];
        $this->export = openssl_x509_parse($this->getCertificate());
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
