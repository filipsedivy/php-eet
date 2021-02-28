<?php declare(strict_types=1);

namespace FilipSedivy\EET\Enum;

use MyCLabs\Enum\Enum;

/**
 * @template T
 * @extends Enum<T>
 */
final class Warning extends Enum
{
    private const ITEM_1 = 'DIC poplatnika v datove zprave se neshoduje s DIC v certifikatu';

    private const ITEM_2 = 'Chybny format DIC poverujiciho poplatnika';

    private const ITEM_3 = 'Chybna hodnota PKP';

    private const ITEM_4 = 'Datum a cas prijeti trzby je novejsi nez datum a cas prijeti zpravy';

    private const ITEM_5 = 'Datum a cas prijeti trzby je vyrazne v minulosti';
}
