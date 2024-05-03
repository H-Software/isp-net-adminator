<?php

namespace App\Controllers;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class topologyController extends adminatorController
{
    public $conn_mysql;
    public $smarty;
    public $logger;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->conn_mysql = $this->container->get('connMysql');
        $this->smarty = $this->container->get('smarty');
        $this->logger = $this->container->get('logger');
        $this->logger->info("topologyController\__construct called");

        $this->adminator = new \App\Core\adminator($this->conn_mysql, $this->smarty, $this->logger);
    }

    public function nodeList(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $this->logger->info("topologyController\\nodeList called");

        $this->checkLevel(5, $this->adminator);

        $topology = new \App\Core\Topology($this->conn_mysql, $this->smarty, $this->logger);

        $this->smarty->assign("page_title", "Adminator3 :: Topologie :: Node list");

        $this->header($request, $response, $this->adminator);

        $output = $topology->getNodeList();

        $this->smarty->assign("body", $output);

        $this->smarty->display('topology/node-list.tpl');

        return $response;
    }

    public function routerList(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $this->logger->info("topologyController\\routerList called");

        $this->checkLevel(85, $this->adminator);

        $topology = new \App\Core\Topology($this->conn_mysql, $this->smarty, $this->logger);

        $this->smarty->assign("page_title", "Adminator3 :: Topologie :: Router list");

        $this->header($request, $response, $this->adminator);

        $output = $topology->getRouterList();

        $this->smarty->assign("body", $output);

        $this->smarty->display('topology/router-list.tpl');

        return $response;
    }
}
