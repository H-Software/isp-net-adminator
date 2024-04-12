<?php

namespace App\Controllers;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class homeController extends adminatorController {
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
    
    public function home(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {            
        $this->logger->addInfo("homeController\home called");

        $this->checkLevel(38);

        $a = new \adminator($this->conn_mysql, $this->smarty, $this->logger, $this->auth);

        if ($request->isPost()) {
            $data = $request->getParsedBody();
            $this->logger->addDebug("homeController\home post data: ".var_export($data, true));    
        }

        $this->smarty->assign("page_title","Adminator3 :: úvodní stránka");

        $this->header($request, $response);

        //vlozeni prihlasovaci historie
        list_logged_users_history($this->conn_mysql, $this->smarty);
        
        //informace z modulu neuhrazené faktury
        //
        $neuhr_faktury_pole = $a->show_stats_faktury_neuhr();
        $this->logger->addInfo("show_stats_faktury_neuhr: result: " . var_export( $neuhr_faktury_pole, true ));

        $this->smarty->assign("d",$neuhr_faktury_pole[0]);

        $this->smarty->assign("count_total", $neuhr_faktury_pole[0]);
        $this->smarty->assign("count_ignored", $neuhr_faktury_pole[1]);
        $this->smarty->assign("count_unknown", $neuhr_faktury_pole[2]);
        $this->smarty->assign("date_last_import", $neuhr_faktury_pole[3]);

        //tady opravy az se dodelaj

        $this->opravy_a_zavady();

        $this->board();

        $this->logger->addInfo("homeController\home: end of rendering");
        $this->smarty->display('home.tpl');

        return $response;
    }

    function board(){
        //generovani zprav z nastenky

        if ($this->container->auth->checkLevel($this->container->logger, 87, false) === true) {
            $this->logger->addInfo("homeController\board allowed");

            $this->smarty->assign("nastenka_povoleno",1);
            $this->smarty->assign("datum",date("j. m. Y"));
            $this->smarty->assign("sid",$this->auth->user_sid);
            
            $nastenka = new \board($this->conn_mysql);

            $nastenka->prepare_vars("");
            
            $nastenka->view_number = 10; //zprávy budou zobrazeny po ...
            
            $zpravy = $nastenka->show_messages();
            
            $this->smarty->assign("zpravy",$zpravy);
            
            $page = $nastenka->show_pages();
            
            $this->smarty->assign("strany",$page);
            
        }
    }

    function opravy_a_zavady(){
        //opravy a zavady vypis
        $pocet_bunek = 11;

        if ($this->container->auth->checkLevel($this->container->logger, 101, false) === true) {
            $this->logger->addInfo("homeController\opravy_a_zavady allowed");

            $v_reseni_filtr = $_GET["v_reseni_filtr"];
            $vyreseno_filtr = $_GET["vyreseno_filtr"];
            $limit = $_GET["limit"];

            if( !isset($v_reseni_filtr) ){ $v_reseni_filtr="99"; }
            if( !isset($vyreseno_filtr) ){ $vyreseno_filtr="0"; }

            if( !isset($limit) ){ $limit="10"; }

            // vypis
            $this->smarty->assign("opravy_povoleno",1);

            $this->smarty->assign("pocet_bunek",$pocet_bunek);
            
            $this->smarty->assign("vyreseno_filtr",$vyreseno_filtr);
            $this->smarty->assign("v_reseni_filtr",$v_reseni_filtr);
            $this->smarty->assign("limit",$limit);
            
            $this->smarty->assign("action",$_SERVER['SCRIPT_URL']);
            
            $opravy = new \opravy($this->conn_mysql, $this->logger);
         
            $rs_vypis = $opravy->vypis_opravy($pocet_bunek);
            // $this->logger->addDebug("homeController\opravy_a_zavady list: result: " . var_export($rs_vypis, true));    

            if($rs_vypis)
            {
                if (strlen($rs_vypis[0]) > 0)
                {
                    // no records in DB
                    $this->logger->addInfo("homeController\opravy_a_zavady list: no records found in database.");    
                    $content_opravy_a_zavady = $rs_vypis[0];
                }
                elseif(strlen($rs_vypis[1]) > 0)
                {
                    // raw html
                    $content_opravy_a_zavady = $rs_vypis[1];
                }
                else{
                    // ??
                    $this->logger->addError("homeController\opravy_a_zavady unexpected return value");
                }
            }
            else{
                $this->logger->addError("homeController\opravy_a_zavady no return value from vypis_opravy call");
            }

            $this->smarty->assign("content_opravy_a_zavady", $content_opravy_a_zavady);
        }
    }
}
