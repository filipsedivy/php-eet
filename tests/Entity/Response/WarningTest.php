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

    public function testEnumFinder(): void
    {
        $entity = new Warning(1);

        $this->assertEquals(1, $entity->getCode());
        $this->assertEquals('DIC poplatnika v datove zprave se neshoduje s DIC v certifikatu', $entity->findFromEnum());
    }

    public function testFalseEnumFinder(): void
    {
        $entity = new Warning(-1);

        $this->assertEquals(-1, $entity->getCode());
        $this->assertNull($entity->findFromEnum());
    }
}