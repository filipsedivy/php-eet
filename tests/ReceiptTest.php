<?php

namespace Tests;

use FilipSedivy\EET\Receipt;

class ReceiptTest extends \PHPUnit_Framework_TestCase
{
    public function testReceiptObjectAttributes(){
        $attributes = array(
            'uuid_zpravy',
            'dic_popl',
            'id_provoz',
            'id_pokl',
            'celk_trzba'
        );

        foreach($attributes as $attribute){
            $this->assertClassHasAttribute($attribute, new Receipt());
        }
    }
}