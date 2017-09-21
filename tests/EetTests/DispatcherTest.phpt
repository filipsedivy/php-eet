<?php
/**
 * Test: FilipSedivy\EET\Dispatcher.
 *
 * @testCase
 */

namespace EetTest\Dispatcher;

use FilipSedivy\EET\Certificate;
use FilipSedivy\EET\Dispatcher;
use FilipSedivy\EET\Exceptions\EetException;
use FilipSedivy\EET\Receipt;
use FilipSedivy\EET\Utils\UUID;
use Tester\Assert;
use Tester\TestCase;

require_once __DIR__.'/../bootstrap.php';

class DispatcherTest extends TestCase
{
    public function testSendReceipt()
    {
        $certificate = new Certificate(__DIR__.'/../../examples/EET_CA1_Playground-CZ00000019.p12', 'eet');
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

        print "\n--- EET - Jedna platba ---\n";
        print sprintf("Castka: %s\n", 500);
        print sprintf("UUID: %s\n", $uuid);

        try{
            $dispatcher->send($r);
            print "FIK: ".$dispatcher->getFik()."\n";
            print "BKP: ".$dispatcher->getBkp()."\n";
            Assert::true(is_string($dispatcher->getFik()) && is_string($dispatcher->getBkp()));
        }catch(EetException $ex){
            print "PKP: ".$dispatcher->getPkp()."\n";
            print "BKP: ".$dispatcher->getBkp()."\n";
            Assert::true(is_string($dispatcher->getPkp()) && is_string($dispatcher->getBkp()));
        }catch(\Exception $ex){
            Assert::fail('Pri odesilani EET zpravy nastala chyba: '.$ex->getMessage());
        }
    }

    public function testSendReceipts()
    {
        $certificate = new Certificate(__DIR__.'/../../examples/EET_CA1_Playground-CZ00000019.p12', 'eet');
        $dispatcher = new Dispatcher($certificate);
        $dispatcher->setPlaygroundService();

        print "\n--- EET - Rezim zcykleneho placeni ---\n";

        for($i = 0; $i < rand(4, 9); $i++)
        {
            $uuid = UUID::v4();
            $castka = rand(400, 100000);

            $r = new Receipt();
            $r->uuid_zpravy = $uuid;
            $r->id_provoz = '11';
            $r->id_pokl = 'IP105';
            $r->dic_popl = 'CZ1212121218';
            $r->porad_cis = '1';
            $r->dat_trzby = new \DateTime();
            $r->celk_trzba = $castka;

            print "\n- Pokus cislo: ".($i + 1)."\n";
            print sprintf("Castka: %s\n", $castka);
            print sprintf("UUID: %s\n", $uuid);

            try{
                $dispatcher->send($r);
                print "FIK: ".$dispatcher->getFik()."\n";
                print "BKP: ".$dispatcher->getBkp()."\n";
            }catch(EetException $ex){
                print "PKP: ".$dispatcher->getPkp()."\n";
                print "BKP: ".$dispatcher->getBkp()."\n";
            }catch(\Exception $ex){
                Assert::fail('Pri odesilani '.$i.' platby nastala chyba: '.$ex->getMessage());
            }
        }

        Assert::true(true);
    }

}

(new DispatcherTest())->run();