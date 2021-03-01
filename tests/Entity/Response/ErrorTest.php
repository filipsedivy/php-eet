<?php declare(strict_types=1);

namespace FilipSedivy\EET\Tests\Entity\Response;

use FilipSedivy\EET\Entity\Response\Error;
use PHPUnit\Framework\TestCase;

class ErrorTest extends TestCase
{
    public function testProperties(): void
    {
        $entity = new Error(-1, true);

        $this->assertTrue($entity->isTest());
        $this->assertEquals(-1, $entity->getCode());
    }
}