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
	$view = new \Slim\Views\Twig(__DIR__ . '/../../resources/views/', [
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
	return new \App\Controllers\HomeController($container);
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

$app->add(new \App\Middleware\ValidationErrorsMiddleware($container));
$app->add(new \App\Middleware\OldInputMiddleware($container));
$app->add(new \App\Middleware\CsrfViewMiddleware($container));

$app->add($container->csrf);

v::with('App\\Validation\\Rules\\');

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

// routes
use App\Middleware\GuestMiddleware;
use App\Middleware\AuthMiddleware;


$app->get('/', 'HomeController:index')->setName('home');

$app->group('', function () {
	$this->get('/auth/signup', 'AuthController:getSignUp')->setName('auth.signup');
	$this->post('/auth/signup', 'AuthController:postSignUp');
	$this->get('/auth/signin', 'AuthController:getSignIn')->setName('auth.signin');
	$this->post('/auth/signin', 'AuthController:postSignIn');
})->add(new GuestMiddleware($container));


$app->group('', function () {
	$this->get('/auth/signout', 'AuthController:getSignOut')->setName('auth.signout');
	$this->get('/auth/password/change', 'PasswordController:getChangePassword')->setName('auth.password.change');
	$this->post('/auth/password/change', 'PasswordController:postChangePassword');
})->add(new AuthMiddleware($container));
