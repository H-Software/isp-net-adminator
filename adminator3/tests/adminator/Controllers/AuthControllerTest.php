<?php

declare(strict_types=1);

namespace App\Tests;

use App\Controllers\Auth\AuthController;
use Psr\Http\Message\ResponseFactoryInterface;
use Slim\Interfaces\RouteParserInterface;
use Nyholm\Psr7Server\ServerRequestCreator;
use Symfony\Component\HttpFoundation\Request;

final class AuthControllerTest extends AdminatorTestCase
{
    /** @var ServerRequestCreator */
    protected $creator;

    protected function setUp(): void
    {
    }

    protected function tearDown(): void
    {
    }

    public function test_ctl_login_page_default_view()
    {
        // $this->markTestSkipped('under construction');
        $self = $this;

        $request = Request::create(
            '/',
            'GET',
            []
        );
        $request->overrideGlobals();
        $serverRequest = self::$psrHttpFactory->createRequest($request);

        $container = self::initDIcontainer(false, true);

        $adminatorMock = self::initAdminatorMockClass($container);
        $this->assertIsObject($adminatorMock);

        $routerParser = \Mockery::mock(
            RouteParserInterface::class,
        );

        $authController = new AuthController($container, $routerParser);

        $responseFactory = $container->get(ResponseFactoryInterface::class);
        $response = $responseFactory->createResponse();

        $response = $authController->signin($serverRequest, $response, ['flashEnabled' => false]);

        $responseContent = $response->getBody()->__toString();

        // echo $responseContent;

        $this->assertEquals($response->getStatusCode(), 200);

        adminatorAssert::assertBaseCommon($responseContent);

        adminatorAssert::assertXpathQueryContentRegex($response, '//*[@id="adminator-signin-title"]', '/^Sign In$/');
        adminatorAssert::assertXpathQueryContentRegex($response, '//*[@id="adminator-signin-submit"]', '/^Sign In$/');

    }

    public function test_ctl_login_page_post_mocked()
    {
        // $this->markTestSkipped('under construction');
        $self = $this;

        $request = Request::create(
            '/login',
            'POST',
            []
        );
        $request->overrideGlobals();
        $serverRequest = self::$psrHttpFactory->createRequest($request);

        $container = self::initDIcontainer(true, true);

        $adminatorMock = self::initAdminatorMockClass($container);
        $this->assertIsObject($adminatorMock);

        $routeParser = $container->get(RouteParserInterface::class);

        $authController = new AuthController($container, $routeParser);

        $responseFactory = $container->get(ResponseFactoryInterface::class);
        $response = $responseFactory->createResponse();

        $response = $authController->signin($serverRequest, $response, ['flashEnabled' => false]);

        $responseContent = $response->getBody()->__toString();

        // echo $responseContent;

        $this->assertEquals($response->getStatusCode(), 302);

        list($hLocation) =  $response->getHeader('Location');
        $this->assertEquals($hLocation, '/home');
    }

    // TODO: add tests with unmocked sentinel

    // TODO: add tests for check (failed) csrf

    // TODO: add tests for password-changes

    // TODO: add tests got logout

}
