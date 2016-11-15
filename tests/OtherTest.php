<?php

namespace Tests;

use FilipSedivy\EET\Utils\UUID;

class OtherTest extends \PHPUnit_Framework_TestCase{

    public function testMinimumUUIDLength(){
        $uuid = UUID::v4();
        $lengthAssert = strlen($uuid) > 35;
        $this->assertTrue($lengthAssert);
    }

}