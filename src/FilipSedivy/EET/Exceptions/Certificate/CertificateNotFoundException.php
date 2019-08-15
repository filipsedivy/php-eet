<?php

namespace FilipSedivy\EET\Exceptions\Certificate;

use FilipSedivy\EET\Exceptions\FileSystem\FileNotFoundException;
use FilipSedivy\EET\Exceptions\FileSystem\FileSystemException;
use Throwable;

class CertificateNotFoundException extends FileNotFoundException implements FileSystemException
{
    public function __construct(string $path, Throwable $previous = null)
    {
        $message = "Certificate not found. Path: '{$path}'";
        parent::__construct($message, 0, $previous);
    }
}