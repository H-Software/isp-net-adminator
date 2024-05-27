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

    protected $settings;

    protected $sentinel;

    protected $adminator;

    protected ServerRequestInterface $request;

    protected ResponseInterface $response;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->conn_mysql = $this->container->get('connMysql');
        $this->smarty = $this->container->get('smarty');
        $this->logger = $this->container->get('logger');
        $this->sentinel = $this->container->get('sentinel');
        $this->settings = $this->container->get('settings');

        $this->logger->info("topologyController\__construct called");

        parent::__construct($container);
    }

    public function nodeList(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $this->logger->info("topologyController\\nodeList called");

        $this->request = $request;
        $this->response = $response;

        if(!$this->checkLevel(5)) {
            return $this->response;
        };

        $topology = new \App\Core\Topology($this->conn_mysql, $this->smarty, $this->logger, $this->settings);

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

        $this->request = $request;
        $this->response = $response;

        if(!$this->checkLevel(85)) {
            return $this->response;
        };

        $topology = new \App\Core\Topology($this->conn_mysql, $this->smarty, $this->logger, $this->settings);

        $this->smarty->assign("page_title", "Adminator3 :: Topologie :: Router list");

        $this->header($request, $response, $this->adminator);

        $output = $topology->getRouterList();

        $this->smarty->assign("body", $output);

        $this->smarty->display('topology/router-list.tpl');

        return $response;
    }
}
