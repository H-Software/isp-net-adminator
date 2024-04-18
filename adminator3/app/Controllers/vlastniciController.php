<?php

namespace App\Controllers;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class vlastniciController extends adminatorController {
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

        $this->logger = $container->logger;
        $this->logger->addInfo("vlastniciController\__construct called");

        $this->adminator = new \App\Core\adminator($this->conn_mysql, $this->smarty, $this->logger);
	  }

    public function cat(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
      $this->logger->addInfo("vlastniciController\cat called");

      $this->checkLevel(90);

      $this->smarty->assign("page_title","Adminator3 :: Zákazníci");

      $this->header($request, $response, $this->adminator);
      
      $this->smarty->assign("body","Prosím vyberte z podkategorie výše....");

      $this->smarty->display('vlastnici/vlastnici-cat.tpl');

    }

    public function vlastnici2(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {

        $this->logger->addInfo("vlastniciController\\vlastnici2 called");
        
        $this->checkLevel(38);

        $this->smarty->assign("page_title","Adminator3 :: Zákazníci");

        $this->header($request, $response, $this->adminator);

        $select = $_GET["select"];

        $vlastnik2 = new \vlastnik2($this->conn_mysql);
        
        $this->smarty->assign("select",$select);
        
        if( $select == 2)
        { $fu_select = "2"; } //Pouze FU
        if( $select == 3 )
        { $fu_select = "1"; } //pouze DU
               
        $fakt_skupiny = $vlastnik2->show_fakt_skupiny($fu_select);
        
        $this->smarty->assign("fakt_skupiny",$fakt_skupiny);
        
        
        $this->smarty->display('vlastnici/vlastnici2.tpl');

        return $response;
    }

    public function fakturacniSkupiny(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {

        $this->logger->addInfo("vlastniciController\\fakturacniSkupiny called");
        
        $this->checkLevel(99);

        $this->smarty->assign("page_title","Adminator3 :: Zákazníci :: fakturační skupiny");

        $this->header($request, $response, $this->adminator);

        // main login
        //
        $fs = new \App\Customer\fakturacniSkupiny($this->conn_mysql);
        $fs_items = $fs->getItems();

        if(empty($fs_items))
        {
            $this->smarty->assign("message_no_items","Nebyly nalezeny žádné fakturační skupiny");
            $this->smarty->display('vlastnici/fakturacni-skupiny.tpl');
            return $response;
        }
        
        $this->smarty->assign("fs_items","<pre>" . var_export($fs_items,true). "</pre>");

        $this->smarty->display('vlastnici/fakturacni-skupiny.tpl');

    }

}