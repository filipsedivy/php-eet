<?php declare(strict_types=1);

namespace FilipSedivy\EET;

use FilipSedivy\EET\Exceptions;
use FilipSedivy\EET\Utils;

class Certificate
{
    /** @var string */
    private $privateKey;

    /** @var string */
    private $certificate;

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
    }

    public function getPrivateKey(): string
    {
        return $this->privateKey;
    }

    public function getCertificate(): string
    {
        return $this->certificate;
    }
}
