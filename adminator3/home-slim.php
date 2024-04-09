<?php

require "include/main.function.shared.php";
require "include/config.php";
require "include/main.function.php";

$smarty = new Smarty;
$smarty->compile_check = true;
//$smarty->debugging = true;

$auth = new auth_service($conn_mysql, $smarty, $logger);
$auth->page_level_id = 38;
$auth->check_all();

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app = new \Slim\App(['settings' => $slim_config]);

require "app/src/dependencies.php";
require "app/src/middleware.php";

// routing

// $app->get('/hello', function (Request $request, Response $response) {
//     $this->logger->addInfo("hello route called");
//     $response->getBody()->write("Hello from Slim");

//     return $response;
// });

$app->get('/home', \homeController::class . ':home');
$app->post('/home', \homeController::class . ':home');

$app->get('/about', \aboutController::class . ':about');
$app->post('/about', \aboutController::class . ':about');

// final
$app->run();
