<?php

use Slim\Views\Twig;
use Slim\Csrf\Guard;

use Psr\Container\ContainerInterface;
use Odan\Session\Middleware\SessionStartMiddleware;
use App\View\CsrfExtension;
use App\View\TwigMessagesExtension;
use App\View\TwigPhpExtension;
use App\Middleware\FlashOldFormDataMiddleware;
use Slim\Views\TwigMiddleware;


// $container = $app->getContainer();

$container->set('settings', function () {
    return require __DIR__ . '/settings.php';
});

$container->set('logger', function($c) { 
    $settings = $c->get('settings');

    $logger = new Monolog\Logger($settings['logger']['name']);
    $filename = $settings['logger']['path'];

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
    $settings = $container->get('settings');

	$view = Twig::create($settings['twig']['path'], [
		'cache' => false,
	]);

    // $view->getEnvironment()->enableStrictVariables();

    // $view->addExtension($container->get(TwigPhpExtension::class));
    // $view->addExtension($container->get(CsrfExtension::class));
    // $view->addExtension($container->get(TwigMessagesExtension::class));

	// $view->getEnvironment()->addGlobal('flash', $container->get('flash'));

	return $view;
});

$container->set('validator', function ($container) {
	return new App\Validation\Validator;
});

$container->set('csrf', function() use($responseFactory) {
	return new Guard($responseFactory);
});

$app->add('csrf');

// $app->add(
//     function ($request, $next) {
//         // Start PHP session
//         if (session_status() !== PHP_SESSION_ACTIVE) {
//             session_start();
//         }

//         // Change flash message storage
//         $this->get('flash')->__construct($_SESSION);

//         return $next->handle($request);
//     }
// );

$app->addMiddleware($container->get(SessionStartMiddleware::class));

// $app->addMiddleware($container->get(TwigMiddleware::class));
$app->addMiddleware(TwigMiddleware::createFromContainer($app));

// $app->addMiddleware($container->get(FlashOldFormDataMiddleware::class));

$container->set('AuthController', function($container) {
	return new \App\Controllers\Auth\AuthController($container);
});

// $container['PasswordController'] = function($container) {
// 	return new \App\Controllers\Auth\PasswordController($container);
// };

$container->set('HomeController', function($container) {
	return new \App\Controllers\HomeController($container);
});

$container->set('aboutController', function ($c) {
    return new \App\Controllers\aboutController($c);
});

$container->set('adminController', function ($c) {
    return new \App\Controllers\adminController($c);
});

$container->set('archivZmenController', function ($c) {
    return new \App\Controllers\archivZmenController($c);
});

$container->set('othersController', function ($c) {
    return new \App\Controllers\othersController($c);
});

$container->set('objektyController', function ($c) {
    return new \App\Controllers\objektyController($c);
});

$container->set('partnerController', function ($c) {
    return new \App\Controllers\partnerController($c);
});

$container->set('platbyController', function ($c) {
    return new \App\Controllers\platbyController($c);
});

$container->set('topologyController', function ($container) {
    return new \App\Controllers\topologyController($container);
});

$container->set('vlastniciController', function ($c) {
    return new \App\Controllers\vlastniciController($c);
});

$container->set('workController', function ($c) {
    return new \App\Controllers\workController($c);
});
