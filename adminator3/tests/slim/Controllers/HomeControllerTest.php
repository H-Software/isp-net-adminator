<?php

declare(strict_types=1);

namespace App\Tests;

use PHPUnit\Framework\TestCase;
use App\Controllers\HomeController;
use App\Preferences;
use Prophecy\Argument;
use Prophecy\Prophecy\MethodProphecy;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use DI\CompiledContainer;
use DI\ContainerBuilder;
use DI\Test\UnitTest\Fixtures\FakeContainer;

// https://github.com/adriansuter/Slim4-Skeleton/blob/master/tests/Controllers/HomeControllerTest.php

class HomeControllerTest extends TestCase
{
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
        $builder = new ContainerBuilder(FakeContainer::class);
        /** @var FakeContainer $container */
        $container = $builder->build();

        // Not compiled
        $this->assertNotInstanceOf(CompiledContainer::class, $container);

        $homeController = new HomeController($container);

        $serverRequest = $this->createMock(ServerRequestInterface::class);
        $response = $this->createMock(ResponseInterface::class);

        $homeController($serverRequest, $response, []);
    }
}
