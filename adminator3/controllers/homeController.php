<?php

class homeController {
    var $conn_mysql;
    var $smarty;
    var $logger;
    var $auth;
    var $app;

    function __construct($conn_mysql, $smarty, $logger, $auth, $app) {
		$this->conn_mysql = $conn_mysql;
        $this->smarty = $smarty;
        $this->logger = $logger;
        $this->auth = $auth;
        $this->app = $app;
	}

    function home(){

        $this->logger->addInfo("homeController\home called");

        $this->smarty->assign("page_title","Adminator3 :: úvodní stránka");

        $this->footer();

        //vlozeni prihlasovaci historie
        list_logged_users_history($this->conn_mysql, $this->smarty);

        $this->opravy_a_zavady();

        //informace z modulu neuhrazené faktury
            
        $neuhr_faktury_pole = show_stats_faktury_neuhr();
        $this->logger->addInfo("show_stats_faktury_neuhr: result: " . var_export( $neuhr_faktury_pole, true ));

        $this->smarty->assign("d",$neuhr_faktury_pole[0]);

        $this->smarty->assign("count_total", $neuhr_faktury_pole[0]);
        $this->smarty->assign("count_ignored", $neuhr_faktury_pole[1]);
        $this->smarty->assign("count_unknown", $neuhr_faktury_pole[2]);
        $this->smarty->assign("date_last_import", $neuhr_faktury_pole[3]);

        //tady opravy az se dodelaj
                                                                            
        $this->board();

        $this->logger->addInfo("homeController\home: end of rendering");

        $this->smarty->display('home.tpl');
    }

    function footer(){

        $this->smarty->assign("nick_a_level",$this->auth->nick." (".$this->auth->level.")");
        $this->smarty->assign("login_ip",$_SERVER['REMOTE_ADDR']);

        //kategorie
        $uri=$_SERVER["REQUEST_URI"];
        $uri_replace = str_replace ("adminator3", "", $uri);

        list($kategorie, $kat_2radka, $mapa) = zobraz_kategorie($uri,$uri_replace);

        $this->smarty->assign("kategorie",$kategorie);
        $this->smarty->assign("kat_2radka",$kat_2radka);

        $this->smarty->assign("show_se_cat_values", array("0","1"));
        $this->smarty->assign("show_se_cat_output", array("Nezobr. odkazy","Zobrazit odkazy"));

        $show_se_cat = $_POST["show_se_cat"];

        if( $show_se_cat == 0 )
        { $this->smarty->assign("show_se_cat_selected", "0"); }
        else
        { $this->smarty->assign("show_se_cat_selected", "1"); }

        $this->smarty->assign("show_se_cat",$show_se_cat);

        $se_cat_adminator_link = $_SERVER['HTTP_HOST'];
        $se_cat_adminator_link = str_replace("adminator3", "adminator2", $se_cat_adminator_link);

        $this->smarty->assign("se_cat_adminator","adminator2");
        $this->smarty->assign("se_cat_adminator_link",$se_cat_adminator_link);

        $prihl_uziv = vypis_prihlasene_uziv($nick);

        if( $prihl_uziv[100] == true ){
            $this->smarty->assign("pocet_prihl_uziv",0);
        }
        else{
            $this->smarty->assign("pocet_prihl_uziv",$prihl_uziv[0]);

            $this->smarty->assign("prvni_jmeno",$prihl_uziv[1]);
            $this->smarty->assign("prvni_level",$prihl_uziv[2]);
        }

        //button na vypis vsech prihl. uziv.
        $this->smarty->assign("windowtext2",$prihl_uziv[3]);

        // velikost okna
        $this->smarty->assign("windowdelka2","170");
        $this->smarty->assign("windowpadding2","40");
            
        // pozice okna
        $this->smarty->assign("windowtop2","150");
        $this->smarty->assign("windowleft2","350");

        $this->smarty->assign("subcat_select",0);
    }

    function board(){
        //generovani zprav z nastenky

        if ($this->auth->check_level(87, false) === true) {
            $this->logger->addInfo("homeController\board allowed");

            $this->smarty->assign("nastenka_povoleno",1);
            $this->smarty->assign("datum",date("j. m. Y"));
            $this->smarty->assign("sid",$this->auth->user_sid);
            
            $nastenka = new board($this->conn_mysql);

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
        if ($this->auth->check_level(101,false) === true) {
            $this->logger->addInfo("homeController\opravy_a_zavady allowed");

            $v_reseni_filtr = $_GET["v_reseni_filtr"];
            $vyreseno_filtr = $_GET["vyreseno_filtr"];
            $limit=$_GET["limit"];

            if( !isset($v_reseni_filtr) ){ $v_reseni_filtr="99"; }
            if( !isset($vyreseno_filtr) ){ $vyreseno_filtr="0"; }

            if( !isset($limit) ){ $limit="10"; }

            // vypis
            $this->smarty->assign("opravy_povoleno",0);

            $this->smarty->assign("pocet_bunek",11);
            
            $this->smarty->assign("vyreseno_filtr",$vyreseno_filtr);
            $this->smarty->assign("v_reseni_filtr",$v_reseni_filtr);
            $this->smarty->assign("limit",$limit);
            
            $this->smarty->assign("action",$_SERVER["PHP_SELF"]);
            
            $oprava = new opravy;

            $oprava->vypis_opravy();
            
            // $this->smarty->assign("dotaz_radku",$dotaz_radku);
        }
    }
}
