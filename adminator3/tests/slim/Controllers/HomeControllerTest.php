<?php

declare(strict_types=1);

namespace App\Tests;

use PHPUnit\Framework\TestCase;
use App\Controllers\HomeController;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use DI\CompiledContainer;
use DI\ContainerBuilder;
use EasyMock\EasyMock;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use PHPUnit\DbUnit\DataSet\MockDataSet;
use PDO;
use Psr\Http\Message\ResponseFactoryInterface;
use Intercept;

class HomeControllerTest extends TestCase
{
    use EasyMock;
    use \phpmock\phpunit\PHPMock;

    protected function setUp(): void
    {
        $_POST = array();
        $_POST['show_se_cat'] = "null";

        $_SERVER = array();
        $_SERVER['HTTP_HOST'] = "127.0.0.1";

    }

    public function testHome()
    {
        // $this->markTestSkipped('under construction');

        $self = $this;

        // Make the ContainerBuilder use our fake class to catch constructor parameters
        // $builder = new ContainerBuilder(FakeContainer::class);

        // $otherContainer = $this->easyMock(ContainerInterface::class);

        // $container = new Container;

        //

        // $builder->wrapContainer($otherContainer);


        // /** @var FakeContainer $container */
        // $container = $builder->build();

        // $session_status = $this->getFunctionMock("Slim\Csrf", "session_status");
        // $session_status->expects($this->once())->willReturn(PHP_SESSION_ACTIVE);

        $builder = new ContainerBuilder();
        $builder->addDefinitions('tests/slim/fixtures/bootstrapContainer.php');
        $container = $builder->build();

        // $appMock = \Mockery::mock(
        //     \Slim\Factory\AppFactory::create(),
        //     [
        //         null,
        //         $container
        //     ]
        // );

        // $appMock->shouldReceive('getResponseFactory')->andReturn();

        // $responseFactory = $appMock->getResponseFactory();
        $rfMock = \Mockery::mock(ResponseFactoryInterface::class);
        $responseFactory = $rfMock;

        require_once __DIR__ . '/../fixtures/bootstrapContainerAfter.php';

        // Not compiled
        $this->assertNotInstanceOf(CompiledContainer::class, $container);

        $this->assertInstanceOf(ContainerInterface::class, $container);

        $this->assertInstanceOf(LoggerInterface::class, $container->get('logger'));
        $this->assertIsObject($container->get('smarty'));

        $adminatorMock = \Mockery::mock(
            \App\Core\adminator::class,
            [
                $container->get('connMysql'),
                $container->get('smarty'),
                $container->get('logger'),
                '127.0.0.1', // userIPAddress
            ]
        );

        $adminatorMock->userIdentityUsername = 'test@test';
        $adminatorMock->shouldReceive('checkLevel')->andReturn(true);
        $adminatorMock->shouldReceive('getServerUri')->andReturn("http://localhost:8080/home");
        $adminatorMock->shouldReceive('zobraz_kategorie')->andReturn(
            array(
                array(),
                array()
            )
        );
        $adminatorMock->shouldReceive('list_logged_users')->andReturn("");

        $adminatorMock->shouldReceive('show_stats_faktury_neuhr')->andReturn([0, 0, 0, 0]);

        $homeController = new HomeController($container, $adminatorMock);

        $serverRequest = $this->createMock(ServerRequestInterface::class);
        $response = $this->createMock(ResponseInterface::class);

        ob_start(); // Start output buffering

        $homeController->home($serverRequest, $response, []);

        $output = ob_get_contents();

        ob_end_clean();

        $outputKeywords = array(
            '<html lang="en">',
            '<title>Adminator3 :: úvodní stránka</title>',
            'bootstrap.min.css" rel="stylesheet"',
            'Jste přihlášeni v administračním systému',
        );

        foreach ($outputKeywords as $w) {
            self::assertStringContainsString($output, $w);
        }
    }
}
