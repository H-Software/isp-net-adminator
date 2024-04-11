<?php

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class platbyController extends adminatorController {
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
        
        $this->logger->addInfo("platbyController\__construct called");
	  }

    public function cat(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
      $this->logger->addInfo("platbyController\cat called");

      $this->checkLevel(92);

      $this->smarty->assign("page_title","Adminator3 :: Platby");

      $this->header();
      
      $body .= "Prosím vyberte z podkategorie výše....";

      $this->smarty->assign("body",$body);
      
      $this->smarty->assign("link_a2_platby",fix_link_to_another_adminator("/platby.php"));
      $this->smarty->assign("link_a2_faktury",fix_link_to_another_adminator("/faktury.php"));
      
      $this->smarty->display('platby/platby-cat.tpl');

    }

    public function platby(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {

        $this->logger->addInfo("platbyController\\platby2 called");
        
        $this->checkLevel();

        $this->smarty->assign("page_title","Adminator3 :: Platby");

        $this->header();

        

        return $response;
    }

}