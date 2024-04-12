<?php

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

      $this->checkLevel();

      $this->smarty->assign("page_title","Adminator3 :: Objekty");

      $this->header($request, $response);
      
      $this->smarty->assign("body","Prosím vyberte z podkategorie výše....");

      $this->smarty->display('objekty/objekty-cat.tpl');

    }

    public function objekty(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {

        $this->logger->addInfo("objektyController\\objekty called");
        
        $this->checkLevel();

        $this->smarty->assign("page_title","Adminator3 :: Objekty");

        $this->header($request, $response);

        return $response;
    }

}