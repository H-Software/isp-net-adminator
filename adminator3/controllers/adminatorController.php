<?php

class adminatorController{

    public function __construct($conn_mysql = null, $smarty, $logger, $auth)
    {
		// $this->conn_mysql = $conn_mysql;
        $this->smarty = $smarty;
        $this->logger = $logger;
        $this->auth = $auth;
        
        $this->logger->addInfo("adminatorController\__construct called");
	}

    function header(){

        $this->logger->addDebug("adminatorController\\footer called");
        $this->logger->addDebug("adminatorController\\footer: ".$this->auth->user_nick." (".$this->auth->user_level.")");

        $this->smarty->assign("nick_a_level",$this->auth->user_nick." (".$this->auth->user_level.")");
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

        $prihl_uziv = vypis_prihlasene_uziv($this->auth->user_nick);

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
}