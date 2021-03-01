<?php declare(strict_types=1);

namespace FilipSedivy\EET\Tests\Exceptions\EET;

use FilipSedivy\EET\Exceptions;
use FilipSedivy\EET\Entity\Response;
use PHPUnit\Framework\TestCase;

class ErrorExceptionTest extends TestCase
{
    public function testMessage(): void
    {
        $errorResponse = new Response\Error(-1, true);

        $this->expectException(Exceptions\EET\ErrorException::class);
        $this->expectExceptionMessage('Docasna technicka chyba zpracovani – odeslete prosim datovou zpravu pozdeji');
        $this->expectExceptionCode(-1);

        throw Exceptions\EET\ErrorException::fromErrorResponse($errorResponse);
    }

    public function testMessage2(): void
    {
        $errorResponse = new Response\Error(2, true);

        $this->expectException(Exceptions\EET\ErrorException::class);
        $this->expectExceptionMessage('Kodovani XML neni platne');
        $this->expectExceptionCode(2);

        throw Exceptions\EET\ErrorException::fromErrorResponse($errorResponse);
    }

    public function testMessage3(): void
    {
        $errorResponse = new Response\Error(3, true);

        $this->expectException(Exceptions\EET\ErrorException::class);
        $this->expectExceptionMessage('XML zprava nevyhovela kontrole XML schematu');
        $this->expectExceptionCode(3);

        throw Exceptions\EET\ErrorException::fromErrorResponse($errorResponse);
    }

    public function testEmptyMessage(): void
    {
        $errorResponse = new Response\Error(-99, true);

        $this->expectException(Exceptions\EET\ErrorException::class);
        $this->expectExceptionMessage('');
        $this->expectExceptionCode(-99);

        throw Exceptions\EET\ErrorException::fromErrorResponse($errorResponse);
    }
}