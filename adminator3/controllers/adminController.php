<?php

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class adminController extends adminatorController {
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
        
        $this->logger->addInfo("adminController\__construct called");
	}

    public function admin(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {

        $this->logger->addInfo("adminController\admin called");
        
        $this->checkLevel();

        $this->smarty->assign("page_title","Adminator3 :: admin");

        $this->header();

        return $response;
    }

}