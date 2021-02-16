<?php declare(strict_types=1);

namespace FilipSedivy\EET;

use DateTime;
use FilipSedivy\EET\Utils\Format;

class Receipt
{
    private const HEADER = ['uuid_zpravy', 'prvni_zaslani'];

    private const BODY_REQUIRE = [
        'dic_popl', 'id_provoz', 'id_pokl',
        'porad_cis', 'celk_trzba', 'rezim', 'dat_trzby'
    ];

    private const BODY_OPTIONAL = [
        'dic_poverujiciho', 'zakl_nepodl_dph', 'zakl_dan1', 'dan1', 'zakl_dan2',
        'dan2', 'zakl_dan3', 'dan3', 'cest_sluz', 'pouzit_zboz1',
        'pouzit_zboz2', 'pouzit_zboz3', 'urceno_cerp_zuct', 'cerp_zuct'
    ];

    private const BODY_PRICE_FORMAT = [
        'celk_trzba', 'zakl_nepodl_dph', 'zakl_dan1', 'dan1', 'zakl_dan2',
        'dan2', 'zakl_dan3', 'dan3', 'cest_sluz', 'pouzit_zboz1',
        'pouzit_zboz2', 'pouzit_zboz3', 'urceno_cerp_zuct', 'cerp_zuct'
    ];

    public string $uuid_zpravy;

    public bool $prvni_zaslani = true;

    public string $dic_popl;

    public ?string $dic_poverujiciho = null;

    public string $id_provoz;

    public string $id_pokl;

    public string $porad_cis;

    public DateTime $dat_trzby;

    public float $celk_trzba;

    public ?float $zakl_nepodl_dph = null;

    public ?float $zakl_dan1 = null;

    public ?float $dan1 = null;

    public ?float $zakl_dan2 = null;

    public ?float $dan2 = null;

    public ?float $zakl_dan3 = null;

    public ?float $dan3 = null;

    public ?float $cest_sluz = null;

    public ?float $pouzit_zboz1 = null;

    public ?float $pouzit_zboz2 = null;

    public ?float $pouzit_zboz3 = null;

    public ?float $urceno_cerp_zuct = null;

    public ?float $cerp_zuct = null;

    public int $rezim = 0;

    public ?string $bkp = null;

    public ?string $pkp = null;

    /** @return array<string|float|int|null> */
    public function buildHeader(): array
    {
        $header = [];

        foreach (self::HEADER as $parameter) {
            $value = $this->{$parameter};

            $header[$parameter] = $value;
        }

        return $header;
    }

    /** @return array<string|float|int|null> */
    public function buildBody(bool $autoFormatPrice = true): array
    {
        $body = [];

        // build require parameters
        foreach (self::BODY_REQUIRE as $parameter) {
            $value = $this->{$parameter};

            if ($value instanceof DateTime) {
                $value = $value->format('c');
            }

            $body[$parameter] = $value;
        }

        // build optional parameters
        foreach (self::BODY_OPTIONAL as $parameter) {
            $value = $this->{$parameter};

            if ($value !== null) {
                $body[$parameter] = $value;
            }
        }

        // format price
        if ($autoFormatPrice) {
            foreach (self::BODY_PRICE_FORMAT as $item) {
                if (array_key_exists($item, $body) && $body[$item] !== null) {
                    $body[$item] = Format::price($body[$item]);
                }
            }
        }

        return $body;
    }
}
