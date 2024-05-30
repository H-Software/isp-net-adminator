<?php

// error_reporting(-1);
error_reporting(E_ALL & ~E_DEPRECATED);

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
date_default_timezone_set('Europe/Prague');

$loaderOrig = require dirname(__DIR__) . '/vendor/autoload.php';
// TODO: test better loading files or WTF is this
// $loaderOrig = findFile();

require __DIR__ . '/bootstrap/netteLoader.php';

require __DIR__ . '/../config/settings-tests.php';

require __DIR__ . '/bootstrap/session.php';
