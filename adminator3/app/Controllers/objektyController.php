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

    var $adminator;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
		$this->conn_mysql = $container->connMysql;
        $this->smarty = $container->smarty;
        
        $this->logger = $container->logger;
        $this->logger->info("objektyController\__construct called");

        $this->adminator = new \App\Core\adminator($this->conn_mysql, $this->smarty, $this->logger);
	}

    public function cat(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
      $this->logger->info("objektyController\cat called");

      $this->checkLevel(93, $this->adminator);

      $this->smarty->assign("page_title","Adminator3 :: Objekty");

      $this->header($request, $response, $this->adminator);
      
      $this->smarty->assign("body","Prosím vyberte z podkategorie výše....");

      $this->smarty->display('objekty/subcat.tpl');

    }

    public function stb(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {

        $csrf_html = $this->generateCsrfToken($request, $response, true);
        $stb = new \App\Core\stb($this->container);
        $stb->csrf_html = $csrf_html[0];

        $this->logger->info("objektyController\\stb called");
        
        $this->checkLevel(135, $this->adminator);

        $this->smarty->assign("page_title","Adminator3 :: Objekty STB");

        $this->header($request, $response, $this->adminator);

        if ($this->adminator->checkLevel(137, false) === true) {
            $stb->enable_modify_action = true;
        }
        
        if ($this->adminator->checkLevel(152, false) === true) {
            $stb->enable_unpair_action = true;
        }

        $rs = $stb->stbListGetBodyContent();

        $this->smarty->assign("body",$rs[0]);

        $this->smarty->display('objekty/stb.tpl');

    }


    public function stbAction(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {

        $this->logger->info("objektyController\\stbAction called");
        
        $this->checkLevel(136, $this->adminator);

        $this->smarty->assign("page_title","Adminator3 :: STB :: Actions");

        $this->header($request, $response, $this->adminator);

        $stb = new \App\Core\stb($this->container);

        $csrf = $this->generateCsrfToken($request, $response, false);

        $rs = $stb->stbAction($request, $response, $csrf);

        if (isset($rs[1])){
            // view form
            $this->smarty->assign($rs[0]);

            $this->smarty->display($rs[1]);
        }
        else{
            // result view, ..
            $this->smarty->assign("body", $rs[0]);
            $this->smarty->display('objekty/stb-action.tpl');
        }

    }

}