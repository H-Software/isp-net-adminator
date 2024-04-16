<?php

use Respect\Validation\Validator as v;

use marcelbonnet\Slim\Auth\ServiceProvider\SlimAuthProvider;
use Zend\Authentication\Storage\Session as SessionStorage;
use marcelbonnet\Slim\Auth\Middleware\Authorization;
use marcelbonnet\Slim\Auth\Handlers\RedirectHandler;
use marcelbonnet\Slim\Auth\Adapter\LdapRdbmsAdapter;

use \Slim\Http\Request as SlimHttpRequest;
use \Slim\Http\Response as SlimHttpResponse;

use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;

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

// $container['auth'] = function($container) {
//     return new \App\Auth\Auth;
// };

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

	// $view->getEnvironment()->addGlobal('auth', [
	// 	'check' => $container->auth->check(),
	// 	'user' => $container->auth->user()
	// ]);

	$view->getEnvironment()->addGlobal('flash', $container->flash);

	return $view;
};

// $container['validator'] = function ($container) {
// 	return new App\Validation\Validator;
// };

$acl = new Acl();

$container['router'] = new \marcelbonnet\Slim\Auth\Route\AuthorizableRouter(null, $acl);
// $container['acl']    = $acl;

// $config = ORMSetup::createAttributeMetadataConfiguration(
//     array(__DIR__."/src"),
//     true // isDevMode
// );

// Create a simple "default" Doctrine ORM configuration for Annotations
$isDevMode = true;
$proxyDir = null;
$cache = null;
$useSimpleAnnotationReader = false;
$ormConfig = ORMSetup::createAnnotationMetadataConfiguration(array(__DIR__."/src"), $isDevMode, $proxyDir, $cache);
// or if you prefer yaml or XML
//$config = Setup::createXMLMetadataConfiguration(array(__DIR__."/config/xml"), $isDevMode);
//$config = Setup::createYAMLMetadataConfiguration(array(__DIR__."/config/yaml"), $isDevMode);

$ormConn = array(
    'driver' => 'pdo_mysql',
    // 'path' => __DIR__ . '/db.sqlite',
);

// obtaining the entity manager
$entityManager = EntityManager::create($ormConn, $ormConfig);

$adapterOptions = [];
$adapter = new marcelbonnet\Slim\Auth\Adapter\LdapRdbmsAdapter(
    NULL,  //LDAP config or NULL if not using LDAP
    $entityManager, //an Doctrine's Entity Manager instance 
    "\Your\Project\Dao\Role",    //Role class
    "role", //Role's class role attribute
    "user", //Role's class user attribute (the @ManyToOne attrib)
    "\Your\Project\Dao\User", //User class
    "username", //User name attribute
    "passwordHash", //password (as a hash) attribute
    marcelbonnet\Slim\Auth\Adapter\LdapRdbmsAdapter::AUTHENTICATE_RDBMS, //auth method: LdapRdbmsAdapter::AUTHENTICATE_RDBMS | LdapRdbmsAdapter::AUTHENTICATE_LDAP 
    10, //a hash factor
    PASSWORD_DEFAULT, //hash algorithm
    $adapterOptions //if needed
    );

$container["authAdapter"] = $adapter;

$slimAuthProvider = new SlimAuthProvider();
$slimAuthProvider->register($container);

$app->add(new Authorization( $container["auth"], $acl, new RedirectHandler("auth/notAuthenticated", "auth/notAuthorized") ));

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
    return new \App\Controllers\archivZmenController($app->getContainer(),$conn_mysql, $smarty, $logger, $auth, $app);
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
    return new \App\Controllers\vlastniciController($app->getContainer(),$conn_mysql, $smarty, $logger, $auth, $app);
};
$container['workController'] = function ($c) {
    global $conn_mysql, $smarty, $logger, $auth, $app;
    return new \App\Controllers\workController($app->getContainer(),$conn_mysql, $smarty, $logger, $auth, $app);
};