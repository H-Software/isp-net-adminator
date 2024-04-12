<?php

namespace App\Controllers;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class aboutController extends adminatorController {
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
        
        $this->logger->addInfo("aboutController\__construct called");
	}

    public function about(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {

        $this->logger->addInfo("aboutController\about called");
        
        $this->checkLevel(142);

        $this->smarty->assign("page_title","Adminator3 :: O programu");

        $this->header($request, $response);

        $this->smarty->assign("body","Prosím vyberte z podkategorie výše....");

        $this->smarty->display('about/about.tpl');

        return $response;
    }

    public function changesOld(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $this->logger->addInfo("aboutController\changesOld called");
        
        $this->checkLevel(144);

        $this->smarty->assign("page_title","Adminator3 :: O programu :: Staré změny");

        $this->header($request, $response);

        $this->smarty->display('about/about-changes-old.tpl');

        return $response;
    }
    
    public function changes(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $this->logger->addInfo("aboutController\changes called");

        $this->checkLevel(145);

        $this->smarty->assign("page_title","Adminator3 :: O programu :: Změny");

        $this->header($request, $response);
        
        $this->smarty->display('about/about-changes.tpl');

        return $response;
    }
}