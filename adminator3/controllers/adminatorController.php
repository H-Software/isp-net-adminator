<?php

class adminatorController{

    var $conn_mysql;
    var $smarty;
    var $logger;
    var $auth;
    var $app;
    
    public function __construct($conn_mysql, $smarty, $logger, $auth)
    {
		$this->conn_mysql = $conn_mysql;
        $this->smarty = $smarty;
        $this->logger = $logger;
        $this->auth = $auth;
        
        $this->logger->addInfo("adminatorController\__construct called");
	}

    public function checkLevel($page_level_id){

        $this->container->auth->page_level_id = $page_level_id;

        $checkLevel = $this->container->auth->checkLevel($this->container->logger);
        
        $this->logger->addInfo("adminatorController\checkLevel: checkLevel result: ".var_export($checkLevel, true));

        if($checkLevel === false){

            $this->smarty->assign("page_title","Adminator3 - chybny level");
            $this->smarty->assign("body","<br>Neopravneny pristup /chyba pristupu. STOP <br>");
            $this->smarty->display('index-nolevel.tpl');
            
            exit;
        }
    }

    function header()
    {

        $this->logger->addDebug("adminatorController\\footer called");
        $this->logger->addDebug("adminatorController\\footer: ".$this->auth->user_nick." (".$this->auth->user_level.")");

        $this->smarty->assign("nick_a_level",$this->auth->user_nick." (".$this->auth->user_level.")");
        $this->smarty->assign("login_ip",$_SERVER['REMOTE_ADDR']);

        //kategorie
        $uri=$_SERVER["REQUEST_URI"];
        $uri_replace = str_replace ("adminator3", "", $uri);

        list($kategorie, $kat_2radka, $mapa) = $this->zobraz_kategorie($uri,$uri_replace);

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

        $prihl_uziv = $this->vypis_prihlasene_uziv();

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

    function zobraz_kategorie($uri,$uri_replace)
    {

        $kategorie = array();

        $kategorie[0] = array( "nazev" => "Zákazníci", "url" => "/vlastnici/cat", "align" => "center", "width" => "18%" );

        if( ereg("^.+vlastnici.+",$uri) or ereg("^.+vlastnici/cat+",$uri) or ereg("^.+vypovedi",$uri) )
        { $kategorie[0]["barva"] = "silver"; }

        $kategorie[1] = array( "nazev" => "Služby", "url" => fix_link_to_another_adminator("/objekty-subcat.php"), "align" => "center", "width" => "18%" );

        if( ereg("^.+objekty.",$uri) or ereg("^.+objekty-subcat.php",$uri) )
        { $kategorie[1]["barva"] = "silver"; }

        $kategorie[2] = array( "nazev" => "Platby", "url" => "/platby/cat", "align" => "center", "width" => "18%" );

        if( ereg("^.+platby.+$",$uri) )
        { $kategorie[2]["barva"] = "silver"; }

        $kategorie[3] = array( "nazev" => "Topologie", "url" => fix_link_to_another_adminator("/topology-nod-list.php"), "align" => "center", "width" => "" );

        if( ereg("^.+topology",$uri) )
        { $kategorie[3]["barva"] = "silver"; }

        $kategorie[4] = array( "nazev" => "Nastavení", "url" => "/admin", "align" => "center", "width" => "" );

        if( ereg("^.+admin.+$",$uri_replace ) or ereg("^.+admin-subcat.php$",$uri) )
        {  $kategorie[4]["barva"] = "silver"; }

        $kategorie[5] = array( "nazev" => "Úvodní strana", "url" => "/home", "align" => "center", "width" => "" );
        
        if( ereg("^.+home.php$",$uri) )
        { $kategorie[5]["barva"] = "silver"; }

        $kat_2radka = array();

        $kat_2radka[0] = array( "nazev" => "Partner program", "url" => fix_link_to_another_adminator("/partner/partner-cat.php"), "width" => "", "align" => "center" );

        if( (ereg("partner",$uri_replace) and !ereg("admin",$uri_replace)) )
        { $kat_2radka[0]["barva"] = "silver"; }

        $kat_2radka[1] = array( "nazev" => "Změny", "url" => "/archiv-zmen/cat", "width" => "", "align" => "center" );

        if( ereg("^.+archiv-zmen.+$",$uri) )
        { $kat_2radka[1]["barva"] = "silver"; }

        $kat_2radka[2] = array( "nazev" => "Work", "url" => "/work", "width" => "", "align" => "center" );

        if( ereg("^.+work.+$",$uri) )
        { $kat_2radka[2]["barva"] = "silver"; }

        $kat_2radka[3] = array( "nazev" => "Ostatní", "url" => "/others", "width" => "", "align" => "center" );

        if( ereg("^.+others.+$",$uri) or ereg("^.+syslog.+$",$uri) or ereg("^.+/mail.php$",$uri) or ereg("^.+opravy.+$",$uri) )
        { $kat_2radka[3]["barva"] = "silver"; }

        $kat_2radka[4] = array( "nazev" => "O programu", "url" => "/about", "width" => "", "align" => "center" );

        if( ereg("^.+about.+$",$uri) )
        { $kat_2radka[4]["barva"] = "silver"; }
        
        $ret = array( $kategorie, $kat_2radka);
            
        return $ret;
    }

    function vypis_prihlasene_uziv()
    {
        $ret = array();

        $MSQ_USER2 = $this->conn_mysql->query("SELECT nick, level FROM autorizace");
        $MSQ_USER_COUNT = $MSQ_USER2->num_rows;

        $ret[0] = $MSQ_USER_COUNT;

        //prvne vypisem prihlaseneho
        $MSQ_USER_NICK = $this->conn_mysql->query("SELECT nick, level FROM autorizace WHERE nick LIKE '".$this->conn_mysql->real_escape_string($this->auth->user_nick)."' ");

        if ($MSQ_USER_NICK->num_rows <> 1)
        {
            $ret[100] = true;
            $ret[101] = "Chyba! Vyber nicku nelze provest.";
        }
        else
        {
            while ($data_user_nick = $MSQ_USER_NICK->fetch_array() )
            {
            $ret[1] = $data_user_nick["nick"];
            $ret[2] = $data_user_nick["level"];
            }
        } // konec else

        // ted najilejeme prihlaseny lidi ( vsecky ) do pop-up okna
        if ( $MSQ_USER_COUNT < 1 )
        { $obsah_pop_okna .= "Nikdo nepřihlášen. (divny)"; }
        else
        {

        while ($data_user2 = $MSQ_USER2->fetch_array())
        {
            $obsah_pop_okna .= "jméno: ".$data_user2["nick"].", level: ".$data_user2["level"].", ";
        } //konec while

        $ret[3] = $obsah_pop_okna;

        } // konec if

        return $ret;
    }
}
