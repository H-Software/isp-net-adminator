<?php

declare(strict_types=1);

namespace App\Tests;

use App\Controllers\othersController;
use Psr\Http\Message\ResponseFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

final class OthersControllerTest extends AdminatorTestCase
{
    protected function setUp(): void
    {
    }

    protected function tearDown(): void
    {
    }

    public function test_ctl_others()
    {
        // $this->markTestSkipped('under construction');
        $self = $this;

        $request = Request::create(
            '/others',
            'GET',
            []
        );
        $request->overrideGlobals();
        $serverRequest = self::$psrHttpFactory->createRequest($request);

        $container = self::initDIcontainer(true, false);

        $adminatorMock = self::initAdminatorMockClass($container);
        $this->assertIsObject($adminatorMock);

        $controller = new othersController($container, $adminatorMock);

        $responseFactory = $container->get(ResponseFactoryInterface::class);
        $response = $responseFactory->createResponse();

        $response = $controller->others($serverRequest, $response, []);

        $responseContent = $response->getBody()->__toString();

        // echo $responseContent;

        $this->assertEquals($response->getStatusCode(), 200);

        adminatorAssert::assertBase($responseContent);

        // TODO: add asserts

        // non-common negative asserts
        $this->assertStringNotContainsStringIgnoringCase("chyba", $responseContent, "found word, which indicates error(s) or failure(s)");
        $this->assertStringNotContainsStringIgnoringCase("nepodařil", $responseContent, " found word, which indicates error(s) or failure(s)");
    }

    public function test_ctl_others_with_low_user_level()
    {
        // $this->markTestSkipped('under construction');
        $self = $this;

        $request = Request::create(
            '/others',
            'GET',
            []
        );
        $request->overrideGlobals();
        $serverRequest = self::$psrHttpFactory->createRequest($request);

        $container = self::initDIcontainer(true, false);

        $adminatorMock = self::initAdminatorMockClass($container, false, 1);
        $this->assertIsObject($adminatorMock);

        $controller = new othersController($container, $adminatorMock);

        $responseFactory = $container->get(ResponseFactoryInterface::class);
        $response = $responseFactory->createResponse();

        $response = $controller->others($serverRequest, $response, []);

        $responseContent = $response->getBody()->__toString();
        $this->assertNotEmpty($responseContent);

        // echo $responseContent;

        adminatorAssert::assertBase($responseContent);

        // page specific asserts
        AdminatorAssert::assertNoLevelPage($response);

        // non-common negative asserts
        $this->assertStringNotContainsStringIgnoringCase("nepodařil", $responseContent, " found word, which indicates error(s) or failure(s)");

    }

    // TODO: add test for Work

    // TODO: add tests for list

    // TODO: add tests for Ucetni

}
