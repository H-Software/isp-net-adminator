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

                $rs_iptv = mysql_query("SELECT id_tarifu, jmeno_tarifu, cena_s_dph FROM tarify_iptv WHERE id_tarifu = '".intval($iptv_sluzba_id_tarifu)."' ");

                while($data_iptv = mysql_fetch_array($rs_iptv)) {

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
            $smarty->assign("file_name", "/".$nazev_souboru);

            //finalni zobrazeni sablony
            $smarty->display('print/smlouva-2012-05.tpl');

        } //konec else !isset nazev
    }
}
