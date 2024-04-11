<?php

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class workController extends adminatorController {
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
        
        $this->logger->addInfo("workController\__construct called");
	}

    public function work(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {

        $this->logger->addInfo("workController\work called");
        
        $this->checkLevel(16);

        $this->smarty->assign("page_title","Adminator3 :: Work");

        $this->header();

        $this->smarty->assign("enable_work",1); //slozeni JS skriptu pro stranku
        $this->smarty->assign("action",$_SERVER['SCRIPT_URL']);
        
        $data_s = "/srv/www/htdocs.ssl/server.remote.log";
        
        /*
        $akce = $_POST["akce"];
        $iptables = $_POST["iptables"];
        $dns = $_POST["dns"];
        $optika = $_POST["optika"];
        
        
         if( $iptables == 1 ){ $prvni=$iptables; $pocet+20; }else{ $prvni = 0; }
         if( $dns == 1 ){ $druha=$dns; $pocet+20; }else{ $druha = 0; }
         if( $optika == 1 ){ $treti=$optika; $pocet+20; }else{ $treti = 0; } 
         if( ( $iptables==0 and $dns==0 and $optika == 0 ) ){ $akce=""; }
        */
            
         // uložení odpovědi v případě vypnutého JavaScriptu
         if( isset($_GET["akce"]) )
         { // nelze pouzi JS/ajax
           echo "neumim AJAX ";
           //mysql_query("UPDATE anketa SET pocet = pocet + 1 WHERE id = " . intval($_GET["anketa"]));
         }
        
         if( ( file_exists ($data_s) ) )
         {
           $fp = fopen($data_s, "r");
           $odpoved_file = fread($fp, filesize ($data_s));
           //echo $data;
           fclose ($fp);
         }
         else
         { $odpoved_file = "\n log soubor neexistuje \n"; }
                 
        $this->smarty->assign("odpoved_file",$odpoved_file); //log ze souboru
             
        $this->smarty->display('work.tpl');

        return $response;
    }

}