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

use Tester\Environment;
use Tester\Helpers;

if(!file_exists(__DIR__.'/../vendor/autoload.php'))
{
    echo 'Install Nette Tester using `composer update --dev`';
    die(0);
}

require_once __DIR__.'/../vendor/autoload.php';

Environment::setup();
date_default_timezone_set('Europe/Prague');