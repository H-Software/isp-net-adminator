<?php

$container = $app->getContainer();

$container['logger'] = function($c) {
    $logger = new \Monolog\Logger('my_logger');
    $file_handler = new \Monolog\Handler\StreamHandler('../a3-logs/app.log');
    $logger->pushHandler($file_handler);
    return $logger;
  };
  
// $container['session'] = function ($c) {
//     return new \SlimSession\Helper();
// };

// $logger->addInfo("session id: ".$container['session']::id());

// controllers
$container['homeController'] = function ($c) {
    global $conn_mysql, $smarty, $logger, $auth, $app;
    return new homeController($app->getContainer(),$conn_mysql, $smarty, $logger, $auth, $app);
};

$container['aboutController'] = function ($c) {
    global $conn_mysql, $smarty, $logger, $auth, $app;
    return new aboutController($app->getContainer(),$conn_mysql, $smarty, $logger, $auth, $app);
};

$container['archivZmenController'] = function ($c) {
    global $conn_mysql, $smarty, $logger, $auth, $app;
    return new archivZmenController($app->getContainer(),$conn_mysql, $smarty, $logger, $auth, $app);
};
