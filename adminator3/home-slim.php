<?php

require "include/main.function.shared.php";
require "include/config.php";
require "include/main.function.php";

$smarty = new Smarty;
$smarty->compile_check = true;
//$smarty->debugging = true;

$auth = new auth_service($conn_mysql, $smarty, $logger);
$auth->page_level_id = 38;
// TODO: remove this (login beypass)
$auth->check_login_no_die = true;
$auth->check_all();

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app = new \Slim\App(['settings' => $slim_config]);
$container = $app->getContainer();

$container['logger'] = function($c) {
  $logger = new \Monolog\Logger('my_logger');
  $file_handler = new \Monolog\Handler\StreamHandler('../a3-logs/app.log');
  $logger->pushHandler($file_handler);
  return $logger;
};

// controllers
$container['homeController'] = function ($c) {
    global $conn_mysql, $smarty, $logger, $app, $auth;
    return new homeController($conn_mysql, $smarty, $logger, $auth, $app);
};

// routering
$app->get('/hello', function (Request $request, Response $response) {
    $this->logger->addInfo("hello route called");
    $response->getBody()->write("Hello from Slim");

    return $response;
});

$app->get('/home', \homeController::class . ':home');

// final
$app->run();
