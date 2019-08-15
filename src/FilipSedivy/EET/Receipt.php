<?php declare(strict_types=1);

namespace FilipSedivy\EET;

class Receipt
{
    /**
     * @var string
     * @require
     */
    public $uuid_zpravy;

    /**
     * @var boolean
     * @require
     */
    public $prvni_zaslani = true;

    /**
     * @var string
     * @require
     */
    public $dic_popl;

    /** @var string */
    public $dic_poverujiciho;

    /**
     * @var string
     * @require
     */
    public $id_provoz;

    /**
     * @var string
     * @require
     */
    public $id_pokl;

    /**
     * @var string
     * @require
     */
    public $porad_cis;

    /**
     * @var \DateTime
     * @require
     */
    public $dat_trzby;

    /**
     * @var float
     * @require
     */
    public $celk_trzba = 0;

    /** @var float|null */
    public $zakl_nepodl_dph = null;

    /** @var float|null */
    public $zakl_dan1 = null;

    /** @var float|null */
    public $dan1 = null;

    /** @var float|null */
    public $zakl_dan2 = null;

    /** @var float|null */
    public $dan2 = null;

    /** @var float|null */
    public $zakl_dan3 = null;

    /** @var float|null */
    public $dan3 = null;

    /** @var float|null */
    public $cest_sluz = null;

    /** @var float|null */
    public $pouzit_zboz1 = null;

    /** @var float|null */
    public $pouzit_zboz2 = null;

    /** @var float|null */
    public $pouzit_zboz3 = null;

    /** @var float|null */
    public $urceno_cerp_zuct = null;

    /** @var float|null */
    public $cerp_zuct = null;

    /** @var int|null */
    public $rezim = 0;

    /** @var string */
    public $bkp;

    /** @var string */
    public $pkp;
}