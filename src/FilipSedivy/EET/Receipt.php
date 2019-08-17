<?php declare(strict_types=1);

namespace FilipSedivy\EET;

use DateTime;
use FilipSedivy\EET\Utils\Format;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;

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

    /** @var string */
    public $uuid_zpravy;

    /** @var bool */
    public $prvni_zaslani = true;

    /** @var string */
    public $dic_popl;

    /** @var string|null */
    public $dic_poverujiciho;

    /** @var string */
    public $id_provoz;

    /** @var string */
    public $id_pokl;

    /** @var string */
    public $porad_cis;

    /** @var \DateTime */
    public $dat_trzby;

    /** @var float */
    public $celk_trzba;

    /** @var float|null */
    public $zakl_nepodl_dph;

    /** @var float|null */
    public $zakl_dan1;

    /** @var float|null */
    public $dan1;

    /** @var float|null */
    public $zakl_dan2;

    /** @var float|null */
    public $dan2;

    /** @var float|null */
    public $zakl_dan3;

    /** @var float|null */
    public $dan3;

    /** @var float|null */
    public $cest_sluz;

    /** @var float|null */
    public $pouzit_zboz1;

    /** @var float|null */
    public $pouzit_zboz2;

    /** @var float|null */
    public $pouzit_zboz3;

    /** @var float|null */
    public $urceno_cerp_zuct;

    /** @var float|null */
    public $cerp_zuct;

    /** @var int */
    public $rezim = 0;

    /** @var string|null */
    public $bkp;

    /** @var string|null */
    public $pkp;

    public function buildHeader(): array
    {
        $header = [];

        foreach (self::HEADER as $parameter) {
            $value = $this->{$parameter};

            $header[$parameter] = $value;
        }

        return $header;
    }

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
                if (array_key_exists($item, $body)) {
                    $body[$item] = Format::price($body[$item]);
                }
            }
        }

        return $body;
    }

    public static function loadValidatorMetadata(ClassMetadata $metadata): void
    {
        $metadata
            ->addPropertyConstraint('uuid_zpravy', new Assert\NotBlank)
            ->addPropertyConstraint('uuid_zpravy', new Assert\Type('string'))
            ->addPropertyConstraint('uuid_zpravy', new Assert\Uuid([
                'versions' => [Assert\Uuid::V4_RANDOM]
            ]));

        $metadata
            ->addPropertyConstraint('prvni_zaslani', new Assert\Type('bool'));

        $metadata
            ->addPropertyConstraint('dic_popl', new Assert\NotBlank)
            ->addPropertyConstraint('dic_popl', new Assert\Regex([
                'pattern' => '/^CZ([0-9]{8,10})$/'
            ]));

        $metadata
            ->addPropertyConstraint('dic_poverujiciho', new Assert\Regex([
                'pattern' => '/^CZ([0-9]{8,10})$/'
            ]));

        $metadata
            ->addPropertyConstraint('id_provoz', new Assert\NotBlank)
            ->addPropertyConstraint('id_provoz', new Assert\Regex([
                'pattern' => '/^[1-9][0-9]{0,5}$/'
            ]));

        $metadata
            ->addPropertyConstraint('id_pokl', new Assert\NotBlank)
            ->addPropertyConstraint('id_pokl', new Assert\Regex([
                'pattern' => '/^[0-9a-zA-Z\.,:;\/#\-_ ]{1,20}$/'
            ]));

        $metadata
            ->addPropertyConstraint('porad_cis', new Assert\NotBlank)
            ->addPropertyConstraint('porad_cis', new Assert\Regex([
                'pattern' => '/^[0-9a-zA-Z\.,:;\/#\-_ ]{1,25}$/'
            ]));

        $metadata
            ->addPropertyConstraint('dat_trzby', new Assert\NotBlank)
            ->addPropertyConstraint('dat_trzby', new Assert\Type(DateTime::class));

        $metadata
            ->addPropertyConstraint('celk_trzba', new Assert\NotBlank)
            ->addPropertyConstraint('celk_trzba', new Assert\Type('numeric'));

        $metadata
            ->addPropertyConstraint('zakl_nepodl_dph', new Assert\Type('numeric'));

        $metadata
            ->addPropertyConstraint('zakl_dan1', new Assert\Type('numeric'));

        $metadata
            ->addPropertyConstraint('dan1', new Assert\Type('numeric'));

        $metadata
            ->addPropertyConstraint('zakl_dan2', new Assert\Type('numeric'));

        $metadata
            ->addPropertyConstraint('dan2', new Assert\Type('numeric'));

        $metadata
            ->addPropertyConstraint('zakl_dan3', new Assert\Type('numeric'));

        $metadata
            ->addPropertyConstraint('dan3', new Assert\Type('numeric'));

        $metadata
            ->addPropertyConstraint('cest_sluz', new Assert\Type('numeric'));

        $metadata
            ->addPropertyConstraint('pouzit_zboz1', new Assert\Type('numeric'));

        $metadata
            ->addPropertyConstraint('pouzit_zboz2', new Assert\Type('numeric'));

        $metadata
            ->addPropertyConstraint('pouzit_zboz3', new Assert\Type('numeric'));

        $metadata
            ->addPropertyConstraint('urceno_cerp_zuct', new Assert\Type('numeric'));

        $metadata
            ->addPropertyConstraint('cerp_zuct', new Assert\Type('numeric'));

        $metadata
            ->addPropertyConstraint('rezim', new Assert\NotBlank)
            ->addPropertyConstraint('rezim', new Assert\Regex([
                'pattern' => '/^[01]$/'
            ]));
    }
}
