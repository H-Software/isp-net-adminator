<?php

declare(strict_types=1);

namespace App\Tests;

use Mockery as m;
use App\Controllers\HomeController;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseFactoryInterface;

use Symfony\Component\HttpFoundation\Request;
use Nyholm\Psr7\Factory\Psr17Factory;
use Symfony\Bridge\PsrHttpMessage\Factory\PsrHttpFactory;

final class HomeControllerTest extends AdminatorTestCase
{
    protected $psrHttpFactory;

    protected $serverRequest;

    protected function setUp(): void
    {
        // prepare data for forms
        //
        // $_POST = array();
        // $_POST['show_se_cat'] = "null";

        // $_GET = array();
        // $_GET["v_reseni_filtr"] = 99;
        // $_GET["vyreseno_filtr"] = 0;
        // $_GET["limit"] = 10;

        // $_SERVER = array();
        // $_SERVER['HTTP_HOST'] = "127.0.0.1";
        // $_SERVER['SCRIPT_URL'] = "/home";
        // $_SERVER['REMOTE_ADDR'] = "127.0.0.1";
        // $_SERVER["REQUEST_URI"] = '/test';

        $psr17Factory = new Psr17Factory();

        // https://symfony.com/doc/current/components/psr7.html#converting-from-httpfoundation-objects-to-psr-7
        $this->psrHttpFactory = new PsrHttpFactory(
            $psr17Factory,
            $psr17Factory,
            $psr17Factory,
            $psr17Factory
        );
    }

    protected function tearDown(): void
    {
        $this->psrHttpFactory = null;

        m::close();
    }

    public function test_ctl_home_page()
    {
        // $this->markTestSkipped('under construction');
        $self = $this;

        $request = Request::create(
            '/home',
            'GET',
            [
                "v_reseni_filtr" => 99,
                "vyreseno_filtr" => 0,
                "limit" => 10,
            ],
            [],
            [],
            [
                'SCRIPT_URL' => "/home",
            ],
        );

        $request->overrideGlobals();
        $serverRequest = $this->psrHttpFactory->createRequest($request);

        $container = self::initDIcontainer(true, false);

        $adminatorMock = self::initAdminatorMockClass($container);
        $this->assertIsObject($adminatorMock);

        $opravyMock = m::mock(
            \opravy::class,
        );
        $opravyMock->shouldReceive('vypis_opravy')->andReturn(["mock -> no data"]);

        $homeController = new HomeController($container, $adminatorMock, $opravyMock);

        // $serverRequest = $this->createMock(ServerRequestInterface::class);
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
        $this->assertStringNotContainsStringIgnoringCase("chyba", $responseContent, "found word (" . $w. "), which indicates error(s) or failure(s)");
        $this->assertStringNotContainsStringIgnoringCase("nepodařil", $responseContent, "found word (" . $w. "), which indicates error(s) or failure(s)");
    }
}
