<?php

namespace App\Controllers;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class topologyController extends adminatorController {
    var $conn_mysql;
    var $smarty;
    var $logger;

    public function __construct(ContainerInterface $container, $conn_mysql, $smarty)
    {
        $this->container = $container;
		$this->conn_mysql = $conn_mysql;
        $this->smarty = $smarty;
        // $this->logger = $logger;
        $this->logger = $container->logger;

        $this->logger->addInfo("topologyController\__construct called");
	  }

    public function nodeList(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
      $this->logger->addInfo("topologyController\\nodeList called");

      $this->checkLevel(5);

      $topology = new \App\Core\Topology($this->conn_mysql, $this->smarty, $this->logger);

      $this->smarty->assign("page_title","Adminator3 :: Topologie :: Node list");

      $this->header($request, $response);
      
      $output = $topology->getNodeList();

      $this->smarty->assign("body", $output);

      $this->smarty->display('topology/node-list.tpl');

    }

    public function routerList(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
      $this->logger->addInfo("topologyController\\routerList called");

      $this->checkLevel();

      $topology = new \App\Core\Topology($this->conn_mysql, $this->smarty, $this->logger);

      $this->smarty->assign("page_title","Adminator3 :: Topologie :: Router list");

      $this->header($request, $response);
      
    //   $output = $topology->getNodeList();

    //   $this->smarty->assign("body", $output);

      $this->smarty->display('topology/router-list.tpl');

    }
}
