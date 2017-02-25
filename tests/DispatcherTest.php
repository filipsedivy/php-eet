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
use FilipSedivy\EET\Dispatcher;
use FilipSedivy\EET\Exceptions\EetException;
use FilipSedivy\EET\Receipt;
use FilipSedivy\EET\Utils\UUID;

class DispatcherTest extends \PHPUnit_Framework_TestCase
{
    public function testSendReceipt()
    {
        $certificate = new Certificate(__DIR__."/../examples/EET_CA1_Playground-CZ00000019.p12", "eet");
        $dispatcher = new Dispatcher($certificate);
        $dispatcher->setPlaygroundService();

        $uuid = UUID::v4();

        $r = new Receipt();
        $r->uuid_zpravy = $uuid;
        $r->id_provoz = '11';
        $r->id_pokl = 'IP105';
        $r->dic_popl = 'CZ1212121218';
        $r->porad_cis = '1';
        $r->dat_trzby = new \DateTime();
        $r->celk_trzba = 500;

        try{
            $dispatcher->send($r);
            $this->expectOutputString("FIK: ".$dispatcher->getFik());
            $this->expectOutputString("BKP: ".$dispatcher->getBkp());
            $this->assertTrue( is_string($dispatcher->getFik()) && is_string($dispatcher->getBkp()) );
        }catch(EetException $ex){
            $this->expectOutputString("PKP: ".$dispatcher->getPkp());
            $this->expectOutputString("BKP: ".$dispatcher->getBkp());
            $this->assertTrue( is_string($dispatcher->getPkp()) && is_string($dispatcher->getBkp()) );
        }catch(\Exception $ex){
            $this->fail();
        }
    }


}