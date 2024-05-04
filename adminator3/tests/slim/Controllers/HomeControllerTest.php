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

class HomeControllerTest extends TestCase
{
    use EasyMock;

    protected function setUp(): void
    {
    }

    public function testHome()
    {
        // $this->markTestSkipped('under construction');

        $self = $this;
        // $twigProphecy = $this->prophesize(Twig::class);
        // $twigProphecy->addMethodProphecy(
        //     (new MethodProphecy(
        //         $twigProphecy,
        //         'render',
        //         [Argument::type(ResponseInterface::class), Argument::type('string'), Argument::type('array')]
        //     ))->will(function ($arguments) use ($self) {
        //         $self->assertSame('home.twig', $arguments[1]);
        //         $self->assertSame([
        //             'pageTitle' => 'Home',
        //             'rootPath' => '/path/to/root'
        //         ], $arguments[2]);

        //         return $arguments[0];
        //     })
        // );

        // $preferencesProphecy = $this->prophesize(Preferences::class);
        // $preferencesProphecy->addMethodProphecy(
        //     (new MethodProphecy($preferencesProphecy, 'getRootPath', []))
        //         ->willReturn('/path/to/root')
        // );

        // /** @var Twig $twig */
        // $twig = $twigProphecy->reveal();

        // /** @var Preferences $preferences */
        // $preferences = $preferencesProphecy->reveal();

        // Make the ContainerBuilder use our fake class to catch constructor parameters
        // $builder = new ContainerBuilder(FakeContainer::class);

        // $otherContainer = $this->easyMock(ContainerInterface::class);

        // $container = new Container;

        //

        // $builder->wrapContainer($otherContainer);


        // /** @var FakeContainer $container */
        // $container = $builder->build();



        $builder = new ContainerBuilder();
        $builder->addDefinitions('tests/slim/fixtures/bootstrapContainer.php');
        $container = $builder->build();

        require_once __DIR__ . '/../fixtures/bootstrapContainerAfter.php';

        // Not compiled
        $this->assertNotInstanceOf(CompiledContainer::class, $container);

        $this->assertInstanceOf(ContainerInterface::class, $container);

        $this->assertInstanceOf(LoggerInterface::class, $container->get('logger'));
        $this->assertIsObject($container->get('smarty'));

        $adminatorInstance = \Mockery::mock(
            \App\Core\adminator::class,
            [
                $container->get('connMysql'),
                $container->get('smarty'),
                $container->get('logger')
            ]
        );

        $adminatorInstance->userIdentityUsername = 'test@test';

        $homeController = new HomeController($container, $adminatorInstance);

        $serverRequest = $this->createMock(ServerRequestInterface::class);
        $response = $this->createMock(ResponseInterface::class);

        $homeController->home($serverRequest, $response, []);
    }
}
