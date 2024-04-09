<?php

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class aboutController {
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
        
        $this->logger->addInfo("homeController\__construct called");
	}

    public function about(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {

        $this->logger->addInfo("aboutController\about called");
        
        $this->auth->check_level(1420);

        $this->smarty->assign("page_title","Adminator3 :: O programu");

        $ac = new adminatorController(null, $this->smarty, $this->logger, $this->auth);
        $ac->footer();

        //vlastní obsah

        $this->smarty->assign("body","Prosím vyberte z podkategorie výše....");

        $this->smarty->display('about/about.tpl');

        return $response;
    }
}
