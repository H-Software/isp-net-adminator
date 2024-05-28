<?php

declare(strict_types=1);

namespace App\Tests;

use App\Controllers\topologyController;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Slim\Interfaces\RouteParserInterface;
use Nyholm\Psr7Server\ServerRequestCreator;

final class TopologyControllerTest extends AdminatorTestCase
{
    /** @var ServerRequestCreator */
    protected $creator;

    protected function setUp(): void
    {
        // prepare data for forms
        //
        // $_POST = array();
        // $_GET = array();
        // $_SERVER = array();

        $psr17Factory = new \Nyholm\Psr7\Factory\Psr17Factory();

        $this->creator = new ServerRequestCreator(
            $psr17Factory,
            $psr17Factory,
            $psr17Factory,
            $psr17Factory
        );

    }

    protected function tearDown(): void
    {
    }

    public function testNodeList()
    {
        // $this->markTestSkipped('under construction');
        $self = $this;

        $container = self::initDIcontainer(true, false);

        $adminatorMock = self::initAdminatorMockClass($container);
        $this->assertIsObject($adminatorMock);

        // $routerParser = \Mockery::mock(
        //     RouteParserInterface::class,
        // );

        $topologyController = new topologyController($container);

        $serverRequest = $this->createMock(ServerRequestInterface::class);
        // $response = $this->createMock(ResponseInterface::class);
        $responseFactory = $container->get(ResponseFactoryInterface::class);
        $response = $responseFactory->createResponse();

        $response = $topologyController->nodeList($serverRequest, $response, []);

    }
}
