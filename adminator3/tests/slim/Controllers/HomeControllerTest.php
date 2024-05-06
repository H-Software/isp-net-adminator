<?php

declare(strict_types=1);

namespace App\Tests;

use App\Controllers\HomeController;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

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
    }

    protected function tearDown(): void
    {
    }

    public function testHome()
    {
        // $this->markTestSkipped('under construction');
        $self = $this;

        $container = self::initDIcontainer();

        // mock "underlaying" class for helper functions/logic
        $adminatorMock = \Mockery::mock(
            \App\Core\adminator::class,
            [
                $container->get('connMysql'),
                $container->get('smarty'),
                $container->get('logger'),
                '127.0.0.1', // userIPAddress
                $container->get('pdoMysql'),
                $container->get('settings'),
            ]
        )->makePartial();

        $adminatorMock->userIdentityUsername = 'test@test';
        $adminatorMock->shouldReceive('checkLevel')->andReturn(true);
        $adminatorMock->shouldReceive('getServerUri')->andReturn("http://localhost:8080/home");
        $adminatorMock->shouldReceive('zobraz_kategorie')->andReturn(
            array(
                array(),
                array()
            )
        );
        // $adminatorMock->shouldReceive('list_logged_users')->andReturn("");
        // $adminatorMock->shouldReceive('show_stats_faktury_neuhr')->andReturn([0, 0, 0, 0]);

        $homeController = new HomeController($container, $adminatorMock);

        $serverRequest = $this->createMock(ServerRequestInterface::class);
        $response = $this->createMock(ResponseInterface::class);

        ob_start();

        $homeController->home($serverRequest, $response, []);


        // // test sqlite migration
        // $sql = 'pragma table_info(\'board\');';
        // $sql2 = "SELECT * FROM users";
        // $rs = self::$pdoMysql->query($sql2);
        // print_r($rs->fetchAll());

        $output = ob_get_contents();

        ob_end_clean();

        // debug
        // echo $output;

        $this->assertNotEmpty($output);

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
            */
            // $this->assertStringContainsString($output, $w);

            // TODO: maybe will works "assertThat()"
            // -> https://docs.phpunit.de/en/9.6/assertions.html#assertthat

            if (!str_contains($output, $w)) {
                $this->assertFalse(true, "missing string \"" . $w . "\" in controller output");
            }
        }

        if (preg_match("/(failed|chyba|error)+/i", $output)) {
            // TODO: enable this assert after fix database UP operation
            // $this->assertFalse(true, "found some word(s), which indicates error(s)");
        }
    }
}
