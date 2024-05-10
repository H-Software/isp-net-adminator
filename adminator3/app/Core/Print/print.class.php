<?php

namespace App\Print;

use Exception;
use App\Core\adminator;
use Psr\Container\ContainerInterface;

class printClass extends adminator
{
    // private $container;

    // private $validator;

    // public $conn_pgsql;
    // public $conn_mysql;

    // public $pdoMysql;

    public $logger;

    // public $loggedUserEmail;

    public $adminator; // handler for instance of adminator class

    public $csrf_html;

    public function __construct(ContainerInterface $container)
    {
        // $this->container = $container;
        // $this->validator = $container->get('validator');
        // $this->conn_mysql = $container->get('connMysql');
        // $this->pdoMysql = $container->get('pdoMysql');

        $this->logger = $container->get('logger');
        $this->smarty = $container->get('smarty');

        // $this->loggedUserEmail = \Cartalyst\Sentinel\Native\Facades\Sentinel::getUser()->email;

        // $this->adminator = new adminator($this->conn_mysql, $this->smarty, $this->logger);

    }

    private function nacti_soubory($find_string)
    {
        $handle = opendir('print/temp/');
        $i = 0;

        while (false !== ($file = readdir($handle))) {
            if ($file != "." && $file != ".." && !is_dir($file) && preg_match('/'.$find_string."/", $file)) {
                $soubor[$i] = "$file";
                $i++;
            }
        }
        closedir($handle);

        sort($soubor);

        return $soubor;
    }

    public function printListAll()
    {
        $this->smarty->assign("action", "/print/redirect");
        $this->smarty->assign("csrf_html", $this->csrf_html);

        $soubor3 = $this->nacti_soubory("smlouva-fiber");
        $this->smarty->assign("soubory_smlouvy_new", $soubor3);

        $soubor4 = $this->nacti_soubory("reg-form-pdf");
        $this->smarty->assign("soubory_regform_new", $soubor4);

        $soubor5 = $this->nacti_soubory("smlouva-v3");
        $this->smarty->assign("soubory_smlouva_v3", $soubor5);

        $soubor6 = $this->nacti_soubory("reg-form-v3");
        $this->smarty->assign("soubory_reg_form_2012_05", $soubor6);


        $this->smarty->display('print/list-all.tpl');
    }

    public function printSmlouva201205()
    {
        $output = "";

        $ec = $_POST["ec"];

        $jmeno = $_POST["jmeno"];
        $nazev_spol = $_POST["nazev_spol"];

        $adresa = $_POST["adresa"];
        $f_adresa = $_POST["f_adresa"];

        $mesto = $_POST["mesto"];
        $f_mesto = $_POST["f_mesto"];

        $cislo_op = $_POST["cislo_op"];

        $ico = $_POST["ico"];
        $dic = $_POST["dic"];

        $kor_adresa = $_POST["kor_adresa"];
        $telefon = $_POST["telefon"];

        $kor_mesto = $_POST["kor_mesto"];
        $email = $_POST["email"];


        //
        //sluzba internet
        //
        $internet_sluzba = $_POST["internet_sluzba"];

        if($internet_sluzba > 0) {
            $int_select_1 = $_POST["int_select_1"];

            //1. tarif
            $int_1_nazev = $_POST["int_1_nazev"];
            $int_1_rychlost = $_POST["int_1_rychlost"];
            $int_1_cena_1 = $_POST["int_1_cena_1"];

            $int_1_vip = $_POST["int_1_vip"];
            $int_1_cena_2 = $_POST["int_1_cena_2"];

            $int_1_adresa = $_POST["int_1_adresa"];

            $int_select_2 = $_POST["int_select_2"];

            //2. tarif

            $int_2_nazev = $_POST["int_2_nazev"];
            $int_2_rychlost = $_POST["int_2_rychlost"];
            $int_2_cena_1 = $_POST["int_2_cena_1"];

            $int_2_vip = $_POST["int_2_vip"];
            $int_2_cena_2 = $_POST["int_2_cena_2"];

            $int_2_adresa = $_POST["int_2_adresa"];
        }

        //
        //sluzba iptv
        //
        $iptv_sluzba = $_POST["iptv_sluzba"];

        $iptv_sluzba_id_tarifu = $_POST["iptv_sluzba_id_tarifu"];

        if($iptv_sluzba == 1) {

            $iptv_tarif_nazev  = $_POST["iptv_tarif_nazev"];
            $iptv_tarif_kanaly = $_POST["iptv_tarif_kanaly"];
            $iptv_tarif_cena   = $_POST["iptv_tarif_cena"];

            if($iptv_sluzba_id_tarifu > 0) {

                $rs_iptv = $this->conn_mysql->query("SELECT id_tarifu, jmeno_tarifu, cena_s_dph FROM tarify_iptv WHERE id_tarifu = '".intval($iptv_sluzba_id_tarifu)."' ");

                while($data_iptv = $this->conn_mysql->fetch_array($rs_iptv)) {

                    if((strlen($iptv_tarif_nazev) == 0)) {
                        $iptv_tarif_nazev = $data_iptv["jmeno_tarifu"];
                    }

                    if((strlen($iptv_tarif_cena) == 0)) {
                        $iptv_tarif_cena = $data_iptv["cena_s_dph"];
                    }
                }

            }

            $iptv_tema_nazev  = $_POST["iptv_tema_nazev"];
            $iptv_tema_kanaly = $_POST["iptv_tema_kanaly"];
            $iptv_tema_cena   = $_POST["iptv_tema_cena"];

            //STB

            $stb = $_POST["stb"];
            $stb_sn = $_POST["stb_sn"];
            $stb_kauce = $_POST["stb_kauce"];

        }

        //sluzba voip
        $voip_sluzba = $_POST["voip_sluzba"];

        if($voip_sluzba > 0) {
            $voip_1_cislo = $_POST["voip_1_cislo"];
            $voip_1_typ = $_POST["voip_1_typ"];

            $voip_2_cislo = $_POST["voip_2_cislo"];
            $voip_2_typ = $_POST["voip_2_typ"];

            if($voip_1_typ == 1) {
                $voip_1_pre = "X";
                $voip_1_post = "";
            } elseif($voip_1_typ == 2) {
                $voip_1_pre = "";
                $voip_1_post = "X";
            } else {
                $voip_1_pre = "";
                $voip_1_post = "";
            }

            if($voip_2_typ == 1) {
                $voip_2_pre = "X";
                $voip_2_post = "";
            } elseif($voip_2_typ == 2) {
                $voip_2_pre = "";
                $voip_2_post = "X";
            } else {
                $voip_2_pre = "";
                $voip_2_post = "";
            }

        }

        $ostatni_nazev = $_POST["ostatni_nazev"];
        $ostatni_cena = $_POST["ostatni_cena"];

        //sleva

        $sleva_select = $_POST["sleva_select"];

        if($sleva_select == 1) {
            $bonus_select_1 = $_POST["bonus_select_1"];
        }

        $bonus_1_tarif = $_POST["bonus_1_tarif"];
        $bonus_1_cena1 = $_POST["bonus_1_cena1"];
        $bonus_1_cena2 = $_POST["bonus_1_cena2"];

        $bonus_2_tarif = $_POST["bonus_2_tarif"];
        $bonus_2_cena1 = $_POST["bonus_2_cena1"];
        $bonus_2_cena2 = $_POST["bonus_2_cena2"];


        $platba_1_od = $_POST["platba_1_od"];
        $platba_1_do = $_POST["platba_1_do"];
        $platba_1_cena = $_POST["platba_1_cena"];
        $platba_1_pozn = $_POST["platba_1_pozn"];

        $platba_2_od = $_POST["platba_2_od"];
        $platba_2_do = $_POST["platba_2_do"];
        $platba_2_cena = $_POST["platba_2_cena"];
        $platba_2_pozn = $_POST["platba_2_pozn"];

        $platba_3_od = $_POST["platba_3_od"];
        $platba_3_do = $_POST["platba_3_do"];
        $platba_3_cena = $_POST["platba_3_cena"];
        $platba_3_pozn = $_POST["platba_3_pozn"];

        $vs = $_POST["vs"];

        $zpusob_placeni = $_POST["zpusob_placeni"];

        $celk_cena = $_POST["celk_cena"];
        $celk_cena_s_dph = $_POST["celk_cena_s_dph"];


        $platba = $_POST["platba"];

        $min_plneni = $_POST["min_plneni"];
        $min_plneni_doba = $_POST["min_plneni_doba"];
        $aut_prodlouzeni = $_POST["aut_prodlouzeni"];

        //$platba = $_POST["platba"];

        //systemove, nebrat
        $odeslano = $_POST["odeslano"];

        #
        #	zacatek stranky pro zobrazeni formu
        #

        if(((strlen($jmeno) < 2) or (!isset($odeslano)))) {

            echo '
                <html>
                <head>
                <link rel="stylesheet" type="text/css" href="/plugins/tigra_calendar/tcal.css" />
                <script type="text/javascript" src="/plugins/tigra_calendar/tcal.js"></script>
                <script type="text/javascript" src="/plugins/tigra_calendar/custom-a3-print.js"></script>
                

                <style>

                .input1{ width: 80px; height: 17px; font-size: 10px; }

                .input-size-big{ font-size: 12px; }

                .label-font { font-size: 13px; }

                .select1 { font-size: 10px;; color: grey; }

                .input-border{ border: 2px solid black; }

                </style>

                </head>

                <body>';

            if ($send != "OK") {
                echo "<p><span style=\"color: blue; font-weight: bold; \"> Pro odeslání formuláře použijte tlačítko OK. </span></p>";
            }

            //
            //  zobrazeni hlavni casti formu
            //
            require __DIR__ . "/inc.smlouva.input.form.2.php";

            echo "</body>
        </html>";

        } // konec if !isset nazev
        else { //budeme generovat

            // konverze promennych
            require(__DIR__ . "/inc.smlouva.gen.prepare.vars.2.php");
            // konec pripravy promennych

            // opravdovy zacatek generovani
            define('FPDF_FONTPATH', "include/font/");

            require(__DIR__ . "/inc.smlouva.gen.main.2.php");

            //zobrazeni odkazu dpdf soubor
            $this->smarty->assign("file_name", "/".$nazev_souboru);

            //finalni zobrazeni sablony
            $this->smarty->display('print/smlouva-2012-05.tpl');

        } //konec else !isset nazev
    }

    public function printRegForm201205Old()
    {

        /*
        $ec = $_POST["ec"];

        $vas_technik = $_POST["vas_technik"];
        $vas_technik_tel = $_POST["vas_technik_tel"];

        $prip_tech = $_POST["prip_tech"];
        $cislo_portu = intval($_POST["cislo_portu"]);
        $poznamka = $_POST["poznamka"];

        //internet
        $int_pocet_zarizeni = intval($_POST["int_pocet_zarizeni"]);

        for($i=1; $i<=$int_pocet_zarizeni; $i++)
        {
            //internet
            $int_zarizeni = "int_zarizeni_".$i;
            $$int_zarizeni = $_POST[$int_zarizeni];

            $int_zarizeni_ip = "int_zarizeni_".$i."_ip";
            $$int_zarizeni_ip = $_POST[$int_zarizeni_ip];

            $int_zarizeni_pozn = "int_zarizeni_".$i."_pozn";
            $$int_zarizeni_pozn = $_POST[$int_zarizeni_pozn];

            $int_zarizeni_vlastnik = "int_zarizeni_".$i."_vlastnik";
            $$int_zarizeni_vlastnik = $_POST[$int_zarizeni_vlastnik];

        }

        $ip_dhcp = $_POST["ip_dhcp"];

        $ip_adresa = $_POST["ip_adresa"];
        $ip_brana = $_POST["ip_brana"];
        $ip_maska = $_POST["ip_maska"];

        $ip_dns1 = $_POST["ip_dns1"];
        $ip_dns2 = $_POST["ip_dns2"];

        //IPTV

        $iptv_pocet_zarizeni = $_POST["iptv_pocet_zarizeni"];

        for($i=1; $i<=$iptv_pocet_zarizeni; $i++)
        {
            //
            $iptv_zarizeni = "iptv_zarizeni_".$i;
            $$iptv_zarizeni = $_POST[$iptv_zarizeni];

            $iptv_zarizeni_ip = "iptv_zarizeni_".$i."_ip";
            $$iptv_zarizeni_ip = $_POST[$iptv_zarizeni_ip];

            $iptv_zarizeni_pozn = "iptv_zarizeni_".$i."_pozn";
            $$iptv_zarizeni_pozn = $_POST[$iptv_zarizeni_pozn];

            $iptv_zarizeni_vlastnik = "iptv_zarizeni_".$i."_vlastnik";
            $$iptv_zarizeni_vlastnik = $_POST[$iptv_zarizeni_vlastnik];

        }

        //VOIP

        $voip_pocet_zarizeni = $_POST["voip_pocet_zarizeni"];

        for($i=1; $i<=$voip_pocet_zarizeni; $i++)
        {
            //
            $voip_zarizeni = "voip_zarizeni_".$i;
            $$voip_zarizeni = $_POST[$voip_zarizeni];

            $voip_zarizeni_ip = "voip_zarizeni_".$i."_ip";
            $$voip_zarizeni_ip = $_POST[$voip_zarizeni_ip];

            $voip_zarizeni_pozn = "voip_zarizeni_".$i."_pozn";
            $$voip_zarizeni_pozn = $_POST[$voip_zarizeni_pozn];

            $voip_zarizeni_vlastnik = "voip_zarizeni_".$i."_vlastnik";
            $$voip_zarizeni_vlastnik = $_POST[$voip_zarizeni_vlastnik];

        }

        //int. zarezeni

        $mat_pocet = $_POST["mat_pocet"];

        for($i=1; $i<=$mat_pocet; $i++)
        {
            $mat = "mat_".$i;
            $$mat = $_POST[$mat];
        }

        $poznamka2 = $_POST["poznamka2"];
        */

        //systemove

        $odeslano = $_POST["odeslano"];

        //
        if((strlen($_GET["id_vlastnika"]) > 0)) {
            $id_objektu = $_GET["id_vlastnika"];
        } else {
            $id_objektu = intval($_POST["id_objektu"]);
        }

        if($id_objektu > 0) {

            echo "<div style=\"color: blue;\">INFO: generování údajů z adminátora ...</div>";

            //prvni check jestli nejde o tunel verejku, ta sama byt nemuze
            $rs_tun = pg_query("SELECT tunnelling_ip FROM objekty WHERE id_komplu = '$id_objektu' ");

            if(pg_fetch_result($rs_tun, 0, 0) == 1) {

                echo "<div style=\"font-weight: bold; color: red;\" >".
                    "Chyba! Nelze vygenerovat formulář, byla zvolena tunelovaná veřejná IP adresa</div>";
            } else {


                $rs_obj = pg_query("SELECT id_cloveka, id_tarifu, port_id, id_nodu, ip, mac, client_ap_ip ".
                        " FROM objekty ".
                        " WHERE id_komplu = '$id_objektu' ");

                $rs_obj_num = pg_num_rows($rs_obj);

                if($rs_obj_num <> 1) {

                    echo "<div style=\"font-weight: bold; color: red;\" >".
                    "Chyba! Nelze načíst údaje z databáze pro id_objektu ".$id_objektu."</div>";
                } else {


                    while($data_obj = pg_fetch_array($rs_obj)) {

                        $id_cloveka 	= $data_obj["id_cloveka"];
                        $id_tarifu 	= $data_obj["id_tarifu"];

                        $cislo_portu_adm = $data_obj["port_id"];
                        $id_nodu 	= $data_obj["id_nodu"];

                        $ip 		= $data_obj["ip"];
                        $mac	 	= $data_obj["mac"];

                        $client_ap_ip	= $data_obj["client_ap_ip"];

                    } //end od while

                    //zjistovani EC (z vlastniku)
                    if($id_cloveka > 0) {

                        $rs_vl = pg_query("SELECT vs FROM vlastnici WHERE id_cloveka = '$id_cloveka' ");
                        $rs_vl_num = pg_num_rows($rs_vl);

                        if($rs_vl_num == 1) {

                            while($data_vl = pg_fetch_array($rs_vl)) {
                                $ec = $data_vl["vs"];
                            }
                        } else {
                            echo "<div style=\"font-weight: bold; color: red;\" >".
                            "Chyba! Nelze načíst údaje z databáze pro id_vlastnika ".$id_cloveka." (rows: ".$rs_vl_num.")</div>";
                        }
                    } else {
                        //objekt nema vlastnika

                        echo "<div style=\"font-weight: bold; color: orange;\" >".
                            "Warning! Nenalezen vlastník objektu, EČ nelze vygenerovat</div>";

                    }

                    //zjistovani typu optika/wifi (z tarifu)

                    $rs_tarif = $this->conn_mysql->query("SELECT typ_tarifu FROM tarify_int WHERE id_tarifu = '$id_tarifu' ");
                    $typ_tarifu = mysql_result($rs_tarif, 0, 0);

                    if($typ_tarifu == 0) {
                        $prip_tech = 3;
                    } else {
                        //optika
                        $prip_tech = 1;

                        //je aktualni cislo portu
                        $cislo_portu = $cislo_portu_adm;
                    }

                    //zjistovani pole POZNAMKA, z vypisu nodu
                    $rs_nod = $this->conn_mysql->query("SELECT jmeno, ip_rozsah FROM nod_list WHERE id = '".intval($id_nodu)."' ");
                    $rs_nod_num = mysql_num_rows($rs_nod);

                    if($rs_nod_num <> 1) {
                        echo "<div style=\"font-weight: bold; color: red;\" >".
                            "Chyba! Nelze načíst údaje z databáze lokalit pro id_nodu ".intval($id_nodu).". (rows: ".$rs_nod_num.")</div>";
                    } else {

                        while($data_nod = mysql_fetch_array($rs_nod)) {

                            $poznamka = " NOD: ".mysql_result($rs_nod, 0, 0);
                            $ip_rozsah = mysql_result($rs_nod, 0, 1);
                        }
                    }

                    //typ ip adresy (dhcp or not)
                    if($typ_tarifu == 1) {
                        $ip_dhcp = 1;
                    }

                    //ip adresa, maska, brana, DNS
                    $ip_adresa = $ip;

                    $ip_arr = explode(".", $ip);

                    if($ip_arr[0] == "10") {
                        //lokálky

                        if(($ip_arr[1] < 50) or ($ip_arr[1] == 88)) {

                            $ip_maska = "255.255.0.0";
                            $ip_brana = $ip_arr[0].".".$ip_arr[1].".1.1";
                        } elseif($ip_arr[1] < 100) {

                            //wifi - C rozsah
                            $ip_maska = "255.255.255.0";
                            $ip_brana = $ip_arr[0].".".$ip_arr[1].".".$ip_arr[2].".1";
                        } else {

                            //asi jen optika
                            $ip_maska = "255.255.252.0";
                            $ip_roz_arr = explode(".", $ip_rozsah);

                            $ip_brana = "10.136.".$ip_roz_arr[2].".1";
                        }
                    } elseif($ip_arr[0] == "212") {
                        //verejky - wifi - obecně

                        $ip_maska = "255.255.255.252";
                        $ip_brana = $ip_arr[0].".".$ip_arr[1].".".$ip_arr[2].".".($ip_arr[3] + 1);
                    } elseif($ip_arr[0] == "82") {
                        //verejky optika
                        $ip_maska = "255.255.255.224";
                        $ip_brana = $ip_arr[0].".".$ip_arr[1].".".$ip_arr[2].".1";

                    } else {

                        //neco jinyho - neumim
                    }

                    $ip_dns1 = "10.3.1.1";
                    $ip_dns2 = "212.80.66.7";


                    //INET zarizeni

                    //wimax
                    if($ip_arr[1] == 88) {

                        $int_pocet_zarizeni = 2;
                        $int_zarizeni_1 = "Alvarion BreezeMAX  3,5G SU - wimax";

                        $int_zarizeni_1_vlastnik = "1";

                    }

                    //optika
                    if($typ_tarifu == 1) {

                        $int_pocet_zarizeni = 1;

                        $int_zarizeni_1 = "PC/ROUTER";
                        $int_zarizeni_1_ip = $mac;
                    }

                    //bezdrat
                    if($typ_tarifu == 0) {

                        if((strlen($client_ap_ip) > 5)) {

                            $int_pocet_zarizeni = 2;

                            $int_zarizeni_2 = "klientské zařízení";
                            $int_zarizeni_2_ip = $client_ap_ip;
                        }

                    }

                    if((!isset($int_pocet_zarizeni)) or ($int_pocet_zarizeni == 0)) {
                        $int_pocet_zarizeni = 1;
                    }

                    //zjisteni zda vlastnik ma jeste tunel. verejku
                    $rs_tunel = pg_query("SELECT ip, tunnel_user, tunnel_pass ".
                            "FROM objekty ".
                            "WHERE ((id_cloveka = '$id_cloveka') ".
                            "	    AND ".
                            "	(id_komplu != '$id_objektu') ".
                            "	    AND ".
                            "	(tunnelling_ip = 1) ) ");

                    $rs_tunel_num = pg_num_rows($rs_tunel);

                    if($rs_tunel_num == 1) {

                        while($data_tunel = pg_fetch_array($rs_tunel)) {

                            $tunnel_user = $data_tunel["tunnel_user"];
                            $tunnel_pass = $data_tunel["tunnel_pass"];

                            $ip_vip = $data_tunel["ip"];
                        }

                        $int_pocet_zarizeni = ($int_pocet_zarizeni + 1);
                        $int_tunel = "int_zarizeni_".$int_pocet_zarizeni;
                        $int_tunel_ip = "int_zarizeni_".$int_pocet_zarizeni."_ip";

                        $$int_tunel = "veřejná IP adresa (tunel) ";
                        $$int_tunel_ip = $ip_vip;

                        $int_pocet_zarizeni = ($int_pocet_zarizeni + 1);
                        $int_tunel = "int_zarizeni_".$int_pocet_zarizeni;
                        $int_tunel_ip = "int_zarizeni_".$int_pocet_zarizeni."_ip";

                        $$int_tunel = " --> uživ. jméno: ".$tunnel_user.", ".
                                "uživ. heslo: ".$tunnel_pass;

                        //$int_zarizeni_2 = $int_tunel." - vip-ka";
                    }



                } //end else if rs_obj_num <> !

            } //konec if else tunelling_ip

        } //end if id_objektu > 0

        $id_stb = intval($_GET["id_stb"]);

        if(($id_stb > 0) or ($id_cloveka > 0)) {


            if($id_stb > 0) {

                echo "<div style=\"color: blue;\">INFO: generování údajů z adminátora ...</div>";

                /*
            //check zda-li nema clovek vice STB-ů
            $rs_1 = mysql_query("SELECT id_cloveka FROM objekty_stb WHERE id_stb = '$id_stb' ");

            $rs_1_vl = mysql_result($rs_1, 0, 0);

            if( $rs_1_vl > 0){
                //STB ma vlasnika, cili muzem hledal dalsi
                $rs_2 = mysql_query("SELECT id_stb FROM objekty_stb WHERE ( (id_cloveka = '$rs_1_vl') and (id_stb != '$id_stb') ) ");
                $rs_2_num = mysql_num_rows($rs_2);

                if( $rs_2_num > 1){
                //2 a více dalších ST

                $sql_where = " ( ";

                while( $data_2 = mysql_fetch_rows($rs_2) ){
                    $sql_where .= " (id_stb = '".$data_2["id_stb"]."') AND ";
                }

                $sql_where = " (id_stb = '$id_stb') ) ";
                }
                elseif($rs_2_num == 1){
                //jeden dalsi STB

                }
                else{
                //zadny dalsi STB

                }

            }
            else
            */
                {

                    $sql_where = " id_stb = '$id_stb' ";
                }
            } else {
                $sql_where = " id_cloveka = '$id_cloveka' ";
            }
            //zjistovani stb
            $rs_stb = $this->conn_mysql->query("SELECT ip_adresa, mac_adresa, puk, popis FROM objekty_stb WHERE ".$sql_where);
            $rs_stb_num = $rs_stb->num_rows;

            if($rs_stb_num > 3) {

                $iptv_pocet_zarizeni = 1;
                $iptv_zarizeni_1 = "více set-top-boxů";

            } elseif($rs_stb_num == 0) {

                //$iptv_pocet_zarizeni = 3;
            } else {
                //Stb se do chlívků vejdou

                $iptv_pocet_zarizeni = $rs_stb_num;

                $i = 1;

                while($data_stb = $rs_stb->fetch_array()) {

                    $iptv_zarizeni = "iptv_zarizeni_".$i;
                    $iptv_zarizeni_ip = "iptv_zarizeni_".$i."_ip";
                    $iptv_zarizeni_pozn = "iptv_zarizeni_".$i."_pozn";

                    $iptv_zarizeni_vlastnik = "iptv_zarizeni_".$i."_vlastnik";

                    $$iptv_zarizeni = "STB - ".$data_stb["popis"];

                    $$iptv_zarizeni_ip = /* $data_stb["ip_adresa"].", ".*/ $data_stb["mac_adresa"];
                    $$iptv_zarizeni_pozn = "PUK: ".$data_stb["puk"];

                    $$iptv_zarizeni_vlastnik = 1;

                    $i++;
                }

            }


        } //konec if gener. STB

        //zjistovani VOIP

        //coming soon :-)

        #
        #       zacatek stranky pro zobrazeni formu
        #

        if(!isset($odeslano)) {

            echo '<html>

        <head>
        
            <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" >

            <meta http-equiv="Cache-Control" content="must-revalidate, no-cache, post-check=0, pre-check=0" > 
            <meta http-equiv="Pragma" content="public" > 
                
            <meta http-equiv="Cache-Control" content="no-cache" > 
            <meta http-equiv="Pragma" content="no-cache" > 
            <meta http-equiv="Expires" content="-1" > 
            
        <title>Průvodce tiskem registračního formuláře</title>

        <style>

        .input1{ width: 80px; height: 17px; font-size: 10px; }

        .input-size-big{ font-size: 12px; }

        .label-font { font-size: 13px; }

        .select1 { font-size: 10px;; color: grey; }

        .input-border{ border: 2px solid black; }

        </style>

        </head>

        <body>';

            if($send != "OK") {
                echo "<p><span style=\"color: blue; font-weight: bold; \"> Pro odeslání formuláře použijte tlačítko OK. </span></p>";
            }

            //
            //  zobrazeni hlavni casti formu
            //
            require("inc.reg.form.input.form.2.php");

            echo "</body>
        </html>";

        } // konec if !isset nazev
        else { //budeme generovat

            // konverze promennych
            require("inc.reg.form.gen.prepare.vars.2.php");
            // konec pripravy promennych

            // opravdovy zacatek generovani
            define('FPDF_FONTPATH', "include/font/");

            require("inc.reg.form.gen.main.2.php");

            //presmerovani na dpdf soubor

            // echo '<html>
            //     <head>
            //         <title>Tisk Registračního formuláře</title>
            //     </head>
            // <body>
            //     Vygenerovany soubor je <a href="'" >zde</a>.
            // </body>
            // </html>';

            $this->smarty->assign("file_name", '/'.$nazev_souboru);

            //finalni zobrazeni sablony
            $this->smarty->display('others/print-reg-form-2012-05.tpl');

        } //konec else !isset nazev

    }
}
