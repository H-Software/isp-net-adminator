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

        $csrf_html = $this->generateCsrfToken($request, $response, true);
        $stb = new \App\Core\stb($this->conn_mysql,$this->logger);
        $stb->csrf_html = $csrf_html[0];

        $this->logger->addInfo("objektyController\\stb called");
        
        $this->checkLevel(135);

        $this->smarty->assign("page_title","Adminator3 :: Objekty STB");

        $this->header($request, $response);

        if ($this->container->auth->checkLevel($this->container->logger, 137, false) === true) {
            $stb->enable_modify_action = true;
        }
        
        if ($this->container->auth->checkLevel($this->container->logger, 152, false) === true) {
            $stb->enable_unpair_action = true;
        }

        $rs = $stb->stbListGetBodyContent();

        $this->smarty->assign("body",$rs[0]);

        $this->smarty->display('objekty/stb.tpl');

    }


    public function stbAction(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {

        $this->logger->addInfo("objektyController\\stbAction called");
        
        $this->checkLevel(136);

        $this->smarty->assign("page_title","Adminator3 :: STB :: Actions");

        $this->header($request, $response);

        $stb = new \App\Core\stb($this->conn_mysql, $this->logger);

        $csrf = $this->generateCsrfToken($request, $response, false);

        $rs = $stb->stbAction($request, $response, $csrf);

        if (isset($rs[1])){
            // view form
            $this->smarty->assign($rs[0]);

            try {
                $this->smarty->display($rs[1]);
            }
            catch (Exception $e) {
                $this->logger->addError("objektyController\\stbAction: smarty display failed: " . var_export($e->getMessage(), true));
            }

        }
        else{
            // result view, ..
            $this->smarty->assign("body", $rs[0]);
            $this->smarty->display('objekty/stb-action.tpl');
        }

    }

}