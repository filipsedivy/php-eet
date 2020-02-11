<?php declare(strict_types=1);

namespace FilipSedivy\EET\Exceptions\Certificate;

use FilipSedivy\EET\Exceptions\FileSystem\FileNotFoundException;
use Throwable;

class CertificateNotFoundException extends FileNotFoundException
{
    public function __construct(string $path, ?Throwable $previous = null)
    {
        $message = "Certificate not found. Path: '{$path}'";
        parent::__construct($message, 0, $previous);
    }
}