<?php

namespace App\Controllers;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class topologyController extends adminatorController
{
    public \mysqli|\PDO $conn_mysql;

    public $smarty;

    public $logger;

    protected $settings;

    // protected $sentinel;

    // protected $adminator;

    protected ServerRequestInterface $request;

    protected ResponseInterface $response;

    public function __construct(ContainerInterface $container)
    {
        // $this->container = $container;
        $this->conn_mysql = $container->get('connMysql');
        $this->smarty = $container->get('smarty');
        $this->logger = $container->get('logger');
        // $this->sentinel = $container->get('sentinel');
        $this->settings = $container->get('settings');

        $this->logger->info(__CLASS__ . "\\" . __FUNCTION__ . " called");

        parent::__construct($container);
    }

    public function nodeList(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $this->logger->info(__CLASS__ . "\\" . __FUNCTION__ . " called");

        $this->request = $request;
        $this->response = $response;

        if(!$this->checkLevel(5)) {
            return $this->response;
        };

        $topology = new \App\Core\Topology($this->conn_mysql, $this->smarty, $this->logger, $this->settings);
        $output = $topology->getNodeList();

        $assignData = [
            "page_title" => "Adminator3 :: Topologie :: Node list",
            "body" => $output
        ];

        return $this->renderer->template($request, $response, 'topology/node-list.tpl', $assignData);
    }

    public function routerList(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $this->logger->info(__CLASS__ . "\\" . __FUNCTION__ . " called");

        $this->request = $request;
        $this->response = $response;

        if(!$this->checkLevel(85)) {
            return $this->response;
        };

        $topology = new \App\Core\Topology($this->conn_mysql, $this->smarty, $this->logger, $this->settings);
        $output = $topology->getRouterList();

        $assignData = [
            "page_title" => "Adminator3 :: Topologie :: Router list",
            "body" => $output
        ];

        return $this->renderer->template($request, $response, 'topology/router-list.tpl', $assignData);
    }

    public function routerAction(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $this->logger->info(__CLASS__ . "\\" . __FUNCTION__ . " called");

    }
}
