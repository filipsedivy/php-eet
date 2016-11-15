<?php

namespace Tests;

use FilipSedivy\EET\Dispatcher;
use FilipSedivy\EET\Exceptions\ServerException;
use FilipSedivy\EET\Receipt;
use FilipSedivy\EET\Utils\UUID;

class ReceiptTest extends \PHPUnit_Framework_TestCase{

    public function testSendOk(){
        $r = $this->getExampleReceipt();
        try{
            $this->getTestDispatcher()->send($r);
            $this->assertTrue(true, 'EET uctenka je evidovana');
        }catch(ServerException $ex){
            $this->assertTrue(false, $ex->getMessage());
        }
    }

    public function testSendError(){
        $r = $this->getExampleReceipt();
        $r->dic_popl = 'x';
        try{
            $this->getTestDispatcher()->send($r);
            $this->assertTrue(false, 'Chybne DIC proslo');
        }catch(ServerException $ex){
            $this->assertTrue(true, $ex->getMessage());
        }
    }

    private function getTestDispatcher(){
        $eetKey     = __DIR__.'/../examples/cert/eet.key';
        $eetCert    =  __DIR__.'/../examples/cert/eet.pem';
        $d = new Dispatcher(__DIR__.'/../src/Schema/PlaygroundService.wsdl', $eetKey, $eetCert);
        return $d;
    }

    private function getExampleReceipt(){
        $r = new Receipt;
        $r->uuid_zpravy = UUID::v4();
        $r->dic_popl = 'CZ72080043';
        $r->id_provoz = '181';
        $r->id_pokl = '1';
        $r->porad_cis = '1';
        $r->dat_trzby = new \DateTime();
        $r->celk_trzba = 1000;
        return $r;
    }

}