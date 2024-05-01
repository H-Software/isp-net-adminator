<?php

use Slim\Views\Twig;
use Slim\Csrf\Guard;

use Psr\Container\ContainerInterface;
use App\View\CsrfExtension;
use App\Middleware\FlashOldFormDataMiddleware;

$container->set('settings', function () {
    return require __DIR__ . '/../config/settings.php';
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

    $view->getEnvironment()->enableStrictVariables();

    $view->addExtension($container->get(CsrfExtension::class));

	$view->getEnvironment()->addGlobal('flash', $container->get('flash'));

	return $view;
});

$container->set('validator', function ($container) {
	return new App\Validation\Validator;
});

$container->set('FlashOldFormDataMiddleware', function ($container) {
    return new FlashOldFormDataMiddleware($container->get('flash'));
});


