<?php

require "include/main.function.shared.php";
require "include/config.php";
require "include/main.function.php";

$smarty = new Smarty;
$smarty->compile_check = true;
//$smarty->debugging = true;

$config['displayErrorDetails'] = true;
$config['addContentLengthHeader'] = false;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app = new \Slim\App(['settings' => $config]);
$container = $app->getContainer();

$container['logger'] = function($c) {
  $logger = new \Monolog\Logger('my_logger');
  $file_handler = new \Monolog\Handler\StreamHandler('../a3-logs/app.log');
  $logger->pushHandler($file_handler);
  return $logger;
};

$app->get('/hello', function (Request $request, Response $response) {
    $this->logger->addInfo("hello route called");
    $response->getBody()->write("Hello from Slim");

    return $response;
});

$app->run();
