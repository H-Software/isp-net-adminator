<?php

declare(strict_types=1);

namespace App\Tests;

use App\Controllers\topologyController;
use Psr\Http\Message\ResponseFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

final class TopologyControllerTest extends AdminatorTestCase
{
    protected $serverRequest;

    protected function setUp(): void
    {
    }

    protected function tearDown(): void
    {
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
        $serverRequest = self::$psrHttpFactory->createRequest($request);

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

        adminatorAssert::assertTopologySubCat($response, $responseContent);

        AdminatorAssert::assertTopologyNodeListHeaderAndSelectors($responseContent);

        AdminatorAssert::assertTopologyNodeListTableHeader($responseContent);

        // listing asserts
        $this->assertStringContainsString('<div class="text-listing">', $responseContent, "missing listing container");
        $this->assertStringContainsString('<a href="/topology/node-list', $responseContent, "missing listing link");

        // table body asserts
        $this->assertStringContainsString('<td width="5%" colspan="1"  class="tab-topology2 tab-topology-dolni2"', $responseContent);
        $this->assertStringContainsString('target="_blank" >na mapě</a>', $responseContent);
        $this->assertMatchesRegularExpression('/<a href="\/archiv-zmen\?id_nodu=[0-9]+" style="font-size: 12px; ">H: [0-9]+/i', $responseContent);
        $this->assertStringContainsString('<td><form method="POST" action="/topology/node/update">', $responseContent);
        $this->assertStringContainsString('<td><form action="/topology/node/erase" method="POST"', $responseContent);

        // page specific negative asserts
        $this->assertStringNotContainsStringIgnoringCase("nelze zjistit", $responseContent, "unable to show parent router name");

        // non-common negative asserts
        $this->assertStringNotContainsStringIgnoringCase("chyba", $responseContent, "found word, which indicates error(s) or failure(s)");
        $this->assertStringNotContainsStringIgnoringCase("nepodařil", $responseContent, " found word, which indicates error(s) or failure(s)");
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
        $serverRequest = self::$psrHttpFactory->createRequest($request);

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
        $serverRequest = self::$psrHttpFactory->createRequest($request);

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

        adminatorAssert::assertTopologySubCat($response, $responseContent);

        AdminatorAssert::assertTopologyNodeListHeaderAndSelectors($responseContent);

        adminatorAssert::assertTopologyNodeListNoDataFound($responseContent);
    }

    public function test_ctl_router_list_view_all()
    {
        // $this->markTestSkipped('under construction');
        $self = $this;

        $request = Request::create(
            '/topology/router-list',
            'GET',
            []
        );
        $request->overrideGlobals();
        $serverRequest = self::$psrHttpFactory->createRequest($request);

        $container = self::initDIcontainer(true, false);

        $adminatorMock = self::initAdminatorMockClass($container);
        $this->assertIsObject($adminatorMock);

        $topologyController = new topologyController($container, $adminatorMock);

        $responseFactory = $container->get(ResponseFactoryInterface::class);
        $response = $responseFactory->createResponse();

        $response = $topologyController->routerList($serverRequest, $response, []);

        $responseContent = $response->getBody()->__toString();

        // echo $responseContent;

        $this->assertEquals($response->getStatusCode(), 200);

        adminatorAssert::assertBase($responseContent);

        adminatorAssert::assertTopologySubCat($response, $responseContent);

        AdminatorAssert::assertTopologyRouterListHeaderAndSelectors($response);

        // TODO: router_list_view_all: add asserts

        // AdminatorAssert::assertTopologyRouterListTableHeader($responseContent);

        // // listing asserts
        // $this->assertStringContainsString('<div class="text-listing">', $responseContent, "missing listing container");
        // $this->assertStringContainsString('<a href="/topology/node-list', $responseContent, "missing listing link");

        // // table body asserts
        // $this->assertStringContainsString('<td width="5%" colspan="1"  class="tab-topology2 tab-topology-dolni2"', $responseContent);
        // $this->assertStringContainsString('target="_blank" >na mapě</a>', $responseContent);
        // $this->assertMatchesRegularExpression('/<a href="\/archiv-zmen\?id_nodu=[0-9]+" style="font-size: 12px; ">H: [0-9]+/i', $responseContent);
        // $this->assertStringContainsString('<td><form method="POST" action="/topology/nod-update">', $responseContent);
        // $this->assertStringContainsString('<td><form action="/topology/nod-erase" method="POST"', $responseContent);

        // // page specific negative asserts
        // $this->assertStringNotContainsStringIgnoringCase("nelze zjistit", $responseContent, "unable to show parent router name");

        // non-common negative asserts
        $this->assertStringNotContainsStringIgnoringCase("chyba", $responseContent, "found word, which indicates error(s) or failure(s)");
        $this->assertStringNotContainsStringIgnoringCase("nepodařil", $responseContent, " found word, which indicates error(s) or failure(s)");
    }

    public function test_ctl_router_list_view_with_non_exist_find_param()
    {
        // $this->markTestSkipped('under construction');
        $self = $this;

        $request = Request::create(
            '/topology/router-list',
            'GET',
            ['f_search' => 'this-realy-dont-exist']
        );
        $request->overrideGlobals();
        $serverRequest = self::$psrHttpFactory->createRequest($request);

        $container = self::initDIcontainer(true, false);

        $adminatorMock = self::initAdminatorMockClass($container);
        $this->assertIsObject($adminatorMock);

        $topologyController = new topologyController($container, $adminatorMock);

        $responseFactory = $container->get(ResponseFactoryInterface::class);
        $response = $responseFactory->createResponse();

        $response = $topologyController->routerList($serverRequest, $response, []);

        $responseContent = $response->getBody()->__toString();

        // echo $responseContent;

        $this->assertEquals($response->getStatusCode(), 200);

        adminatorAssert::assertBase($responseContent);

        adminatorAssert::assertTopologySubCat($response, $responseContent);

        AdminatorAssert::assertTopologyRouterListHeaderAndSelectors($response);

        self::assertXpathQueryContentRegex($response, '//*[@id="obsah"]/div[5]/div[2]/div[2]/div', '/^Žádné záznamy dle hledaného kritéria\.$/');

        // non-common negative asserts
        $this->assertStringNotContainsStringIgnoringCase("chyba", $responseContent, "found word, which indicates error(s) or failure(s)");
        $this->assertStringNotContainsStringIgnoringCase("nepodařil", $responseContent, " found word, which indicates error(s) or failure(s)");
    }

    public function test_ctl_router_list_view_with_find_id_routeru()
    {
        // $this->markTestSkipped('under construction');
        $self = $this;

        $request = Request::create(
            '/topology/router-list',
            'GET',
            ['f_id_routeru' => '1']
        );
        $request->overrideGlobals();
        $serverRequest = self::$psrHttpFactory->createRequest($request);

        $container = self::initDIcontainer(true, false);

        $adminatorMock = self::initAdminatorMockClass($container);
        $this->assertIsObject($adminatorMock);

        $topologyController = new topologyController($container, $adminatorMock);

        $responseFactory = $container->get(ResponseFactoryInterface::class);
        $response = $responseFactory->createResponse();

        $response = $topologyController->routerList($serverRequest, $response, []);

        $responseContent = $response->getBody()->__toString();

        // echo $responseContent;

        $this->assertEquals($response->getStatusCode(), 200);

        adminatorAssert::assertBase($responseContent);

        adminatorAssert::assertTopologySubCat($response, $responseContent);

        AdminatorAssert::assertTopologyRouterListHeaderAndSelectors($response);

        // TODO: router_list_view_with_non_exist_find_param: add assert for table header

        // TODO: router_list_view_with_non_exist_find_param: add assert for router item

        // non-common negative asserts
        $this->assertStringNotContainsStringIgnoringCase("chyba", $responseContent, "found word, which indicates error(s) or failure(s)");
        $this->assertStringNotContainsStringIgnoringCase("nepodařil", $responseContent, " found word, which indicates error(s) or failure(s)");
    }

    public function test_ctl_router_list_view_with_list_nodes()
    {
        // $this->markTestSkipped('under construction');
        $self = $this;

        $request = Request::create(
            '/topology/router-list',
            'GET',
            [
                'f_id_routeru' => '1',
                'list_nodes'   => 'yes',
            ]
        );
        $request->overrideGlobals();
        $serverRequest = self::$psrHttpFactory->createRequest($request);

        $container = self::initDIcontainer(true, false);

        $adminatorMock = self::initAdminatorMockClass($container);
        $this->assertIsObject($adminatorMock);

        $topologyController = new topologyController($container, $adminatorMock);

        $responseFactory = $container->get(ResponseFactoryInterface::class);
        $response = $responseFactory->createResponse();

        $response = $topologyController->routerList($serverRequest, $response, []);

        $responseContent = $response->getBody()->__toString();

        // echo $responseContent;

        $this->assertEquals($response->getStatusCode(), 200);

        adminatorAssert::assertBase($responseContent);

        adminatorAssert::assertTopologySubCat($response, $responseContent);

        AdminatorAssert::assertTopologyRouterListHeaderAndSelectors($response);

        // TODO: router_list_view_with_non_exist_find_param: add assert for table header

        // TODO: router_list_view_with_non_exist_find_param: add assert for router item

        // node list view
        //
        self::assertXpathQueryContentRegex($response, '//*[@id="topology-router-list-node-view-name-0"]', '/^(\w|\W|\s){5,}$/');
        self::assertXpathQueryContentRegex($response, '//*[@id="topology-router-list-node-view-detail-link-0"]/a', '/^detail nodu\s*$/');
        self::assertXpathQueryContentRegex($response, '//*[@id="topology-router-list-node-view-detail-link-0"]/a', '/^\/topology\/node-list\?find=(\w|\W){3,}$/');

        // non-common negative asserts
        $this->assertStringNotContainsStringIgnoringCase("chyba", $responseContent, "found word, which indicates error(s) or failure(s)");
        $this->assertStringNotContainsStringIgnoringCase("nepodařil", $responseContent, " found word, which indicates error(s) or failure(s)");
    }

    public function test_ctl_router_list_hierarchy()
    {
        // $this->markTestSkipped('under construction');
        $self = $this;

        $request = Request::create(
            '/topology/router-list',
            'GET',
            [
                'typ' => '1',
            ]
        );
        $request->overrideGlobals();
        $serverRequest = self::$psrHttpFactory->createRequest($request);

        $container = self::initDIcontainer(true, false);

        $adminatorMock = self::initAdminatorMockClass($container);
        $this->assertIsObject($adminatorMock);

        $topologyController = new topologyController($container, $adminatorMock);

        $responseFactory = $container->get(ResponseFactoryInterface::class);
        $response = $responseFactory->createResponse();

        $response = $topologyController->routerList($serverRequest, $response, []);

        $responseContent = $response->getBody()->__toString();

        // echo $responseContent;

        $this->assertEquals($response->getStatusCode(), 200);

        adminatorAssert::assertBase($responseContent);

        adminatorAssert::assertTopologySubCat($response, $responseContent);

        self::assertXpathQueryContentRegex($response, '//*[@id="obsah"]/div[5]/div[2]/div[1]/span[1]', '/^.:: Výpis routerů ::.$/');

        // assert 1. level
        self::assertXpathQueryContentRegex($response, '//*[@id="router-list-hierarchy-level-0-name"]', '/^(\w|\W|\s){5,}$/');

        // assert 2. level
        self::assertXpathQueryContentRegex($response, '//*[@id="router-list-hierarchy-level-1-name"]', '/^(\w|\W|\s){5,}$/');

        // non-common negative asserts
        $this->assertStringNotContainsStringIgnoringCase("chyba", $responseContent, "found word, which indicates error(s) or failure(s)");
        $this->assertStringNotContainsStringIgnoringCase("nepodařil", $responseContent, " found word, which indicates error(s) or failure(s)");
    }

    // TODO: add test for node-list with search

    // TODO: add test for RouterList with search

    // TODO: add test for node-action

    // TODO: add test for router-action

}
