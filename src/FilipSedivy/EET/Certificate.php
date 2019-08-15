<?php declare(strict_types=1);

namespace FilipSedivy\EET;

use FilipSedivy\EET\Exceptions\Certificate\CertificateExportFailedException;
use FilipSedivy\EET\Exceptions\Certificate\CertificateNotFoundException;
use FilipSedivy\EET\Exceptions\ExtensionNotFound;
use FilipSedivy\EET\Utils\FileSystem;

class Certificate
{
    /** @var string */
    private $privateKey;

    /** @var string */
    private $cert;

    public function __construct($certificate, $password)
    {
        if (!FileSystem::isFileExists($certificate)) {
            throw new CertificateNotFoundException($certificate);
        }

        $certs = [];
        $pkcs12 = file_get_contents($certificate);

        if (!function_exists('openssl_pkcs12_read')) {
            throw new ExtensionNotFound('OpenSSL');
        }

        $openSSL = openssl_pkcs12_read($pkcs12, $certs, $password);
        if (!$openSSL) {
            throw new CertificateExportFailedException($certificate);
        }

        $this->privateKey = $certs['pkey'];
        $this->cert = $certs['cert'];
    }

    public function getPrivateKey(): string
    {
        return $this->privateKey;
    }

    public function getCert(): string
    {
        return $this->cert;
    }
}
