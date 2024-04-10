<?php

session_start();

require "include/main.function.shared.php";
require "include/config.php";
require "include/main.function.php";

$smarty = new Smarty;
$smarty->compile_check = true;
//$smarty->debugging = true;

$auth = new auth_service($conn_mysql, $smarty, $logger);
// $auth->page_level_id = 38;
// $auth->check_all();

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app = new \Slim\App(['settings' => $slim_config]);

require "app/src/dependencies.php";

// app routing
use App\Middleware\GuestMiddleware;
use App\Middleware\AuthMiddleware;

$app->group('', function () {
    $this->map(['GET', 'POST'],'/home', \homeController::class . ':home');

    $this->map(['GET', 'POST'],'/about', \aboutController::class . ':about');
    $this->map(['GET', 'POST'], '/about/changes-old', \aboutController::class . ':changesOld');
    $this->map(['GET', 'POST'], '/about/changes', \aboutController::class . ':changes');
    
    $this->map(['GET', 'POST'],'/archiv-zmen/cat', \archivZmenController::class . ':archivZmenCat');
    $this->map(['GET', 'POST'],'/archiv-zmen/ucetni', \archivZmenController::class . ':archivZmenUcetni');
})->add(new AuthMiddleware($container));

// final
$app->run();
