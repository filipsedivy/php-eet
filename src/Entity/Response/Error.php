<?php declare(strict_types=1);

namespace FilipSedivy\EET\Entity\Response;

final class Error
{
    private int $code;

    private bool $test;

    public function __construct(int $code, bool $test)
    {
        $this->code = $code;
        $this->test = $test;
    }

    public function getCode(): int
    {
        return $this->code;
    }

    public function isTest(): bool
    {
        return $this->test;
    }
}