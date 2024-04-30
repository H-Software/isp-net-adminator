<?php

use Slim\Views\Twig;
use Slim\Csrf\Guard;

use Slim\Exception\NotFoundException;

use czhujer\Slim\Auth\ServiceProvider\SlimAuthProvider;
use czhujer\Slim\Auth\Middleware\Authorization;
use czhujer\Slim\Auth\Handlers\RedirectHandler;
// use czhujer\Slim\Auth\Adapter\LdapRdbmsAdapter;

use Laminas\Authentication\Storage\Session as SessionStorage;

use Laminas\Session\Config\SessionConfig;
use Laminas\Session\SessionManager;

$container = $app->getContainer();

$container->set('settings', function () {
    return require __DIR__ . '/settings.php';
});

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

// $container["authStorage"] = $storage;

$container->set('logger', function($c) {
    
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
});

// https://www.slimframework.com/docs/v3/handlers/error.html
// $container['errorHandler'] = function ($container) {
//     return new App\Handlers\Error($container['logger']);
// };

// $container['phpErrorHandler'] = function ($container) {
//     return $container['errorHandler'];
// };

$container->set('connMysql', $conn_mysql);

$container->set('connPgsql', $db_ok2);

$container->set('smarty', $smarty);

$container->set('db', function ($container) use ($capsule) {
    return $capsule;
});

$container->set('validator', function ($container) {
	return new App\Validation\Validator;
});

$container->set('flash', function($container) {
	return new \Slim\Flash\Messages;
});

$container->set('view', function ($container) {
	$view = Twig::create(__DIR__ . '/../resources/views/', [
		'cache' => false,
	]);

	// $view->addExtension(new \Slim\Views\TwigExtension(
	// 	$container->router,
	// 	$container->request->getUri()
	// ));

	$view->getEnvironment()->addGlobal('flash', $container->flash);

	return $view;
});

// $container['validator'] = function ($container) {
// 	return new App\Validation\Validator;
// };

// $acl = new Acl();

// $container['router'] = new \czhujer\Slim\Auth\Route\AuthorizableRouter(null, $acl);
// $container['acl']    = $acl;

// $adapterOptions = [];
// $adapter = new czhujer\Slim\Auth\Adapter\LdapRdbmsAdapter(
//     NULL,  //LDAP config or NULL if not using LDAP
//     $em, //an Doctrine's Entity Manager instance 
//     "App\Entity\UserRole",    //Role class
//     "role", //Role's class role attribute
//     "user", //Role's class user attribute (the @ManyToOne attrib)
//     "App\Entity\User", //User class
//     "username", //User name attribute
//     "passwordHash", //password (as a hash) attribute
//     czhujer\Slim\Auth\Adapter\LdapRdbmsAdapter::AUTHENTICATE_RDBMS, //auth method: LdapRdbmsAdapter::AUTHENTICATE_RDBMS | LdapRdbmsAdapter::AUTHENTICATE_LDAP 
//     10, //a hash factor
//     PASSWORD_DEFAULT, //hash algorithm
//     $adapterOptions //if needed
//     );

// $container["authAdapter"] = $adapter;

// $slimAuthProvider = new SlimAuthProvider();
// $slimAuthProvider->register($container);

// $app->add(
//         new Authorization( 
//                 $container["auth"], 
//                 $acl, 
//                 new RedirectHandler("/auth/notAuthenticated", "/auth/notAuthorized") 
//             )
//         );

$container->set('csrf', function() use($responseFactory) {
	return new Guard($responseFactory);
});

$app->add('csrf');

$container->set('AuthController', function($container) {
	return new \App\Controllers\Auth\AuthController($container);
});

// $container['PasswordController'] = function($container) {
// 	return new \App\Controllers\Auth\PasswordController($container);
// };

$container->set('HomeController', function($container) {
	return new \App\Controllers\HomeController($container);
});

// $container['aboutController'] = function ($c) {
//     return new \App\Controllers\aboutController($c);
// };

// $container['adminController'] = function ($c) {
//     return new \App\Controllers\adminController($c);
// };

// $container['archivZmenController'] = function ($c) {
//     return new \App\Controllers\archivZmenController($c);
// };

// $container['othersController'] = function ($c) {
//     return new \App\Controllers\othersController($c);
// };

// $container['objektyController'] = function ($c) {
//     return new \App\Controllers\objektyController($c);
// };

// $container['partnerController'] = function ($c) {
//     return new \App\Controllers\partnerController($c);
// };

// $container['platbyController'] = function ($c) {
//     return new \App\Controllers\platbyController($c);
// };

// $container['topologyController'] = function ($container) {
//     return new \App\Controllers\topologyController($container);
// };

// $container['vlastniciController'] = function ($c) {
//     return new \App\Controllers\vlastniciController($c);
// };
// $container['workController'] = function ($c) {
//     return new \App\Controllers\workController($c);
// };