<?php

declare(strict_types=1);

namespace App\Tests;

use App\Controllers\Auth\AuthController;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Slim\Interfaces\RouteParserInterface;

final class AuthControllerTest extends AdminatorTestCase
{
    protected function setUp(): void
    {
        // prepare data for forms
        //
        $_POST = array();
        $_GET = array();
        $_SERVER = array();
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

        // $response = $authController->home($serverRequest, $response, []);

    }
}
