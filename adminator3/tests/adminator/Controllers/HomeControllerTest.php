<?php

declare(strict_types=1);

namespace App\Tests;

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
    }

    public function testLogin()
    {
        // $this->markTestSkipped('under construction');
        $self = $this;

        $container = self::initDIcontainer();
        // $responseFactory = $container->get(ResponseFactoryInterface::class);

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
        $adminatorMock->shouldReceive('getUserLevel')->andReturn(1);
        $adminatorMock->shouldReceive('checkLevel')->andReturn(true);
        $adminatorMock->shouldReceive('getServerUri')->andReturn("http://localhost:8080/home");
        // $adminatorMock->shouldReceive('zobraz_kategorie')->andReturn(
        //     require __DIR__ . "/../../fixtures/zobraz_kategorie_data.php"
        // );
        $adminatorMock->shouldReceive('getUserToken')->andReturn(false);
        // $adminatorMock->shouldReceive('show_stats_faktury_neuhr')->andReturn([0, 0, 0, 0]);

    }
}
