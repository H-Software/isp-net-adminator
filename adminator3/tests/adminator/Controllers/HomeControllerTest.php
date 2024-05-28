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

    public function testHome()
    {
        $this->markTestSkipped('under construction');
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

        // // test sqlite migration
        // $sql = 'pragma table_info(\'board\');';
        // $sql2 = "SELECT * FROM board";
        // $rs = self::$pdoMysql->query($sql2);
        // print_r($rs->fetchAll());

        // debug
        // echo $responseContent;

        $this->assertNotEmpty($responseContent);

        $outputKeywords = array(
            '<html lang="en">',
            '<title>Adminator3 :: úvodní stránka</title>',  // adminator head rendered
            'bootstrap.min.css" rel="stylesheet"',  // adminator head rendered
            'Jste přihlášeni v administračním systému', // adminator header rendered
            '<div class="home-vypis-useru-napis" >Přihlašení uživatelé: </div>', // loggeduser banner
            'uživatel: <span class="home-vypis-useru-font1" >', // logger user row
            'Výpis Závad/oprav',
            'Bulletin Board - Nástěnka', // board header exists
            '<div class="table zprava-main" >', // board message exists
            '</body>', // smarty rendered whole page
            '</html>' // smarty rendered whole page
        );

        foreach ($outputKeywords as $w) {

            /*
            // N.B.:
            // assert below causes printing output to stdout
            // workaround is using this foraech with assertFalse
            // UPDATE: it works ATM.. probably :)
            */
            // $this->assertStringContainsString($output, $w);

            // TODO: maybe will works "assertThat()"
            // -> https://docs.phpunit.de/en/9.6/assertions.html#assertthat

            if (!str_contains($responseContent, $w)) {
                $this->assertFalse(true, "missing string \"" . $w . "\" in controller output");
            }
        }

        if (preg_match("/(failed|chyba|error)+/i", $responseContent)) {
            $this->assertFalse(true, "found some word(s), which indicates error(s)");
        }
    }
}
