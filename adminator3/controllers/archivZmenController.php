<?php

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class archivZmenController {
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
        
        $this->logger->addInfo("archivZmenController\__construct called");
	}

    public function archivZmenCat(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {

        $this->logger->addInfo("archivZmenController\archivZmenCat called");
        
        $this->auth->check_level(30);

        $this->smarty->assign("body","Prosím vyberte z podkategorie výše....");

        $this->smarty->display('archiv-zmen-cat.tpl');

        return $response;
    }

}