<?php

namespace FilipSedivy\EET\Enum;

class Warning
{
    public const LIST = [
        1 => 'DIC poplatnika v datove zprave se neshoduje s DIC v certifikatu',
        2 => 'Chybny format DIC poverujiciho poplatnika',
        3 => 'Chybna hodnota PKP',
        4 => 'Datum a cas prijeti trzby je novejsi nez datum a cas prijeti zpravy',
        5 => 'Datum a cas prijeti trzby je vyrazne v minulosti '
    ];
}