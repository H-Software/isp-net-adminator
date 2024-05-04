<?php

error_reporting(-1);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
date_default_timezone_set('Europe/Prague');

$loader = require dirname(__DIR__) . '/vendor/autoload.php';
// $loader->add('JeremyKendall\\Slim\\Auth\\Tests\\', __DIR__);

define('SLIM_MODE', 'testing');
