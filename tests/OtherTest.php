<?php

namespace Tests;

use FilipSedivy\EET\Utils\UUID;

class OtherTest extends \PHPUnit_Framework_TestCase{

    public function testDateTypeUUID(){
        $uuid = UUID::v4();
        $this->assertInternalType('string', $uuid);
    }



}