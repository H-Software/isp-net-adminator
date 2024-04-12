<?php

namespace App\Controllers;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class objektyController extends adminatorController {
    var $conn_mysql;
    var $smarty;
    var $logger;
    var $auth;
    var $app;

    public function __construct(ContainerInterface $container, $conn_mysql, $smarty, $logger, $auth, $app)
    {
        $this->container = $container;
		$this->conn_mysql = $conn_mysql;
        $this->smarty = $smarty;
        $this->logger = $logger;
        $this->auth = $auth;
        $this->app = $app;
        
        $this->logger->addInfo("objektyController\__construct called");
	  }

    public function cat(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
      $this->logger->addInfo("objektyController\cat called");

      $this->checkLevel(93);

      $this->smarty->assign("page_title","Adminator3 :: Objekty");

      $this->header($request, $response);
      
      $this->smarty->assign("body","Prosím vyberte z podkategorie výše....");

      $this->smarty->display('objekty/subcat.tpl');

    }

    public function stb(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {

        $this->logger->addInfo("objektyController\\stb called");
        
        $this->checkLevel(135);

        $this->smarty->assign("page_title","Adminator3 :: Objekty STB");

        $this->header($request, $response);

        $stb = new \stb($this->conn_mysql);

        if ($this->container->auth->checkLevel($this->container->logger, 137, false) === true) {
            $stb->enable_modify_action = true;
        }
        
        if ($this->container->auth->checkLevel($this->container->logger, 152, false) === true) {
            $stb->enable_unpair_action = true;
        }

        $rs = $stb->stbListGetBodyContent();

        $this->smarty->assign("body",$rs[0]);

        $this->smarty->fetch('objekty/stb.tpl');

    }


    public function stbAction(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {

        $this->logger->addInfo("objektyController\\stb called");
        
        $this->checkLevel(136);

        $this->smarty->assign("page_title","Adminator3 :: STB :: upravy");

        $this->header($request, $response);

        $this->smarty->fetch('objekty/stb-action.tpl');


    }

}