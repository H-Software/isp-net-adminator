<?php

declare(strict_types=1);

namespace App\Tests;

use App\Controllers\topologyController;
use Psr\Http\Message\ResponseFactoryInterface;

use Symfony\Component\HttpFoundation\Request;
use Nyholm\Psr7\Factory\Psr17Factory;
use Symfony\Bridge\PsrHttpMessage\Factory\PsrHttpFactory;

final class TopologyControllerTest extends AdminatorTestCase
{
    protected $psrHttpFactory;

    protected $serverRequest;

    protected $topologyController;

    protected function setUp(): void
    {
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
    }

    public function test_ctl_node_list_view_all()
    {
        // $this->markTestSkipped('under construction');
        $self = $this;

        $request = Request::create(
            '/topology/node-list',
            'GET',
            []
        );
        $request->overrideGlobals();
        $serverRequest = $this->psrHttpFactory->createRequest($request);

        $container = self::initDIcontainer(true, false);

        $adminatorMock = self::initAdminatorMockClass($container);
        $this->assertIsObject($adminatorMock);

        $topologyController = new topologyController($container, $adminatorMock);

        $responseFactory = $container->get(ResponseFactoryInterface::class);
        $response = $responseFactory->createResponse();

        $response = $topologyController->nodeList($serverRequest, $response, []);

        $responseContent = $response->getBody()->__toString();

        // echo $responseContent;

        $this->assertEquals($response->getStatusCode(), 200);

        adminatorAssert::assertBase($responseContent);

        adminatorAssert::assertTopologySubCat($responseContent);

        AdminatorAssert::assertTopologyNodeListHeaderAndSelectors($responseContent);

        AdminatorAssert::assertTopologyNodeListTableHeader($responseContent);

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

        // clean-up
        $responseContent = null;
        $response = null;
        $responseFactory = null;
        $topologyController = null;
        $adminatorMock = null;
        $container = null;
        $serverRequest = null;
        $request = null;
    }

    public function test_ctl_node_list_with_low_user_level()
    {
        // $this->markTestSkipped('under construction');
        $self = $this;

        $request = Request::create(
            '/topology/node-list',
            'GET',
            []
        );
        $request->overrideGlobals();
        $serverRequest = $this->psrHttpFactory->createRequest($request);

        $container = self::initDIcontainer(true, false);

        $adminatorMock = self::initAdminatorMockClass($container, false, 1);
        $this->assertIsObject($adminatorMock);

        $topologyController = new topologyController($container, $adminatorMock);

        $responseFactory = $container->get(ResponseFactoryInterface::class);
        $response = $responseFactory->createResponse();

        $response = $topologyController->nodeList($serverRequest, $response, []);

        $responseContent = $response->getBody()->__toString();
        $this->assertNotEmpty($responseContent);

        // echo $responseContent;

        adminatorAssert::assertBase($responseContent);

        // page specific asserts
        AdminatorAssert::assertNoLevelPage($response);

        // non-common negative asserts
        $this->assertStringNotContainsStringIgnoringCase("nepodařil", $responseContent, " found word, which indicates error(s) or failure(s)");

        // clean-up
        $responseContent = null;
        $response = null;
        $responseFactory = null;
        $topologyController = null;
        $adminatorMock = null;
        $container = null;
        $serverRequest = null;
        $request = null;
    }

    public function test_ctl_node_list_view_with_non_exist_find_param()
    {
        // $this->markTestSkipped('under construction');
        $self = $this;

        $request = Request::create(
            '/topology/node-list',
            'GET',
            ['find' => 'this-realy-dont-exist']
        );
        $request->overrideGlobals();
        $serverRequest = $this->psrHttpFactory->createRequest($request);

        $container = self::initDIcontainer(true, false);

        $adminatorMock = self::initAdminatorMockClass($container);
        $this->assertIsObject($adminatorMock);

        $topologyController = new topologyController($container, $adminatorMock);

        $responseFactory = $container->get(ResponseFactoryInterface::class);
        $response = $responseFactory->createResponse();

        $response = $topologyController->nodeList($serverRequest, $response, []);

        $responseContent = $response->getBody()->__toString();

        $this->assertEquals($response->getStatusCode(), 200);

        // echo $responseContent;

        adminatorAssert::assertBase($responseContent);

        adminatorAssert::assertTopologySubCat($responseContent);

        AdminatorAssert::assertTopologyNodeListHeaderAndSelectors($responseContent);

        adminatorAssert::assertTopologyNodeListNoDataFound($responseContent);

        // clean-up
        $responseContent = null;
        $response = null;
        $responseFactory = null;
        $topologyController = null;
        $adminatorMock = null;
        $container = null;
        $serverRequest = null;
        $request = null;
    }

}
