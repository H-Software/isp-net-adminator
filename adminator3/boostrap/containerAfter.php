<?php

use Slim\Views\Twig;
use Slim\Csrf\Guard;

use Psr\Container\ContainerInterface;
use App\View\CsrfExtension;
use App\Middleware\FlashOldFormDataMiddleware;

use OpenFeature\OpenFeatureAPI;
use OpenFeature\Providers\Flagd\FlagdProvider;

use Cartalyst\Sentinel\Native\SentinelBootstrapper;
use Cartalyst\Sentinel\Native\Facades\Sentinel;

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

        $logger->info("app running in mode: " . var_export($_ENV['MODE'], true));

        return $logger;
    }
);

/* add sentinel user auth lib to container */
$container->set('sentinel', function () use ($container) {
    $logger = $container->get('logger');

    $logger->debug("bootstrap\containerAfer: sentinel: called");

    // $sentinel = Sentinel::instance(new SentinelBootstrapper((require __DIR__ . '/../config/sentinel.php')));
    $boostrap = new SentinelBootstrapper((require __DIR__ . '/../config/sentinel.php'));

    $sentinel = new Sentinel($boostrap);

    // $sentinel->getSentinel();
    
    return $sentinel;

    // return $instance->getSentinel();
});

$container->set(
    'openfeature',
    function ($c) {
        $logger = $c->get('logger');

        $httpClient = new \GuzzleHttp\Client();
        $httpFactory = new \GuzzleHttp\Psr7\HttpFactory();


        $api = OpenFeatureAPI::getInstance();

        $logger->debug("bootstrap\containerAfer: openfeature: client instance: " . var_export($httpClient instanceof \Psr\Http\Client\ClientInterface, true));
        $logger->debug("bootstrap\containerAfer: openfeature: requestFactory instance: " . var_export($httpFactory instanceof \Psr\Http\Message\RequestFactoryInterface, true));
        $logger->debug("bootstrap\containerAfer: openfeature: streamFactory instance: " . var_export($httpFactory instanceof \Psr\Http\Message\StreamFactoryInterface, true));

        $api->setProvider(new FlagdProvider([
            'host' => 'flagd',
            'port' => 8013,
            'secure' => false,
            'protocol' => 'http',
            'httpConfig' => [
                'client' => $httpClient, // \Psr\Http\Client\ClientInterface
                'requestFactory' => $httpFactory, // Psr\Http\Message\RequestFactoryInterface
                'streamFactory' => $httpFactory, // Psr\Http\Message\StreamFactoryInterface
            ],
        ]));

        $client = $api->getClient('flagd-local', '1.0');

        $configVersion = $client->getStringValue("adminator3FlagdConfigVersion", "null");

        $logger->debug("bootstrap\containerAfer: openfeature: adminator3FlagdConfigVersion: " . var_export($configVersion, true));

        return $client;
    }
);

$container->set(
    'smarty',
    function ($c) {
        $settings = $c->get('settings');
        $logger = $c->get('logger');

        $smarty = new Smarty();
        $smarty->compile_check = $settings['smarty']['compile_check'];
        $smarty->caching = $settings['smarty']['caching'];
        //$smarty->debugging = true;

        $logger->debug("bootstrap\containerAfer: smarty compile_check: " . var_export($settings['smarty']['compile_check'], true));
        $logger->debug("bootstrap\containerAfer: smarty caching: " . var_export($settings['smarty']['caching'], true));

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
        $logger = $container->get('logger');
        $logger->debug("bootstrap\containerAfer: flash: called");
        return new \Slim\Flash\Messages();
    }
);

$container->set(
    'view',
    function ($container) {
        $settings = $container->get('settings');
        $logger = $container->get('logger');

        $logger->debug("bootstrap\containerAfer: view: called");
        
        $view = Twig::create(
            $settings['twig']['path'],
            [
                'cache' => false,
            ]
        );

        $view->getEnvironment()->enableStrictVariables();

        $view->addExtension($container->get(CsrfExtension::class));

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
        return new FlashOldFormDataMiddleware($container);
    }
);
