<?php

use Slim\Views\Twig;
use Slim\Csrf\Guard;

use Psr\Container\ContainerInterface;
use App\View\CsrfExtension;
use App\Middleware\FlashOldFormDataMiddleware;

$container->set(
    'settings',
    function () {
        return require __DIR__ . '/../config/settings.php';
    }
);

$container->set(
    'logger',
    function ($c) {
        $settings = $c->get('settings');

        $logger = new Monolog\Logger($settings['logger']['name']);

        $formatter = new Monolog\Formatter\LineFormatter(
            $settings['logger']['output'],
            $settings['logger']['dateFormat']
        );

        $stream = new Monolog\Handler\StreamHandler(
            $settings['logger']['path'],
            $settings['logger']['level']
        );
        $stream->setFormatter($formatter);

        $fingersCrossed = new Monolog\Handler\FingersCrossedHandler(
            $stream,
            $settings['logger']['level']
        );
        $logger->pushProcessor(new Monolog\Processor\UidProcessor());

        $handler = new Monolog\ErrorHandler($logger);
        $handler->registerExceptionHandler();
        $handler->registerFatalHandler();

        $logger->pushHandler($fingersCrossed);
        return $logger;
    }
);

// $container->set('smarty', $smarty);

$container->set(
    'smarty',
    function ($c) {
        $smarty = new Smarty();
        $smarty->compile_check = true;
        //$smarty->debugging = true;

        return $smarty;
    }
);

$container->set(
    'connMysql',
    function ($c) {
        return init_mysql("Adminator3");
    }
);

$container->set(
    'pdoMysql',
    function () use ($capsule) {
        return $capsule->connection("default")->getPdo();
    }
);

$container->set(
    'connPgsql',
    function ($c) {
        return init_postgres("Adminator3");
    }
);

$container->set(
    'db',
    function ($container) use ($capsule) {
        return $capsule;
    }
);

$container->set(
    'validator',
    function ($container) {
        return new App\Validation\Validator();
    }
);

$container->set(
    'flash',
    function ($container) {
        return new \Slim\Flash\Messages();
    }
);

$container->set(
    'view',
    function ($container) {
        $settings = $container->get('settings');

        $view = Twig::create(
            $settings['twig']['path'],
            [
                'cache' => false,
            ]
        );

        $view->getEnvironment()->enableStrictVariables();

        $view->addExtension($container->get(CsrfExtension::class));

        $view->getEnvironment()->addGlobal('flash', $container->get('flash'));

        return $view;
    }
);

$container->set(
    'validator',
    function ($container) {
        return new App\Validation\Validator();
    }
);

$container->set(
    'FlashOldFormDataMiddleware',
    function ($container) {
        return new FlashOldFormDataMiddleware($container->get('flash'));
    }
);
