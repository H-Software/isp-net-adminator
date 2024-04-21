<?php

// use Respect\Validation\Validator as v;

use czhujer\Slim\Auth\ServiceProvider\SlimAuthProvider;
use czhujer\Slim\Auth\Middleware\Authorization;
use czhujer\Slim\Auth\Handlers\RedirectHandler;
// use czhujer\Slim\Auth\Adapter\LdapRdbmsAdapter;

use Laminas\Authentication\Storage\Session as SessionStorage;

use Laminas\Session\Config\SessionConfig;
use Laminas\Session\SessionManager;

$container = $app->getContainer();

// init sessions
$sessionConfig = new SessionConfig();
$sessionConfig->setOptions(array(
    // 'remember_me_seconds' => 5,
    'name' => 'adminator-auth',
    // 'cookie_lifetime' => 5
));
$sessionManager = new SessionManager($sessionConfig);
$sessionManager->rememberMe();

$storage = new SessionStorage();
// $sessionManager->setStorage($storage);

$container["authStorage"] = $storage;

$container['logger'] = function($c) {
    
    $settings = $c->get('settings')['logger'];

    $logger = new Monolog\Logger($settings['name']);
    $filename = __DIR__ . '/../../a3-logs/app.log';

    // the default date format is "Y-m-d\TH:i:sP"
    // $dateFormat = "Y n j, g:i a";
    $dateFormat = 'Y-m-d\TH:i:s';

    // the default output format is "[%datetime%] %channel%.%level_name%: %message% %context% %extra%\n"
    // we now change the default output format according to our needs.
    $output = "%datetime% > %level_name% > %message% %context% %extra%\n";

    // finally, create a formatter
    $formatter = new Monolog\Formatter\LineFormatter($output, $dateFormat);

    $stream = new Monolog\Handler\StreamHandler($filename, Monolog\Logger::DEBUG);
    $stream->setFormatter($formatter);
    $fingersCrossed = new Monolog\Handler\FingersCrossedHandler(
        $stream, Monolog\Logger::DEBUG
    );
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    
    $handler = new Monolog\ErrorHandler($logger);
    $handler->registerExceptionHandler();
    $handler->registerFatalHandler();

    $logger->pushHandler($fingersCrossed);
    return $logger;
};

// https://www.slimframework.com/docs/v3/handlers/error.html
$container['errorHandler'] = function ($container) {
    return new App\Handlers\Error($container['logger']);
};

$container['phpErrorHandler'] = function ($container) {
    return $container['errorHandler'];
};

$container['connMysql'] = $conn_mysql;

$container['connPgsql'] = $db_ok2;

$container['smarty'] = $smarty;

$container['db'] = function ($container) use ($capsule) {
    return $capsule;
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

	$view->getEnvironment()->addGlobal('flash', $container->flash);

	return $view;
};

// $container['validator'] = function ($container) {
// 	return new App\Validation\Validator;
// };

$acl = new Acl();

$container['router'] = new \czhujer\Slim\Auth\Route\AuthorizableRouter(null, $acl);
$container['acl']    = $acl;

$adapterOptions = [];
$adapter = new czhujer\Slim\Auth\Adapter\LdapRdbmsAdapter(
    NULL,  //LDAP config or NULL if not using LDAP
    $em, //an Doctrine's Entity Manager instance 
    "App\Entity\UserRole",    //Role class
    "role", //Role's class role attribute
    "user", //Role's class user attribute (the @ManyToOne attrib)
    "App\Entity\User", //User class
    "username", //User name attribute
    "passwordHash", //password (as a hash) attribute
    czhujer\Slim\Auth\Adapter\LdapRdbmsAdapter::AUTHENTICATE_RDBMS, //auth method: LdapRdbmsAdapter::AUTHENTICATE_RDBMS | LdapRdbmsAdapter::AUTHENTICATE_LDAP 
    10, //a hash factor
    PASSWORD_DEFAULT, //hash algorithm
    $adapterOptions //if needed
    );

$container["authAdapter"] = $adapter;

$slimAuthProvider = new SlimAuthProvider();
$slimAuthProvider->register($container);

$app->add(
        new Authorization( 
                $container["auth"], 
                $acl, 
                new RedirectHandler("/auth/notAuthenticated", "/auth/notAuthorized") 
            )
        );

$container['csrf'] = function($container) {
	return new \Slim\Csrf\Guard;
};

$app->add(new \App\Middleware\CsrfViewMiddleware($container));

$app->add($container->csrf);

// v::with('App\\Validation\\Rules\\');

$container['AuthController'] = function($container) {
	return new \App\Controllers\Auth\AuthController($container);
};

$container['HomeController'] = function($container) {
    global $conn_mysql, $smarty;
	return new \App\Controllers\HomeController($container,$conn_mysql, $smarty);
};

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
    return new \App\Controllers\archivZmenController($app->getContainer(), $smarty);
};

$container['othersController'] = function ($c) {
    global $conn_mysql, $smarty, $logger, $auth, $app;
    return new \App\Controllers\othersController($app->getContainer(),$conn_mysql, $smarty, $logger, $auth, $app);
};

$container['objektyController'] = function ($c) {
    global $conn_mysql, $smarty, $logger, $auth, $app;
    return new \App\Controllers\objektyController($app->getContainer(),$conn_mysql, $smarty, $logger, $auth, $app);
};

$container['platbyController'] = function ($c) {
    global $conn_mysql, $smarty, $logger, $auth, $app;
    return new \App\Controllers\platbyController($app->getContainer(),$conn_mysql, $smarty, $logger, $auth, $app);
};

$container['topologyController'] = function ($container) {
    global $conn_mysql, $smarty;
    return new \App\Controllers\topologyController($container, $conn_mysql, $smarty);
};

$container['vlastniciController'] = function ($c) {
    global $conn_mysql, $smarty, $logger, $auth, $app;
    return new \App\Controllers\vlastniciController($app->getContainer());
};
$container['workController'] = function ($c) {
    global $conn_mysql, $smarty, $logger, $auth, $app;
    return new \App\Controllers\workController($app->getContainer(),$conn_mysql, $smarty, $logger, $auth, $app);
};