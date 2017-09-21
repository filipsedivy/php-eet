<?php
/**
 * Test: FilipSedivy\EET\Certificate.
 *
 * @testCase
 */

namespace EetTest\Certificate;

use FilipSedivy\EET\Certificate;
use FilipSedivy\EET\Exceptions\CertificateException;
use Tester\Assert;

require_once __DIR__.'/../bootstrap.php';

class CertificateTest extends \Tester\TestCase
{
    public function testFileNotExists()
    {
        try
        {
            new Certificate(__DIR__.'/certificate.p12', 'testPassword');
            Assert::fail('Neplatny certifikat nesmi byt nacten');
        }catch(CertificateException $ex){
            Assert::true(true);
        }
    }

    public function testFileExists()
    {
        try
        {
            new Certificate(__DIR__.'/../../examples/EET_CA1_Playground-CZ00000019.p12', 'eet');
            Assert::true(true);
        }catch(CertificateException $ex){
            Assert::fail('Platny certifikat musi byt nacten');
        }
    }

    public function testCertificate()
    {
        try
        {
            $certificate = new Certificate(__DIR__.'/../../examples/EET_CA1_Playground-CZ00000019.p12',  'eet');
            Assert::true(is_string($certificate->getPrivateKey()) && is_string($certificate->getCert()));
        }catch(CertificateException $ex){
            Assert::fail('Privatni klic a klic certifikatu je chybne exportovany');
        }
    }
}

(new CertificateTest())->run();