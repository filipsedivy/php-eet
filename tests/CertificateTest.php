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

use FilipSedivy\EET\Certificate;
use FilipSedivy\EET\Exceptions\CertificateException;

class CertificateTestTest extends \PHPUnit_Framework_TestCase
{
    public function testFileNotExists()
    {
        try
        {
            new Certificate(__DIR__."/certificate.p12", "testPassword");
            $this->fail();
        }catch(CertificateException $ex){
            $this->assertTrue(true);
        }
    }


    public function testFileExists()
    {
        try
        {
            new Certificate(__DIR__."/../examples/EET_CA1_Playground-CZ00000019.p12", "eet");
            $this->assertTrue(true);
        }catch(CertificateException $ex){
            $this->fail();
        }
    }

    public function testCertificate()
    {
        try
        {
            $certificate = new Certificate(__DIR__."/../examples/EET_CA1_Playground-CZ00000019.p12", "eet");
            $this->assertTrue( is_string($certificate->getPrivateKey()) && is_string($certificate->getCert()) );
        }catch(CertificateException $ex){
            $this->fail();
        }
    }
}