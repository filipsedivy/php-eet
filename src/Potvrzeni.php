<?php
/******************************************************************************
 * Author: Petr Suchy (xsuchy09) <suchy@wamos.cz> <https://www.wamos.cz>
 * Project: php-eet
 * Date: 13.03.20
 * Time: 11:15
 * Copyright: (c) Petr Suchy (xsuchy09) <suchy@wamos.cz> <http://www.wamos.cz>
 *****************************************************************************/


declare(strict_types=1);


namespace FilipSedivy\EET;


use DateTime;


/**
 * Class Potvrzeni
 * @package FilipSedivy\EET
 */
class Potvrzeni
{
	/** @var string */
	public $uuid_zpravy;

	/** @var DateTime|null */
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