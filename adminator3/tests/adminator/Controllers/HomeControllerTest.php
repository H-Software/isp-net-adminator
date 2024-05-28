<?php

declare(strict_types=1);

namespace App\Tests;

use Mockery as m;
use App\Controllers\HomeController;
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

        $_SERVER = array();
        $_SERVER['HTTP_HOST'] = "127.0.0.1";
        $_SERVER['SCRIPT_URL'] = "/home";
        $_SERVER['REMOTE_ADDR'] = "127.0.0.1";
        $_SERVER["REQUEST_URI"] = '/test';
    }

    protected function tearDown(): void
    {
        m::close();
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

        $this->assertEquals($response->getStatusCode(), 200);

        $responseContent = $response->getBody()->__toString();

        self::runBasicAsserts($responseContent);

        $assertKeywordsHome = array(
            '<title>Adminator3 :: úvodní stránka</title>',  // adminator head rendered
            '<div class="home-vypis-useru-napis" >Přihlašení uživatelé: </div>', // loggeduser banner
            'uživatel: <span class="home-vypis-useru-font1" >', // logger user row
            'Výpis Závad/oprav',
            'Bulletin Board - Nástěnka', // board header exists
            '<div class="table zprava-main" >', // board message exists
        );

        foreach ($assertKeywordsHome as $w) {
            $this->assertStringContainsString($w, $responseContent, __FUNCTION__ . " :: missing string \"" . $w . "\" in response body");
        }

        // negative assert
        // check word: nelze
        $this->assertStringNotContainsStringIgnoringCase("chyba", $responseContent, __FUNCTION__ . " :: found word (" . $w. "), which indicates error(s) or failure(s)");
        $this->assertStringNotContainsStringIgnoringCase("nepodařil", $responseContent, __FUNCTION__ . " :: found word (" . $w. "), which indicates error(s) or failure(s)");

    }
}
