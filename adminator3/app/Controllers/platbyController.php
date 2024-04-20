<?php

namespace App\Controllers;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class platbyController extends adminatorController {
    var $conn_mysql;
    var $smarty;

    public function __construct(ContainerInterface $container, $conn_mysql, $smarty, $logger, $auth, $app)
    {
        $this->container = $container;
		$this->conn_mysql = $conn_mysql;
        $this->smarty = $smarty;

        $this->logger = $this->container->logger;        
        $this->logger->info("platbyController\__construct called");
	  }

    public function cat(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
      $this->logger->info("platbyController\cat called");

      $this->checkLevel(92);

      $this->smarty->assign("page_title","Adminator3 :: Platby");

      $this->header($request, $response);
      
      $body .= "Prosím vyberte z podkategorie výše....";

      $this->smarty->assign("body",$body);
      
      $this->smarty->assign("link_a2_platby",fix_link_to_another_adminator("/platby.php"));
      $this->smarty->assign("link_a2_faktury",fix_link_to_another_adminator("/faktury.php"));
      
      $this->smarty->display('platby/platby-cat.tpl');

    }

    public function platby(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {

        $this->logger->info("platbyController\\platby called");
        
        $this->checkLevel();

        $this->smarty->assign("page_title","Adminator3 :: Platby");

        $this->header($request, $response);

        
        return $response;
    }

    public function fn(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {

        $this->logger->info("platbyController\\fn called");
        
        $this->checkLevel(107);

        $this->smarty->assign("page_title","Adminator3 :: Faktury Neuhrazene");

        $this->header($request, $response);

        $this->smarty->assign("body","Prosím vyberte z podkategorie výše....");

        $this->smarty->display('platby/fn.tpl');

        return $response;
    }

    public function fnKontrolaOmezeni(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $this->logger->info("platbyController\\fn called");
        
        $this->checkLevel(149);

        $this->smarty->assign("page_title","Adminator3 :: N.F. :: Kontrola omezeni vs. platby");

        $this->header($request, $response);

        $platby = new \platby($this->conn_mysql, $this->logger);

        $pocet_synchro_faktur = $platby->synchro_db_nf();
 
        $ret = $platby->fn_kontrola_omezeni();
        $dotaz_vlastnici_num = $ret[0];
        $zaznam = $ret[1];

        $this->smarty->assign("nadpis","Kontrola omezení objektu vs. neuhr. fakturám");

        $this->smarty->assign("faktury_pocet",$pocet_synchro_faktur);
        
        $this->smarty->assign("vlastnici_pocet",$dotaz_vlastnici_num);
        
        $this->smarty->assign("pole_data",$zaznam);

        $this->smarty->display('faktury/fn-kontrola-omezeni.tpl');

        return $response;
    }
}