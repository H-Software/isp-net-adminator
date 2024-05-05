<?php

declare(strict_types=1);

namespace App\Tests;

use Psr\Http\Message\ResponseFactoryInterface;
use Slim\App;
use Slim\Factory\AppFactory;
use Psr\Container\ContainerInterface;
use PHPUnit\Framework\TestCase;
use Slim\Interfaces\CallableResolverInterface;
use Slim\Interfaces\RouteParserInterface;

// https://github.com/adriansuter/Slim4-Skeleton/blob/master/tests/ContainerFactoryTest.php

class AppFactoryTest extends TestCase
{
    public function testCreate()
    {
        // AppFactory::setContainer($container);
        $app = AppFactory::create();

        $callableResolver = $app->getCallableResolver();
        $responseFactory = $app->getResponseFactory();
        $routeParser = $app->getRouteCollector()->getRouteParser();

        $this->assertInstanceOf(App::class, $app);

        $this->assertInstanceOf(CallableResolverInterface::class, $callableResolver);
        $this->assertInstanceOf(ResponseFactoryInterface::class, $responseFactory);
        $this->assertInstanceOf(RouteParserInterface::class, $routeParser);
    }
}
