<?php

declare(strict_types=1);

namespace App\Tests;

use Mockery as m;
use App\Controllers\HomeController;
use App\Core\adminator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseFactoryInterface;

final class HomeControllerTest extends AdminatorTestCase
{
    protected function setUp(): void
    {
        // prepare data for forms
        //
        $_POST = array();
        $_POST['show_se_cat'] = "null";

        $_GET = array();
        $_GET["v_reseni_filtr"] = 99;
        $_GET["vyreseno_filtr"] = 0;
        $_GET["limit"] = 10;

        // $_SERVER = array();
        $_SERVER['HTTP_HOST'] = "127.0.0.1";
        $_SERVER['SCRIPT_URL'] = "/home";
        $_SERVER['REMOTE_ADDR'] = "127.0.0.1";
        $_SERVER["REQUEST_URI"] = '/test';
    }

    protected function tearDown(): void
    {
        m::close();

        // unset($_SERVER['HTTP_HOST']);
        // unset($_SERVER['SCRIPT_URL']);
        // unset($_SERVER['REMOTE_ADDR']);
        // unset($_SERVER["REQUEST_URI"]);

        unset($_GET["v_reseni_filtr"]);
        unset($_GET["vyreseno_filtr"]);
        unset($_GET["limit"]);
    }

    public function test_ctl_home_page()
    {
        // $this->markTestSkipped('under construction');
        $self = $this;

        $container = self::initDIcontainer(true, false);

        $adminatorMock = self::initAdminatorMockClass($container);
        $this->assertIsObject($adminatorMock);

        $opravyMock = m::mock(
            \opravy::class,
        );
        $opravyMock->shouldReceive('vypis_opravy')->andReturn(["mock -> no data"]);

        $homeController = new HomeController($container, $adminatorMock, $opravyMock);

        $serverRequest = $this->createMock(ServerRequestInterface::class);
        // $response = $this->createMock(ResponseInterface::class);
        $responseFactory = $container->get(ResponseFactoryInterface::class);
        $response = $responseFactory->createResponse();

        $response = $homeController->home($serverRequest, $response, []);

        $responseContent = $response->getBody()->__toString();

        $this->assertEquals($response->getStatusCode(), 200);

        adminatorAssert::assertBase($responseContent);

        AdminatorAssert::assertHomePagePanels($responseContent);

        // page specific asserts
        $assertKeywordsHome = array(
            '<title>Adminator3 :: úvodní stránka</title>',  // corrent title
        );

        foreach ($assertKeywordsHome as $w) {
            $this->assertStringContainsString($w, $responseContent, "missing string \"" . $w . "\" in response body");
        }

        // negative assert
        // check word: nelze
        $this->assertStringNotContainsStringIgnoringCase("chyba", $responseContent,"found word (" . $w. "), which indicates error(s) or failure(s)");
        $this->assertStringNotContainsStringIgnoringCase("nepodařil", $responseContent, "found word (" . $w. "), which indicates error(s) or failure(s)");

    }
}
