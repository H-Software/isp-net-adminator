<?php

declare(strict_types=1);

namespace App\Tests;

use App\Controllers\partnerServisController;
use Psr\Http\Message\ResponseFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

final class PartnerServisControllerTest extends AdminatorTestCase
{
    protected function setUp(): void
    {
    }

    protected function tearDown(): void
    {
    }

    public function test_ctl_list()
    {
        // $this->markTestSkipped('under construction');
        $self = $this;

        $request = Request::create(
            '/partner/servis/list',
            'GET',
            []
        );
        $request->overrideGlobals();
        $serverRequest = self::$psrHttpFactory->createRequest($request);

        $container = self::initDIcontainer(true, false);

        $adminatorMock = self::initAdminatorMockClass($container);
        $this->assertIsObject($adminatorMock);

        $controller = new partnerServisController($container, $adminatorMock);

        $responseFactory = $container->get(ResponseFactoryInterface::class);
        $response = $responseFactory->createResponse();

        // $response = $controller->list($serverRequest, $response, []);

        // $responseContent = $response->getBody()->__toString();

        // // echo $responseContent;

        // $this->assertEquals($response->getStatusCode(), 200);

        // adminatorAssert::assertBase($responseContent);

        // adminatorAssert::assertPartnerSubCat($response);

        // // TODO: add asserts for items

        // // non-common negative asserts
        // $this->assertStringNotContainsStringIgnoringCase("chyba", $responseContent, "found word, which indicates error(s) or failure(s)");
        // $this->assertStringNotContainsStringIgnoringCase("nepodařil", $responseContent, " found word, which indicates error(s) or failure(s)");
    }

    public function test_ctl_list_with_low_user_level()
    {
        // $this->markTestSkipped('under construction');
        $self = $this;

        $request = Request::create(
            '/partner/servis/list',
            'GET',
            []
        );
        $request->overrideGlobals();
        $serverRequest = self::$psrHttpFactory->createRequest($request);

        $container = self::initDIcontainer(true, false);

        $adminatorMock = self::initAdminatorMockClass($container, false, 1);
        $this->assertIsObject($adminatorMock);

        $controller = new partnerServisController($container, $adminatorMock);

        $responseFactory = $container->get(ResponseFactoryInterface::class);
        $response = $responseFactory->createResponse();

        $response = $controller->list($serverRequest, $response, []);

        $responseContent = $response->getBody()->__toString();
        $this->assertNotEmpty($responseContent);

        // echo $responseContent;

        adminatorAssert::assertBase($responseContent);

        // page specific asserts
        AdminatorAssert::assertNoLevelPage($response);

        // non-common negative asserts
        $this->assertStringNotContainsStringIgnoringCase("nepodařil", $responseContent, " found word, which indicates error(s) or failure(s)");

    }

    // TODO: add tests for add / servisAccept / ChangeDesc

}
