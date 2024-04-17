<?php

// session_cache_limiter(false);
// session_start();

require "include/main.function.shared.php";
require "app/config.php";

$smarty = new Smarty;
$smarty->compile_check = true;
//$smarty->debugging = true;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app = new \Slim\App(['settings' => $slim_config]);

require "app/dependencies.php";

require "app/routing.php";

// final
$app->run();
