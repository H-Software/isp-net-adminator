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
        
        $this->logger->addInfo("aboutController\__construct called");
	}

    private function checkLevel($page_level_id){

        $this->container->auth->page_level_id = $page_level_id;

        $checkLevel = $this->container->auth->checkLevel($this->container->logger);
        
        $this->logger->addInfo("aboutController\checkLevel: checkLevel result: ".var_export($checkLevel, true));

        if($checkLevel === false){

            $this->smarty->assign("page_title","Adminator3 - chybny level");
            $this->smarty->assign("body","<br>Neopravneny pristup /chyba pristupu. STOP <br>");
            $this->smarty->display('index-nolevel.tpl');
            
            exit;
        }
    }

    public function about(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {

        $this->logger->addInfo("aboutController\about called");
        
        $this->checkLevel(142);

        $this->smarty->assign("page_title","Adminator3 :: O programu");

        $ac = new adminatorController($this->conn_mysql, $this->smarty, $this->logger, $this->auth);
        $ac->header();

        $this->smarty->assign("body","Prosím vyberte z podkategorie výše....");

        $this->smarty->display('about/about.tpl');

        return $response;
    }

    public function changesOld(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $this->logger->addInfo("aboutController\changesOld called");
        
        $this->checkLevel(144);

        $this->smarty->assign("page_title","Adminator3 :: O programu :: Staré změny");

        $ac = new adminatorController($this->conn_mysql, $this->smarty, $this->logger, $this->auth);
        $ac->header();

        $this->smarty->display('about/about-changes-old.tpl');

        return $response;
    }
    
    public function changes(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $this->logger->addInfo("aboutController\changes called");

        $this->checkLevel(145);

        $this->smarty->assign("page_title","Adminator3 :: O programu :: Změny");

        $ac = new adminatorController($this->conn_mysql, $this->smarty, $this->logger, $this->auth);
        $ac->header();
        
        $this->smarty->display('about/about-changes.tpl');

        return $response;
    }
}