<?php
/**
 * This file is part of the PHP-EET package.
 *
 * (c) Filip Sedivy <mail@filipsedivy.cz>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license MIT
 * @author Filip Sedivy <mail@filipsedivy.cz>
 */

namespace FilipSedivy\EET;

/**
 * Class Receipt
 * @package FilipSedivy\EET
 */
class Receipt {
    /**
     * @var string
     * @require
     */
    public $uuid_zpravy;

    /**
     * @var boolean
     * @require
     */
    public $prvni_zaslani = TRUE;

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

    /** @var float */
    public $zakl_nepodl_dph = 0;

    /** @var float */
    public $zakl_dan1 = 0;

    /** @var float */
    public $dan1 = 0;

    /** @var float */
    public $zakl_dan2 = 0;

    /** @var float */
    public $dan2 = 0;

    /** @var float */
    public $zakl_dan3 = 0;

    /** @var float */
    public $dan3 = 0;

    /** @var float */
    public $cest_sluz = 0;

    /** @var float */
    public $pouzit_zboz1 = 0;

    /** @var float */
    public $pouzit_zboz2 = 0;

    /** @var float */
    public $pouzit_zboz3 = 0;

    /** @var float */
    public $urceno_cerp_zuct = 0;

    /** @var float */
    public $cerp_zuct = 0;

    /** @var int */
    public $rezim = 0;
}