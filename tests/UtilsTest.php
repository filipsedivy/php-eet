<?php
/**
 * This file is part of the PHP-EET package.
 *
 * (c) Filip Sedivy <mail@filipsedivy.cz>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license MIT
 * @author Filip Sedivy <mail@filipsedivy.cz>
 */

namespace Tests;

use FilipSedivy\EET\Utils\UUID;

class UtilsTest extends \PHPUnit_Framework_TestCase
{

    public function testMinimumUUIDLength(){
        $uuid = UUID::v4();
        $lengthAssert = strlen($uuid) > 35;
        $this->assertTrue($lengthAssert);
    }

}