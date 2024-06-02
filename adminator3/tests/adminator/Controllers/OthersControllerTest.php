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

        adminatorAssert::assertOtherCat($response);
        self::assertXpathQueryContentRegex($response, '//*[@id="obsah"]/div[5]/div[2]', '/^Prosím vyberte z podkategorie výše....$/');

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

    public function test_ctl_board()
    {
        // $this->markTestSkipped('under construction');
        $self = $this;

        $request = Request::create(
            '/others/board',
            'GET',
            [],
            [],
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

        $response = $controller->board($serverRequest, $response, []);

        $responseContent = $response->getBody()->__toString();

        // echo $responseContent;

        $this->assertEquals($response->getStatusCode(), 200);

        adminatorAssert::assertBase($responseContent);

        adminatorAssert::assertOtherCat($response);

        adminatorAssert::assertBoardCommon($response, $responseContent);
        adminatorAssert::assertBoardMessages($response, $responseContent);

        // test board mode
        self::assertXpathQueryContentRegex($response, '//*[@id="obsah"]/div[5]/div[4]/div[2]/div[1]', '/^->> Aktuální zprávy$/');

        // non-common negative asserts
        $this->assertStringNotContainsStringIgnoringCase("chyba", $responseContent, "found word, which indicates error(s) or failure(s)");
        $this->assertStringNotContainsStringIgnoringCase("nepodařil", $responseContent, " found word, which indicates error(s) or failure(s)");
    }

    public function test_ctl_board_new_messages()
    {
        // $this->markTestSkipped('under construction');
        $self = $this;

        $request = Request::create(
            '/others/board',
            'GET',
            [
                "action" => "view",
                "what" => "new"
            ],
            [],
            []
        );
        $request->overrideGlobals();
        $serverRequest = self::$psrHttpFactory->createRequest($request);

        $container = self::initDIcontainer(true, false);

        $adminatorMock = self::initAdminatorMockClass($container);
        $this->assertIsObject($adminatorMock);

        $response = self::callControllerFunction($serverRequest, 'App\Controllers\othersController', 'board', $container, $adminatorMock);
        $responseContent = $response->getBody()->__toString();

        adminatorAssert::assertOtherCat($response);

        adminatorAssert::assertBoardCommon($response, $responseContent);
        adminatorAssert::assertBoardMessages($response, $responseContent);

        // test board mode
        self::assertXpathQueryContentRegex($response, '//*[@id="obsah"]/div[5]/div[4]/div[2]/div[1]', '/^->> Aktuální zprávy$/');

        // non-common negative asserts
        $this->assertStringNotContainsStringIgnoringCase("chyba", $responseContent, "found word, which indicates error(s) or failure(s)");
        $this->assertStringNotContainsStringIgnoringCase("nepodařil", $responseContent, " found word, which indicates error(s) or failure(s)");
    }

    public function test_ctl_board_old_messages()
    {
        // $this->markTestSkipped('under construction');
        $self = $this;

        $request = Request::create(
            '/others/board',
            'GET',
            [
                "action" => "view",
                "what" => "old"
            ],
            [],
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

        $response = $controller->board($serverRequest, $response, []);

        $responseContent = $response->getBody()->__toString();

        // echo $responseContent;

        $this->assertEquals($response->getStatusCode(), 200);

        adminatorAssert::assertBase($responseContent);

        adminatorAssert::assertOtherCat($response);

        adminatorAssert::assertBoardCommon($response, $responseContent);
        // TODO: fix missing data in DB - old messages
        // adminatorAssert::assertBoardMessages($response, $responseContent);

        // test board mode
        self::assertXpathQueryContentRegex($response, '//*[@id="obsah"]/div[5]/div[4]/div[2]/div[1]', '/^->> Staré zprávy$/');

        // non-common negative asserts
        $this->assertStringNotContainsStringIgnoringCase("chyba", $responseContent, "found word, which indicates error(s) or failure(s)");
        $this->assertStringNotContainsStringIgnoringCase("nepodařil", $responseContent, " found word, which indicates error(s) or failure(s)");
    }

    public function test_ctl_board_add_message_form()
    {
        // $this->markTestSkipped('under construction');
        $self = $this;

        $request = Request::create(
            '/others/board',
            'GET',
            [
                "action" => "post",
            ],
            [],
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

        $response = $controller->board($serverRequest, $response, []);

        $responseContent = $response->getBody()->__toString();

        // echo $responseContent;

        $this->assertEquals($response->getStatusCode(), 200);

        adminatorAssert::assertBase($responseContent);

        adminatorAssert::assertOtherCat($response);

        adminatorAssert::assertBoardCommon($response, $responseContent);

        // TODO: board_add_message_form: add asserts for form

        // test board mode
        self::assertXpathQueryContentRegex($response, '//*[@id="obsah"]/div[5]/div[4]/div[2]/div[1]', '/^\s*->> Přidat zprávu\s*$/');

        // non-common negative asserts
        $this->assertStringNotContainsStringIgnoringCase("chyba", $responseContent, "found word, which indicates error(s) or failure(s)");
        $this->assertStringNotContainsStringIgnoringCase("nepodařil", $responseContent, " found word, which indicates error(s) or failure(s)");
    }

    public function test_ctl_board_add_message_sent()
    {
        // $this->markTestSkipped('under construction');
        $self = $this;

        $request = Request::create(
            '/others/board?action=post',
            'POST',
            [
                "sent"  => "true",
                "author" => "test@test",
                "email" => "",
                "subject" => "test subject",
                "body" => "test subject message body",
            ],
            [],
            []
        );
        $request->overrideGlobals();
        $serverRequest = self::$psrHttpFactory->createRequest($request);

        $container = self::initDIcontainer(true, false);

        $adminatorMock = self::initAdminatorMockClass($container);
        $this->assertIsObject($adminatorMock);

        $response = self::callControllerFunction($serverRequest, 'App\Controllers\othersController', 'board', $container, $adminatorMock);
        $responseContent = $response->getBody()->__toString();

        adminatorAssert::assertBoardCommon($response, $responseContent);

        // asserts for bootstrap window with action result
        self::assertXpathQueryContentRegex($response, '//*[@id="obsah"]/div[5]/div[4]/div[2]/div[2]', '/^Zpráva úspěšně uložena.$/');

        // non-common negative asserts
        $this->assertStringNotContainsStringIgnoringCase("chyba", $responseContent, "found word, which indicates error(s) or failure(s)");
        $this->assertStringNotContainsStringIgnoringCase("nepodařil", $responseContent, " found word, which indicates error(s) or failure(s)");

        // check, if record is in database
        $request2 = Request::create(
            '/others/board',
            'GET',
            [
                "action" => "view",
                "what" => "new"
            ],
            [],
            []
        );
        $request2->overrideGlobals();
        $serverRequest2 = self::$psrHttpFactory->createRequest($request2);

        $response2 = self::callControllerFunction($serverRequest2, 'App\Controllers\othersController', 'board', $container, $adminatorMock);
        $responseContent2 = $response2->getBody()->__toString();

        adminatorAssert::assertOtherCat($response);

        adminatorAssert::assertBoardCommon($response2, $responseContent2);

        // echo $responseContent2;

        // subject
        // TODO: board_add_message_sent: fix missing inserted data
        // self::assertXpathQueryContentRegex($response2, '//*[@id="obsah"]/div[5]/div[4]/div[2]/div[3]/b', '/test subject/');
        // body
        // TODO: board_add_message_sent: fix missing inserted data
        // self::assertXpathQueryContentRegex($response2, '//*[@id="obsah"]/div[5]/div[4]/div[2]/div[3]/div', '/test subject message body/');

        // non-common negative asserts
        $this->assertStringNotContainsStringIgnoringCase("chyba", $responseContent2, "found word, which indicates error(s) or failure(s)");
        $this->assertStringNotContainsStringIgnoringCase("nepodařil", $responseContent2, " found word, which indicates error(s) or failure(s)");
    }

    // TODO: add tests for BoardRSS

    // TODO: add test for CompanyWeb

}
