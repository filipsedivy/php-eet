<?php

namespace FilipSedivy\EET\Enum;

final class Error
{
    public const LIST = [
        -1 => 'Docasna technicka chyba zpracovani – odeslete prosim datovou zpravu pozdeji',
        2 => 'Kodovani XML neni platne',
        3 => 'XML zprava nevyhovela kontrole XML schematu',
        4 => 'Neplatny podpis SOAP zpravy',
        5 => 'Neplatny kontrolni bezpecnostni kod poplatnika (BKP)',
        6 => 'DIC poplatnika ma chybnou strukturu',
        7 => 'Datova zprava je prilis velka',
        8 => 'Datova zprava nebyla zpracovana kvuli technicke chybe nebo chybe dat',
    ];
}
