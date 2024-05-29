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
        $this->creator = null;
    }

    public function test_ctl_node_list_view_all()
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

        // section sub-cat asserts
        $this->assertMatchesRegularExpression('/<a class="cat2" href="\/topology\/router-list">Routery<\/a>/i', $responseContent);
        $this->assertMatchesRegularExpression('/<a class="cat2" href="\/topology\/node-list">Výpis lokalit\/nodů<\/a>/i', $responseContent);
        $this->assertMatchesRegularExpression('/<a class="cat2" href="topology-user-list.php">Výpis objektů dle přiřazení \/ dle nodů<\/a>/i', $responseContent);

        // page header & selector/fiters asserts
        $this->assertMatchesRegularExpression('/Výpis lokalit\s*\/\s*přípojných bodů/i', $responseContent);
        $this->assertMatchesRegularExpression('/Hledání\:/i', $responseContent);
        $this->assertStringContainsString('<select name="typ_vysilace"', $responseContent);
        $this->assertStringContainsString('<select name="typ_nodu" size="1"', $responseContent);

        // table header asserts
        $this->assertStringContainsString('Umístění aliasu (název routeru):', $responseContent);
        $this->assertStringContainsString('Stav: </span>', $responseContent);
        $this->assertStringContainsString('Úprava / Smazání:', $responseContent);
        $this->assertStringContainsString('<select name="typ_nodu" size="1"', $responseContent);

        // listing asserts
        $this->assertStringContainsString('<div class="text-listing">', $responseContent, "missing listing container");
        $this->assertStringContainsString('<a href="/topology/node-list', $responseContent, "missing listing link");

        // table body asserts
        $this->assertStringContainsString('<td width="5%" colspan="1"  class="tab-topology2 tab-topology-dolni2"', $responseContent);
        $this->assertStringContainsString('target="_blank" >na mapě</a>', $responseContent);
        $this->assertMatchesRegularExpression('/<a href="\/archiv-zmen\?id_nodu=[0-9]+" style="font-size: 12px; ">H: [0-9]+/i', $responseContent);
        $this->assertStringContainsString('<td><form method="POST" action="/topology/nod-update">', $responseContent);
        $this->assertStringContainsString('<td><form action="/topology/nod-erase" method="POST"', $responseContent);

        // page specific negative asserts
        $this->assertStringNotContainsStringIgnoringCase("nelze zjistit", $responseContent, "unable to show parent router name");

        // non-common negative asserts
        $this->assertStringNotContainsStringIgnoringCase("chyba", $responseContent, "found word, which indicates error(s) or failure(s)");
        $this->assertStringNotContainsStringIgnoringCase("nepodařil", $responseContent, " found word, which indicates error(s) or failure(s)");

        // no data assert
        // $this->assertMatchesRegularExpression('/class="alert\s*alert-warning"\s*role="alert"/i', $responseContent, "missing no-data message container");
        // $this->assertMatchesRegularExpression('/Žadné lokality\/nody dle hladeného výrazu \( % \) v databázi neuloženy/i', $responseContent, "missing no-data message");

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

        // non-common negative asserts
        $this->assertStringNotContainsStringIgnoringCase("nepodařil", $responseContent, " found word, which indicates error(s) or failure(s)");

        // clean-up
        $response = null;
        $topologyController = null;
        $serverRequest = null;
    }

    public function test_ctl_node_list_view_non_exist()
    {
        $this->markTestSkipped('under construction');
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

        // clean-up
        $response = null;
        $topologyController = null;
        $serverRequest = null;
    }

}
