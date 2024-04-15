<?php

use Respect\Validation\Validator as v;

$container = $app->getContainer();

$container['logger'] = function($c) {
    $logger = new \Monolog\Logger('my_logger');
    $file_handler = new \Monolog\Handler\StreamHandler('../a3-logs/app.log');
    $logger->pushHandler($file_handler);
    return $logger;
};

$container['db'] = function ($container) use ($capsule) {
    return $capsule;
};

$container['auth'] = function($container) {
    return new \App\Auth\Auth;
};

$container['flash'] = function($container) {
	return new \Slim\Flash\Messages;
};

$container['view'] = function ($container) {
	$view = new \Slim\Views\Twig(__DIR__ . '/../resources/views/', [
		'cache' => false,
	]);

	$view->addExtension(new \Slim\Views\TwigExtension(
		$container->router,
		$container->request->getUri()
	));

	$view->getEnvironment()->addGlobal('auth', [
		'check' => $container->auth->check(),
		'user' => $container->auth->user()
	]);

	$view->getEnvironment()->addGlobal('flash', $container->flash);

	return $view;
};

$container['validator'] = function ($container) {
	return new App\Validation\Validator;
};

$container['HomeController'] = function($container) {
    global $conn_mysql, $smarty;
	return new \App\Controllers\HomeController($container,$conn_mysql, $smarty);
};

$container['AuthController'] = function($container) {
	return new \App\Controllers\Auth\AuthController($container);
};

$container['PasswordController'] = function($container) {
	return new \App\Controllers\Auth\PasswordController($container);
};

$container['csrf'] = function($container) {
	return new \Slim\Csrf\Guard;
};

$container['jsonviewer'] = function ($container) {
    return new JsonViewer($container, $container['logger']);
};

$app->add(new \App\Middleware\ValidationErrorsMiddleware($container));
$app->add(new \App\Middleware\OldInputMiddleware($container));
$app->add(new \App\Middleware\CsrfViewMiddleware($container));

$app->add($container->csrf);

v::with('App\\Validation\\Rules\\');

$container['aboutController'] = function ($c) {
    global $conn_mysql, $smarty, $logger, $auth, $app;
    return new \App\Controllers\aboutController($app->getContainer(),$conn_mysql, $smarty, $logger, $auth, $app);
};

$container['adminController'] = function ($c) {
    global $conn_mysql, $smarty, $logger, $auth, $app;
    return new \App\Controllers\adminController($app->getContainer(),$conn_mysql, $smarty, $logger, $auth, $app);
};

$container['archivZmenController'] = function ($c) {
    global $conn_mysql, $smarty, $logger, $auth, $app;
    return new \App\Controllers\archivZmenController($app->getContainer(),$conn_mysql, $smarty, $logger, $auth, $app);
};

$container['othersController'] = function ($c) {
    global $conn_mysql, $smarty, $logger, $auth, $app;
    return new \App\Controllers\othersController($app->getContainer(),$conn_mysql, $smarty, $logger, $auth, $app);
};

$container['vlastniciController'] = function ($c) {
    global $conn_mysql, $smarty, $logger, $auth, $app;
    return new \App\Controllers\vlastniciController($app->getContainer(),$conn_mysql, $smarty, $logger, $auth, $app);
};

$container['objektyController'] = function ($c) {
    global $conn_mysql, $smarty, $logger, $auth, $app;
    return new \App\Controllers\objektyController($app->getContainer(),$conn_mysql, $smarty, $logger, $auth, $app);
};

$container['platbyController'] = function ($c) {
    global $conn_mysql, $smarty, $logger, $auth, $app;
    return new \App\Controllers\platbyController($app->getContainer(),$conn_mysql, $smarty, $logger, $auth, $app);
};

$container['workController'] = function ($c) {
    global $conn_mysql, $smarty, $logger, $auth, $app;
    return new \App\Controllers\workController($app->getContainer(),$conn_mysql, $smarty, $logger, $auth, $app);
};