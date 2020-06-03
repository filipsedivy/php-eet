<?php declare(strict_types=1);

namespace XSuchy09\EET\Exceptions\EET;

use XSuchy09\EET\Exceptions\RuntimeException;
use XSuchy09\EET\Receipt;
use Throwable;

class ClientException extends RuntimeException
{
    /** @var Receipt */
    private $receipt;

    /** @var string|null */
    private $pkp;

    /** @var string|null */
    private $bkp;

    public function __construct(Receipt $receipt, ?string $pkp, ?string $bkp, Throwable $previous)
    {
        $this->receipt = $receipt;
        $this->bkp = $bkp;
        $this->pkp = $pkp;

        parent::__construct($previous->getMessage(), $previous->getCode(), $previous);
    }

    public function getReceipt(): Receipt
    {
        return $this->receipt;
    }

    public function getBkp(): ?string
    {
        return $this->bkp;
    }

    public function getPkp(bool $encoded = true): ?string
    {
        $pkp = $this->pkp;

        if ($pkp === null) {
            return null;
        }

        if ($encoded) {
            $pkp = base64_encode($pkp);
        }

        return $pkp;
    }
}
