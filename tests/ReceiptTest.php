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
<<<<<<< HEAD
            $this->assertClassHasAttribute($attribute, new Receipt());
=======
            $this->assertClassHasAttribute($attribute, Receipt::class);
>>>>>>> 8a99cbf7f39897f71680a31b2e1afeb8e1670705
        }
    }
}