<?php

use Slim\Views\Twig;
use Twig\TwigFilter;
use Nyholm\Psr7\Factory\Psr17Factory;
use App\Renderer\Renderer;
use App\View\CsrfExtension;
use App\Middleware\FlashOldFormDataMiddleware;
use App\Middleware\GuardMiddleware;
use OpenFeature\OpenFeatureAPI;
use OpenFeature\Providers\Flagd\FlagdProvider;
use Cartalyst\Sentinel\Native\SentinelBootstrapper;
use Cartalyst\Sentinel\Native\Facades\Sentinel;
use Illuminate\Cache\CacheManager;
use Illuminate\Container\Container;

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

$container->set('sentinel', function () use ($container) {
    $logger = $container->get('logger');
    $logger->debug('DI\sentinel: called');

    $boostrap = new SentinelBootstrapper((require __DIR__ . '/../config/sentinel.php'));
    $sentinel = new Sentinel($boostrap);

    return $sentinel->getSentinel();
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

        $logger->debug("DI\openfeature: adminator3FlagdConfigVersion: " . var_export($configVersion, true));

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

        $logger->debug("DI\smarty: compile_check: " . var_export($settings['smarty']['compile_check'], true));
        $logger->debug("DI\smarty: smarty caching: " . var_export($settings['smarty']['caching'], true));

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
    'redis',
    function () use ($container): Redis {
        $logger = $container->get('logger');
        $logger->debug('DI\redis: called');

        /**
        * Setup a new app instance container
        *
        * @var Illuminate\Container\Container
        */
        $app = new Container();
        $app->singleton('app', 'Illuminate\Container\Container');

        /**
        * Set $app as FacadeApplication handler
        */
        Facade::setFacadeApplication($app);

        $redis = new Redis($app, 'phpredis', [
            'cluster' => false,
            'default' => [
                'host'     => '127.0.0.1',
                'port'     => 6379,
                'database' => 0,
            ],
        ]);

        if (!is_object($redis)) {
            $logger->error('DI\redis: return value (redis) is not object');
        }

        $logger->info("DI\\redis: Attempting to connect to Redis");

        try {
            Redis::ping();
        } catch (\Exception $ex) {
            $m = $ex->getMessage();
            $logger->error("DI\\redis: Redis error: $m");
            return $redis;
        }

        if (Redis::isConnected()) {
            $logger->info("DI\\redis: Redis server PING -> " . Redis::ping());
        } else {
            $logger->error("DI\\redis: Redis server not connected, can't send PING!");
        }

        return $redis;
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
        $logger->debug('DI\flash: called');
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
                'cache' => $settings['twig']['cache'],
                'debug' => $settings['twig']['debug'],

            ]
        );

        $view->getEnvironment()->enableStrictVariables();

        $filter = new TwigFilter('ident', function ($string, $number) {
            $spaces = str_repeat(' ', $number);
            return rtrim(preg_replace('#^(.+)$#m', sprintf('%1$s$1', $spaces), $string));
        }, array('is_safe' => array('all')));

        $view->getEnvironment()->addFilter($filter);

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
    'cache',
    function ($container) use ($capsule) {
        $logger = $container->get('logger');
        $settings = $container->get('settings');

        $logger->debug('DI\cache: called');

        // https://github.com/mattstauffer/Torch/blob/master/components/cache/index.php
        $c = new Container();
        $c['config'] = $settings['cache'];
        $c['db'] = $capsule;

        // $logger->debug('DI\cache: using config: ' . var_export($c['config'], true));

        $cacheManager = new CacheManager($c);

        $cache = $cacheManager->store();

        // $logger->debug('DI\cache: using driver: ' . var_export($cacheManager->getDefaultDriver(), true));

        return $cache;
    }
);

$container->set(
    'FlashOldFormDataMiddleware',
    function ($container) {
        return new FlashOldFormDataMiddleware($container);
    }
);

$container->set(
    'GuardMiddleware',
    function ($container) {
        $rf = $container->get(Psr17Factory::class);
        $rendered = $container->get(Renderer::class);
        return new GuardMiddleware($container, $rf, $rendered);
    }
);
