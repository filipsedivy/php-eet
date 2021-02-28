<?php declare(strict_types=1);

namespace FilipSedivy\EET\Enum;

use MyCLabs\Enum\Enum;

/**
 * @template T
 * @extends Enum<T>
 */
final class Error extends Enum
{
    private const ITEM__1 = 'Docasna technicka chyba zpracovani – odeslete prosim datovou zpravu pozdeji';

    private const ITEM_2 = 'Kodovani XML neni platne';

    private const ITEM_3 = 'XML zprava nevyhovela kontrole XML schematu';

    private const ITEM_4 = 'Neplatny podpis SOAP zpravy';

    private const ITEM_5 = 'Neplatny kontrolni bezpecnostni kod poplatnika (BKP)';

    private const ITEM_6 = 'DIC poplatnika ma chybnou strukturu';

    private const ITEM_7 = 'Datova zprava je prilis velka';

    private const ITEM_8 = 'Datova zprava nebyla zpracovana kvuli technicke chybe nebo chybe dat';
}
