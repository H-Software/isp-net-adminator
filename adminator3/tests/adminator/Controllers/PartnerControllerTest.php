<?php

declare(strict_types=1);

namespace App\Tests;

use App\Controllers\partnerController;
use Psr\Http\Message\ResponseFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

final class PartnerControllerTest extends AdminatorTestCase
{
    protected function setUp(): void
    {
    }

    protected function tearDown(): void
    {
    }

    public function test_ctl_cat()
    {
        // $this->markTestSkipped('under construction');
        $self = $this;

        $request = Request::create(
            '/partner/cat',
            'GET',
            []
        );
        $request->overrideGlobals();
        $serverRequest = self::$psrHttpFactory->createRequest($request);

        $container = self::initDIcontainer(true, false, true);

        $adminatorMock = self::initAdminatorMockClass($container);
        $this->assertIsObject($adminatorMock);

        $controller = new partnerController($container, $adminatorMock);

        $responseFactory = $container->get(ResponseFactoryInterface::class);
        $response = $responseFactory->createResponse();

        $response = $controller->cat($serverRequest, $response, []);

        $responseContent = $response->getBody()->__toString();

        // echo $responseContent;

        $this->assertEquals($response->getStatusCode(), 200);

        adminatorAssert::assertBase($responseContent);

        adminatorAssert::assertPartnerSubCat($response);

        self::assertXpathQueryContentRegex($response, '//*[@id="obsah"]/div[5]/div[2]', '/^Prosím vyberte z podkategorie výše....$/');

        // non-common negative asserts
        $this->assertStringNotContainsStringIgnoringCase("chyba", $responseContent, "found word, which indicates error(s) or failure(s)");
        $this->assertStringNotContainsStringIgnoringCase("nepodařil", $responseContent, " found word, which indicates error(s) or failure(s)");
    }

    public function test_ctl_cat_with_low_user_level()
    {
        // $this->markTestSkipped('under construction');
        $self = $this;

        $request = Request::create(
            '/partner/cat',
            'GET',
            []
        );
        $request->overrideGlobals();
        $serverRequest = self::$psrHttpFactory->createRequest($request);

        $container = self::initDIcontainer(true, false, true);

        $adminatorMock = self::initAdminatorMockClass($container, false, 1);
        $this->assertIsObject($adminatorMock);

        $controller = new partnerController($container, $adminatorMock);

        $responseFactory = $container->get(ResponseFactoryInterface::class);
        $response = $responseFactory->createResponse();

        $response = $controller->cat($serverRequest, $response, []);

        $responseContent = $response->getBody()->__toString();
        $this->assertNotEmpty($responseContent);

        // echo $responseContent;

        adminatorAssert::assertBase($responseContent);

        // page specific asserts
        AdminatorAssert::assertNoLevelPage($response);

        // non-common negative asserts
        $this->assertStringNotContainsStringIgnoringCase("nepodařil", $responseContent, " found word, which indicates error(s) or failure(s)");
    }

    public function test_ctl_order_cat()
    {
        // $this->markTestSkipped('under construction');
        $self = $this;

        $request = Request::create(
            '/partner/order',
            'GET',
            []
        );
        $request->overrideGlobals();
        $serverRequest = self::$psrHttpFactory->createRequest($request);

        $container = self::initDIcontainer(true, false, true);

        $adminatorMock = self::initAdminatorMockClass($container);
        $this->assertIsObject($adminatorMock);

        $controller = new partnerController($container, $adminatorMock);

        $responseFactory = $container->get(ResponseFactoryInterface::class);
        $response = $responseFactory->createResponse();

        $response = $controller->orderCat($serverRequest, $response, []);

        $responseContent = $response->getBody()->__toString();

        // echo $responseContent;

        $this->assertEquals($response->getStatusCode(), 200);

        adminatorAssert::assertBase($responseContent);

        adminatorAssert::assertPartnerOrderSubCat($response);

        self::assertXpathQueryContentRegex($response, '//*[@id="obsah"]/div[5]/div[2]', '/^Prosím vyberte z podkategorie výše....$/');

        // non-common negative asserts
        $this->assertStringNotContainsStringIgnoringCase("chyba", $responseContent, "found word, which indicates error(s) or failure(s)");
        $this->assertStringNotContainsStringIgnoringCase("nepodařil", $responseContent, " found word, which indicates error(s) or failure(s)");
    }

    // TODO: add tests for order CRUDs

}
