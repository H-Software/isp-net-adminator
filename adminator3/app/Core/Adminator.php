<?php

namespace App\Core;

use App\Models\User;
use App\Models\PageLevel;

class adminator {
    var $conn_mysql;
    var $smarty;
    var $logger;
    
    var $userIdentityUsername;

    var $page_level_id;

    var $userIdentityLevel;

    var $loggedUserEmail;

    public function __construct($conn_mysql, $smarty, $logger)
    {
        $this->conn_mysql = $conn_mysql;
        $this->smarty = $smarty;
        $this->logger = $logger;

        $this->logger->addInfo("adminator\__construct called");
    }

    public function formInit()
    {
        // bootstrap -> bootstrap.js
        // hush -> no echoing stuff -> https://github.com/formr/formr/issues/87#issuecomment-769374921
        return new \Formr\Formr('bootstrap5', 'hush');
    }

    function objectToArray($data)
    {
        $result = [];
        foreach ($data as $key => $value)
        {
            $result[$key] = (is_array($value) || is_object($value)) ? $this->objectToArray($value) : $value;
        }
        return $result;
    }

    // public function getUserEmail()
    // {
    //     $rs = User::find(isset($_SESSION['user']) ? $_SESSION['user'] : 0, ['email']);
    //     $a = $rs->toArray();
	// 	return $a['email'];
    // }

    public function getUserLevel()
	{
		$rs = User::where(
                            "username",
                            isset($this->userIdentityUsername) ? $this->userIdentityUsername : 0
                        )->first(['level']);
        if(is_object($rs))
        {
            // $this->logger->addInfo("adminator\getUserLevel dump db: " . var_export($rs, true));
            $a = $rs->toArray();
            return $a['level'];
        }
        else{
            return false;
        }
	}

	function checkLevel($page_level_id_custom = 0, $display_no_level_page = true)
    {

        // co mame
        // v promeny level mame level prihlaseneho uzivatele
        // databazi levelu pro jednotlivy stranky

        // co chceme
        // porovnat level uzivatele s prislusnym levelem
        // stranky podle jejiho id

        $this->userIdentityLevel = $this->getUserLevel();

        $this->logger->addInfo("adminator\check_level: called with
                                    [page_level_id_custom => " . $page_level_id_custom
                                    . ", page_level_id => " . $this->page_level_id
                                    . ", user_name => " . $this->userIdentityUsername
                                    . ", user_level => " . $this->userIdentityLevel
                                    . "]");

        if(intval($page_level_id_custom) > 0){
            $pl = $page_level_id_custom;
        }
        else{
            $pl = $this->page_level_id;
        }

        $page_level_rs = $this->find_page_level($this->logger,$pl);
        if($page_level_rs === false or !is_int($page_level_rs)){
            $rs = false;
        }
        elseif($this->userIdentityLevel >= $page_level_rs){
            $rs = true; 
        }
        else{
            $rs = false;
        }

        $this->logger->addInfo("adminator\check_level: find_page_level: pl_id: " . $pl . ", level: " . var_export($page_level_rs, true));
        $this->logger->addInfo("adminator\check_level: result: " . var_export($rs, true));

        if( $rs === false) {
            // user nema potrebny level
			return false;
        }
        else{
            return true;
        }
    }

	function find_page_level($logger,$page_id)
    {

        $page_level = 0;

        $rs = PageLevel::find(isset($page_id) ? $page_id : 0, ['level']);
		if(is_object($rs))
        {
            $a = $rs->toArray();
            $page_level = $a['level'];
        }

        $this->logger->addInfo("adminator\\find_page_level: find result: " . var_export($page_level, true));

        if($page_level > 0){
            return $page_level;
        }
        else{
            return false;
        }
    }

    public function getTarifIptvListForForm($show_zero_value = true)
    {

        $this->logger->addInfo("adminator\getTarifIptvListForForm called");

        if($show_zero_value === true)
        {
            $tarifs[0] = "Není vybráno";
        }

        $q = $this->conn_mysql->query("SELECT id_tarifu, jmeno_tarifu FROM tarify_iptv ORDER by jmeno_tarifu ASC");
    
        $num_rows = $q->num_rows;
        
        if($num_rows < 1)
        {
        $tarifs[0] =  "nelze zjistit / žádný tarif nenalezen";
        return $tarifs;
        }
        
        while( $data = $q->fetch_array())
        {
        $tarifs[$data['id_tarifu']] = $data["jmeno_tarifu"];    
        }

        return $tarifs;
    }

    function zobraz_kategorie($uri,$uri_replace)
    {

        $kategorie = array();

        $kategorie[0] = array( "nazev" => "Zákazníci", "url" => "/vlastnici/cat", "align" => "center", "width" => "18%" );

        // if( ereg("^.+vlastnici.+",$uri) or ereg("^.+vlastnici/cat+",$uri) or ereg("^.+vypovedi",$uri) )
        // { $kategorie[0]["barva"] = "silver"; }

        $kategorie[1] = array( "nazev" => "Služby", "url" => "/objekty/cat", "align" => "center", "width" => "18%" );

        // if( ereg("^.+objekty.",$uri) )
        // { $kategorie[1]["barva"] = "silver"; }

        $kategorie[2] = array( "nazev" => "Platby", "url" => "/platby/cat", "align" => "center", "width" => "18%" );

        // if( ereg("^.+platby.+$",$uri) )
        // { $kategorie[2]["barva"] = "silver"; }

        $kategorie[3] = array( "nazev" => "Topologie", "url" => "/topology", "align" => "center", "width" => "" );

        // if( ereg("^.+topology",$uri) )
        // { $kategorie[3]["barva"] = "silver"; }

        $kategorie[4] = array( "nazev" => "Nastavení", "url" => "/admin", "align" => "center", "width" => "" );

        // if( ereg("^.+admin.+$",$uri_replace ) )
        // {  $kategorie[4]["barva"] = "silver"; }

        $kategorie[5] = array( "nazev" => "Úvodní strana", "url" => "/home", "align" => "center", "width" => "" );
        
        // if( ereg("^.+home.php$",$uri) )
        // { $kategorie[5]["barva"] = "silver"; }

        $kat_2radka = array();

        $kat_2radka[0] = array( "nazev" => "Partner program", "url" => fix_link_to_another_adminator("/partner/partner-cat.php"), "width" => "", "align" => "center" );

        // if( (ereg("partner",$uri_replace) and !ereg("admin",$uri_replace)) )
        // { $kat_2radka[0]["barva"] = "silver"; }

        $kat_2radka[1] = array( "nazev" => "Změny", "url" => "/archiv-zmen/cat", "width" => "", "align" => "center" );

        // if( ereg("^.+archiv-zmen.+$",$uri) )
        // { $kat_2radka[1]["barva"] = "silver"; }

        $kat_2radka[2] = array( "nazev" => "Work", "url" => "/work", "width" => "", "align" => "center" );

        // if( ereg("^.+work.+$",$uri) )
        // { $kat_2radka[2]["barva"] = "silver"; }

        $kat_2radka[3] = array( "nazev" => "Ostatní", "url" => "/others", "width" => "", "align" => "center" );

        // if( ereg("^.+others.+$",$uri) or ereg("^.+syslog.+$",$uri) or ereg("^.+/mail.php$",$uri) or ereg("^.+opravy.+$",$uri) )
        // { $kat_2radka[3]["barva"] = "silver"; }

        $kat_2radka[4] = array( "nazev" => "O programu", "url" => "/about", "width" => "", "align" => "center" );

        // if( ereg("^.+about.+$",$uri) )
        // { $kat_2radka[4]["barva"] = "silver"; }
        
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
        $MSQ_USER_NICK = $this->conn_mysql->query("SELECT nick, level FROM autorizace WHERE nick LIKE '" . $this->userIdentityUsername . "' ");

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
        // if ( $MSQ_USER_COUNT < 1 )
        // { $obsah_pop_okna .= "Nikdo nepřihlášen. (divny)"; }
        // else
        // {

        //     while ($data_user2 = $MSQ_USER2->fetch_array())
        //     {
        //         $obsah_pop_okna .= "jméno: ".$data_user2["nick"].", level: ".$data_user2["level"].", ";
        //     } //konec while

        //     $ret[3] = $obsah_pop_okna;

        // } // konec if

        return $ret;
    }

    function show_stats_faktury_neuhr()
    {
        //
        // vypis neuhrazenych faktur
        //
        // return hodnoty
        //
        // 0. neuhr. faktur celkem
        // 1. nf ignorovane
        // 2. nf nesparovane
        // 3. datum posl. importu
        
        $ret = array();

        try {
            $dotaz_fn = $this->conn_mysql->query("SELECT * FROM faktury_neuhrazene");
            $dotaz_fn_radku = $dotaz_fn->num_rows;
            $ret[0] = $dotaz_fn_radku;
        } catch (Exception $e) {
            die (init_helper_base_html("adminator3") . "<h2 style=\"color: red; \">Error: Database query failed! Caught exception: " . $e->getMessage() . "\n" . "</h2></body></html>\n");
        }

        try {
            $dotaz_fn4 = $this->conn_mysql->query("SELECT * FROM faktury_neuhrazene WHERE ( ignorovat = '1' ) order by id");
            $dotaz_fn4_radku = $dotaz_fn4->num_rows;
            $ret[1] = $dotaz_fn4_radku;
        } catch (Exception $e) {
            die (init_helper_base_html("adminator3") . "<h2 style=\"color: red; \">Error: Database query failed! Caught exception: " . $e->getMessage() . "\n" . "</h2></body></html>\n");
        }

        try {
            $dotaz_fn2 = $this->conn_mysql->query("SELECT * FROM faktury_neuhrazene WHERE par_id_vlastnika = '0' ");
            $dotaz_fn2_radku = $dotaz_fn2->num_rows;
            $ret[2] = $dotaz_fn2_radku;
        } catch (Exception $e) {
            die (init_helper_base_html("adminator3") . "<h2 style=\"color: red; \">Error: Database query failed! Caught exception: " . $e->getMessage() . "\n" . "</h2></body></html>\n");
        }

        try {
            $dotaz_fn3 = $this->conn_mysql->query("SELECT datum,DATE_FORMAT(datum, '%d.%m.%Y %H:%i:%s') as datum FROM fn_import_log order by id");
            $dotaz_fn3_radku = $dotaz_fn3->num_rows;
        } catch (Exception $e) {
            die (init_helper_base_html("adminator3") . "<h2 style=\"color: red; \">Error: Database query failed! Caught exception: " . $e->getMessage() . "\n" . "</h2></body></html>\n");
        }

         while( $data3=$dotaz_fn3->fetch_array() )
         { $datum_fn3=$data3["datum"]; }
        
         if(strlen($datum_fn3) > 0){
            $ret[3] = $datum_fn3;
         } else{
            $ret[3] = "Unknown";
         }
         
        return $ret;
    }
}