<?php declare(strict_types=1);

namespace FilipSedivy\EET\Exceptions\Certificate;

use FilipSedivy\EET\Exceptions\RuntimeException;
use Throwable;

class CertificateExportFailedException extends RuntimeException implements CertificateException
{
    /** @var string */
    private $path;

    public function __construct(string $path, ?Throwable $previous = null)
    {
        $this->path = $path;

        $message = "The certificate ('$path') cannot be exported";
        parent::__construct($message, 0, $previous);
    }

    public function getPath(): string
    {
        return $this->path;
    }
}
