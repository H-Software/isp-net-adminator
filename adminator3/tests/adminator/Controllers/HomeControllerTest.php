<?php

declare(strict_types=1);

namespace App\Tests;

use Mockery as m;
use App\Controllers\HomeController;
use Psr\Http\Message\ResponseFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

final class HomeControllerTest extends AdminatorTestCase
{
    protected $serverRequest;

    protected function setUp(): void
    {
    }

    protected function tearDown(): void
    {
        m::close();
    }

    public function test_ctl_home_w_mocked_auth_and_opravy()
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
        $serverRequest = self::$psrHttpFactory->createRequest($request);

        $container = self::initDIcontainer(true, false);

        $adminatorMock = self::initAdminatorMockClass($container);
        $this->assertIsObject($adminatorMock);

        $opravyMock = m::mock(
            \opravy::class,
        );
        $opravyMock->shouldReceive('vypis_opravy')->andReturn(["mock -> no data"]);

        $response = self::callControllerFunction($serverRequest, 'App\Controllers\HomeController', 'home', $container, $adminatorMock);
        $responseContent = $response->getBody()->__toString();

        AdminatorAssert::assertHomePagePanels($response, $responseContent);

        // page specific asserts
        //
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
