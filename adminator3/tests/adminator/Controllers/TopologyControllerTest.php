<?php

declare(strict_types=1);

namespace App\Tests;

use App\Controllers\topologyController;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Slim\Interfaces\RouteParserInterface;
use Nyholm\Psr7Server\ServerRequestCreator;

final class TopologyControllerTest extends AdminatorTestCase
{
    /** @var ServerRequestCreator */
    protected $creator;

    protected $serverRequest;

    protected $topologyController;

    protected function setUp(): void
    {
        $psr17Factory = new \Nyholm\Psr7\Factory\Psr17Factory();

        $this->creator = new ServerRequestCreator(
            $psr17Factory,
            $psr17Factory,
            $psr17Factory,
            $psr17Factory
        );
    }

    protected function tearDown(): void
    {

    }

    public function test_ctl_node_list_default_view()
    {
        // $this->markTestSkipped('under construction');
        $self = $this;

        $container = self::initDIcontainer(true, false);

        $adminatorMock = self::initAdminatorMockClass($container);
        $this->assertIsObject($adminatorMock);

        $topologyController = new topologyController($container, $adminatorMock);

        $server = [
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => '/topology/router',
        ];

        $serverRequest = $this->creator->fromArrays(
            $server,
            [],
            [],
            [],
        );

        $responseFactory = $container->get(ResponseFactoryInterface::class);
        $response = $responseFactory->createResponse();

        $response = $topologyController->nodeList($serverRequest, $response, []);

        $this->assertEquals($response->getStatusCode(), 200);

        $responseContent = $response->getBody()->__toString();

        // echo $responseContent;

        self::runBasicAsserts($responseContent);

        // page specific asserts
        //
        // Výpis lokalit / přípojných bodů
        // Hledání:
        // class="alert alert-warning" role="alert" / boostrap window
        // Žadné lokality/nody dle hladeného výrazu ( % ) v databázi neuloženy.

        $this->assertMatchesRegularExpression('/Výpis lokalit\s*\/\s*přípojných bodů/i', $responseContent);
        $this->assertMatchesRegularExpression('/Hledání\:/i', $responseContent);
        $this->assertMatchesRegularExpression('/class="alert\s*alert-warning"\s*role="alert"/i', $responseContent);

        // no data assert
        $this->assertMatchesRegularExpression('/Žadné lokality\/nody dle hladeného výrazu \( % \) v databázi neuloženy/i', $responseContent);

        // clean-up
        $response = null;
        $topologyController = null;
        $serverRequest = null;
    }

    public function test_ctl_node_list_with_low_user_level()
    {
        // $this->markTestSkipped('under construction');
        $self = $this;

        $container = self::initDIcontainer(true, false);

        $adminatorMock = self::initAdminatorMockClass($container, false, 1);
        $this->assertIsObject($adminatorMock);

        $topologyController = new topologyController($container, $adminatorMock);

        $server = [
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => '/topology/router',
        ];

        $serverRequest = $this->creator->fromArrays(
            $server,
            [],
            [],
            [],
        );

        $responseFactory = $container->get(ResponseFactoryInterface::class);
        $response = $responseFactory->createResponse();

        $response = $topologyController->nodeList($serverRequest, $response, []);

        $this->assertEquals($response->getStatusCode(), 403);

        $responseContent = $response->getBody()->__toString();
        $this->assertNotEmpty($responseContent);

        // echo $responseContent;

        self::runBasicAsserts($responseContent);

        // page specific asserts
        $this->assertStringContainsString("Nelze zobrazit požadovanou stránku", $responseContent, __FUNCTION__ . " :: missing string 1 in response body");
        $this->assertStringContainsString("Pro otevřetí této stránky nemáte dostatečné oprávnění (level).", $responseContent, __FUNCTION__ . " :: missing string 2 in response body");

        // negative asserts
        // TODO: fix me
        
        // clean-up
        $response = null;
        $topologyController = null;
        $serverRequest = null;
    }
}
