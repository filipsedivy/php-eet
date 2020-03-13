<?php declare(strict_types=1);

namespace FilipSedivy\EET;

/**
 * Class Potvrzeni
 * @package FilipSedivy\EET
 */
class Potvrzeni
{
    /** @var string */
    public $uuid_zpravy;

    /** @var \DateTime|null */
    public $dat_prij;

    /** @var string|null */
    public $bkp;

    /** @var string|null */
    public $fik;

    /** @var bool */
    public $test;

    /** @var array */
    public $varovani;
}