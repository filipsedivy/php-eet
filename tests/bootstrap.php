<?php declare(strict_types=1);

use Tester\Environment;

if (!file_exists(__DIR__ . '/../vendor/autoload.php')) {
    echo 'Install Nette Tester using `composer update --dev`';
    die(0);
}

require_once __DIR__ . '/../vendor/autoload.php';

Environment::setup();
date_default_timezone_set('Europe/Prague');
