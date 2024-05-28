<?php

declare(strict_types=1);

namespace App\Tests;

use App\Controllers\Auth\AuthController;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Slim\Interfaces\RouteParserInterface;
use Nyholm\Psr7Server\ServerRequestCreator;

final class AuthControllerTest extends AdminatorTestCase
{
    /** @var ServerRequestCreator */
    protected $creator;

    protected function setUp(): void
    {
        // prepare data for forms
        //
        $_POST = array();
        $_GET = array();
        $_SERVER = array();

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

    public function testLogin()
    {
        // $this->markTestSkipped('under construction');
        $self = $this;

        $container = self::initDIcontainer(false, true);

        $adminatorMock = self::initAdminatorMockClass($container);
        $this->assertIsObject($adminatorMock);

        $routerParser = \Mockery::mock(
            RouteParserInterface::class,
        );

        $authController = new AuthController($container, $routerParser);

        $server = [
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => '/',
        ];

        $serverRequest = $this->creator->fromArrays(
            $server,
            [],
            [],
            ['redirect' => ''],
        );

        // $response = $this->createMock(ResponseInterface::class);
        $responseFactory = $container->get(ResponseFactoryInterface::class);
        $response = $responseFactory->createResponse();

        // $response = $authController->signin($serverRequest, $response, ['flashEnabled' => false]);

    }
}
