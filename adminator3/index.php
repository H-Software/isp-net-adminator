<?php

require __DIR__ . '/vendor/autoload.php';

// init db functions defs
require "include/main.function.shared.php";
// autoload, init DB conns, init Illuminate\Database
require "app/bootstrap.php";

require "app/routing.php";

$app->addRoutingMiddleware();

// final
$app->run();
