<?php

declare(strict_types=1);

namespace App\Tests;

use App\Controllers\objektyController;
use Psr\Http\Message\ResponseFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

final class ObjektyControllerTest extends AdminatorTestCase
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
            '/objekty/cat',
            'GET',
            []
        );
        $request->overrideGlobals();
        $serverRequest = self::$psrHttpFactory->createRequest($request);

        $container = self::initDIcontainer(true, false);

        $adminatorMock = self::initAdminatorMockClass($container);
        $this->assertIsObject($adminatorMock);

        $response = self::callControllerFunction(
            $serverRequest,
            'App\Controllers\objektyController',
            'cat',
            $container,
            array(
                "adminatorMock" => $adminatorMock,
            )
        );

        $responseContent = $response->getBody()->__toString();

        // echo $responseContent;

        // TODO: add asserts for sub-categories

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
            '/objekty/cat',
            'GET',
            []
        );
        $request->overrideGlobals();
        $serverRequest = self::$psrHttpFactory->createRequest($request);

        $container = self::initDIcontainer(true, false);

        $adminatorMock = self::initAdminatorMockClass($container, false, 1);
        $this->assertIsObject($adminatorMock);

        $controller = new objektyController($container, $adminatorMock);

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

    public function test_ctl_objekty_list()
    {
        // $this->markTestSkipped('under construction');
        $self = $this;

        $request = Request::create(
            '/objekty',
            'GET',
            []
        );
        $request->overrideGlobals();
        $serverRequest = self::$psrHttpFactory->createRequest($request);

        $container = self::initDIcontainer(true, false);

        $adminatorMock = self::initAdminatorMockClass($container);
        $this->assertIsObject($adminatorMock);

        // TODO: objekty_list: fix $_GET/$_POST access

        $response = self::callControllerFunction(
            $serverRequest,
            'App\Controllers\objektyController',
            'objekty',
            $container,
            array(
                "adminatorMock" => $adminatorMock,
            ),
            200,
            ["usePDO" => true]
        );

        $responseContent = $response->getBody()->__toString();

        echo $responseContent;

        // // TODO: add asserts for sub-categories

        // // self::assertXpathQueryContentRegex($response, '//*[@id="obsah"]/div[5]/div[2]', '/^Prosím vyberte z podkategorie výše....$/');

        // // non-common negative asserts
        // $this->assertStringNotContainsStringIgnoringCase("chyba", $responseContent, "found word, which indicates error(s) or failure(s)");
        // $this->assertStringNotContainsStringIgnoringCase("nepodařil", $responseContent, " found word, which indicates error(s) or failure(s)");
    }

    // TODO: add test for stb

    // TODO: add tests for stbAction

    // TODO: add tests for objektyAction

}
