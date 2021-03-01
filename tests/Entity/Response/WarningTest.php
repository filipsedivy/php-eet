<?php declare(strict_types=1);

namespace FilipSedivy\EET\Tests\Entity\Response;

use FilipSedivy\EET\Entity\Response\Warning;
use PHPUnit\Framework\TestCase;

class WarningTest extends TestCase
{
    public function testProperties(): void
    {
        $entity = new Warning(-1);

        $this->assertEquals(-1, $entity->getCode());
    }
}