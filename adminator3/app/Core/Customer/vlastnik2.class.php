<?php

use Psr\Container\ContainerInterface;
use Illuminate\Database\Capsule\Manager as DB;

class vlastnik2
{
    public $conn_mysql;

    public $conn_pgsql;

    public $logger;

    public $container; // for calling stb class over vlastnik2_a2 class
    public $adminator; // handler for instance of adminator class

    public $alert_type;

    public $alert_content;

    public $csrf_html;

    public $listItemsContent;

    public $listMode; // original local variable "co"

    public $listSql; // original local variable "sql"

    public $istFindId;

    public $dotaz_source;

    public $objektListAllowedActionUpdate = false;

    public $objektListAllowedActionErase = false;

    public $objektListAllowedActionGarant = false;

    public $vlastnikAllowedUnassignObject = false;

    public $objektStbListAllowedActionUpdate = false;
    public $objektStbListAllowedActionErase = false;

    public $vlastnici_erase_povolen = false;

    public $vlastnici_update_povolen = false;

    public $form_find;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->conn_mysql = $container->get('connMysql');
        $this->conn_pgsql = $container->get('connPgsql');
        $this->logger = $container->get('logger');

        $this->adminator = new \App\Core\adminator($this->conn_mysql, $this->container->get('smarty'), $this->logger);
    }

    private function listPrepareVars($vlastnik)
    {
        // perms for actions/links
        //
        if ($this->adminator->checkLevel(63, false) === true) {
            $vlastnik->export_povolen = true;
        }

        //promenne pro akce objektu
        if ($this->adminator->checkLevel(29, false) === true) {
            $this->objektListAllowedActionUpdate = true;
        }
        if ($this->adminator->checkLevel(33, false) === true) {
            $this->objektListAllowedActionErase = true;
        }
        if ($this->adminator->checkLevel(34, false) === true) {
            $this->objektListAllowedActionGarant = true;
        }

        // akce objekty STB
        if ($this->adminator->checkLevel(137, false) === true) {
            $this->objektStbListAllowedActionUpdate = true;
        }
        if ($this->adminator->checkLevel(310, false) === true) {
            $this->objektStbListAllowedActionErase = true;
        }

        // promeny pro mazani, zmenu vlastniku
        if ($this->adminator->checkLevel(45, false) === true) {
            $this->vlastnici_erase_povolen = true;
        }

        if ($this->adminator->checkLevel(34, false) === true) {
            $this->vlastnici_update_povolen = true;
        }

        // // odendani objektu od vlastnika
        if ($this->adminator->checkLevel(49, false) === true) {
            $this->vlastnikAllowedUnassignObject = true;
        }

        $find_id = $_GET["find_id"];
        $find    = $this->form_find;

        // $delka_find_id=strlen($find_id);
        if((strlen($find_id) > 0)) {
            $this->listMode = 3;
            /* hledani podle id_cloveka */
            $sql = intval($find_id);
        } elseif((strlen($find) > 0)) {
            $this->listMode = 1;
            /* hledani podle cehokoli */
            $sql = $find;
        } else { /* cokoli dalsiho */
        }

        if($this->listMode == 1) {
            if($sql != "%") {
                $sql = "%".$sql."%";
            }

            $select1 = " WHERE (firma is not NULL) AND ( archiv = 0 or archiv is null ) AND ";
            $select1 .= " ( nick LIKE '$sql' OR jmeno LIKE '$sql' OR prijmeni LIKE '$sql' ";
            $select1 .= " OR ulice LIKE '$sql' OR mesto LIKE '$sql' OR poznamka LIKE '$sql' ";

            $select2 = " OR psc LIKE '$sql' OR icq LIKE '$sql' OR mail LIKE '$sql' OR telefon LIKE '$sql' ";
            $select2 .= " OR vs LIKE '$sql' ) ";

            if ($_GET["select"] == 2) {
                $select3 = " AND fakturacni > 0 ";
            }
            if ($_GET["select"] == 3) {
                $select3 = " AND fakturacni is NULL ";
            }
            if ($_GET["select"] == 4) {
                $select3 = " AND k_platbe = 0 ";
            }
            if ($_GET["select"] == 5) {
                $select3 = " AND k_platbe > 0 ";
            }

            if ($_GET["select"] == 2) {
                $select3 = " AND fakturacni > 0 ";
            }
            if ($_GET["select"] == 3) {
                $select3 = " AND fakturacni is NULL ";
            }
            if ($_GET["select"] == 4) {
                $select3 = " AND k_platbe = 0 ";
            }
            if ($_GET["select"] == 5) {
                $select3 = " AND k_platbe > 0 ";
            }

            if ($_GET["razeni"] == 1) {
                $select4 = " order by id_cloveka ";
            }
            if ($_GET["razeni"] == 3) {
                $select4 = " order by jmeno ";
            }
            if ($_GET["razeni"] == 4) {
                $select4 = " order by prijmeni ";
            }
            if ($_GET["razeni"] == 5) {
                $select4 = " order by ulice ";
            }
            if ($_GET["razeni"] == 6) {
                $select4 = " order by mesto ";
            }
            if ($_GET["razeni"] == 14) {
                $select4 = " order by vs ";
            }
            if ($_GET["razeni"] == 15) {
                $select4 = " order by k_platbe ";
            }

            if ($_GET["razeni2"] == 1) {
                $select5 = " ASC ";
            }
            if ($_GET["razeni2"] == 2) {
                $select5 = " DESC ";
            }

            if ($_GET["fakt_skupina"] > 0) {
                $select6 = " AND fakturacni_skupina_id = ".intval($_GET["fakt_skupina"])." ";
            }

            if ((strlen($select4) > 1)) {
                $select4 = $select4.$select5;
            }

            $this->dotaz_source = " SELECT *, to_char(billing_suspend_start,'FMDD. FMMM. YYYY') as billing_suspend_start_f,
							to_char(billing_suspend_stop,'FMDD. FMMM. YYYY') as billing_suspend_stop_f 
						 FROM vlastnici ".$select1.$select2.$select3.$select6.$select4;
        } elseif($this->listMode == 3) {

            $this->dotaz_source = "SELECT *, to_char(billing_suspend_start,'FMDD. FMMM. YYYY') as billing_suspend_start_f, ".
            " to_char(billing_suspend_stop,'FMDD. FMMM. YYYY') as billing_suspend_stop_f ".
            " FROM vlastnici WHERE ( archiv = 0 or archiv is null ) AND id_cloveka = '$sql' ";
        } else {
            $this->listItemsContent = '<div class="alert alert-warning" role="alert" style="margin-right: 10px" >zadejte výraz k vyhledání....</div>';
        }

        $this->listFindId = $find_id;
        $this->listSql = $sql;
    }

    public function listItems()
    {
        $vlastnik = new vlastnik2_a2();
        $vlastnik->conn_mysql = $this->conn_mysql;
        $vlastnik->conn_pgsql = $this->container->get('connPgsql');
        $vlastnik->container = $this->container;
        $vlastnik->logger = $this->logger;
        $vlastnik->echo = false;
        $vlastnik->cross_url = "/vlastnici/cross";
        $vlastnik->csrf_html = $this->csrf_html;

        $this->listPrepareVars($vlastnik);

        $vlastnik->vlastnici_erase_povolen = $this->vlastnici_erase_povolen;
        $vlastnik->vlastnici_update_povolen = $this->vlastnici_update_povolen;
        $vlastnik->vlastnikAllowedUnassignObject = $this->vlastnikAllowedUnassignObject;

        $vlastnik->objektListAllowedActionUpdate = $this->objektListAllowedActionUpdate;
        $vlastnik->objektListAllowedActionErase = $this->objektListAllowedActionErase;
        $vlastnik->objektListAllowedActionGarant = $this->objektListAllowedActionGarant;

        $vlastnik->objektStbListAllowedActionUpdate = $this->objektStbListAllowedActionUpdate;
        $vlastnik->objektStbListAllowedActionErase = $this->objektStbListAllowedActionErase;

        // generovani exportu
        if($vlastnik->export_povolen) {
            $vlastnik->export();
        }

        // without find search we dont do anything
        if(strlen($this->listItemsContent) > 0) {
            return $this->listItemsContent;
        }

        $this->listItemsContent .= '<div class="vlastnici2-table" style="padding-right: 5px; ">';
        $this->listItemsContent .= $vlastnik->vypis_tab(1);

        $poradek = "find=".$this->listSql."&find_id=".$this->listFindId."&najdi=".$_GET["najdi"]."&select=".$_GET["select"]."&razeni=".
                    $_GET["razeni"]."&razeni2=".$_GET["razeni2"]."&fakt_skupina=".$_GET["fakt_skupina"];

        if(strlen($_GET["list"]) > 0) {
            $list = intval($_GET["list"]);
        }

        $listovani = new c_listing_vlastnici2("/vlastnici2?".$poradek."&menu=1", 30, $list, "<center><div class=\"text-listing2\">\n", "</div></center>\n", $this->dotaz_source);

        if(($list == "") || ($list == "1")) {    //pokud není list zadán nebo je první
            $bude_chybet = 0;                  //bude ve výběru sql dotazem chybet 0 záznamů
        } else {
            $bude_chybet = (($list - 1) * $listovani->interval);    //jinak jich bude chybet podle závislosti na listu a intervalu
        }

        $interval = $listovani->interval;

        if(intval($interval) > 0 and intval($bude_chybet) > 0) {
            $dotaz_final = $this->dotaz_source . " LIMIT " . intval($interval) . " OFFSET " . intval($bude_chybet) . " ";
        } else {
            $dotaz_final = $this->dotaz_source;
        }

        $this->logger->debug("vlastnik2\listItems: dump dotaz_final: " . var_export($dotaz_final, true));

        //      $listovani->listInterval();
        $this->listItemsContent .= $listovani->listPart(false);

        $this->listItemsContent .= $vlastnik->vypis($this->listSql, $this->listMode, $dotaz_final);

        $this->listItemsContent .= $vlastnik->vypis_tab(2);
        $this->listItemsContent .= '</div>';

        $this->listItemsContent .= $listovani->listPart(false);

        return $this->listItemsContent;
    }

    public function crossCheckVars(): bool
    {
        $akce = $_GET["akce"];
        $id_cloveka = $_GET["id_cloveka"];

        if(!(preg_match('/^([[:digit:]]+)$/', $id_cloveka))) {
            $this->alert_type = "danger";
            $this->alert_content = "Chyba! Nesouhlasi vstupni data. (id cloveka) ";
            return false;
        }

        if(!(preg_match('/^([[:digit:]]+)$/', $akce))) {
            $this->alert_type = "danger";
            $this->alert_content = "Chyba! Nesouhlasi vstupni data. (akce) ";
            return false;
        }
        return true;
    }

    public function crossRun(): bool
    {
        $akce = intval($_GET["akce"]);
        $id_cloveka = intval($_GET["id_cloveka"]);

        $this->logger->info(__CLASS__ . "\\" . __FUNCTION__ . " called");

        $html_init = "
                <html>
                <head>
                    <title>Vlastníci rozcestník</title>
                </head>
                <body>";

        if($akce == 0) {
            $dotaz_vlastnik_pom = pg_query($this->conn_pgsql, "SELECT firma, archiv FROM vlastnici WHERE id_cloveka = '$id_cloveka' ");

            while($data_vlastnik_pom = pg_fetch_array($dotaz_vlastnik_pom)) {
                $firma_vlastnik = $data_vlastnik_pom["firma"];
                $archiv_vlastnik = $data_vlastnik_pom["archiv"];
            }

            if($archiv_vlastnik == 1) {
                $id_cloveka_res = "/vlastnici/archiv";
            } elseif($firma_vlastnik == 1) {
                $id_cloveka_res = "/vlastnici2";
            } else {
                $id_cloveka_res = "/vlastnici";
            }

            $id_cloveka_res .= "?find_id=".$id_cloveka;

            $stranka = $id_cloveka_res;
        } elseif($akce == 1) {
            $stranka = fix_link_to_another_adminator("/vlastnici2-add-obj.php?id_vlastnika=".$id_cloveka);
        } elseif($akce == 2) {
            $stranka = fix_link_to_another_adminator("/vlastnici2-add-fakt.php?id_vlastnika=".$id_cloveka);
        } elseif($akce == 3) {
            $rs_vl = pg_query($this->conn_pgsql, "SELECT fakturacni FROM vlastnici WHERE id_cloveka = '$id_cloveka' ");

            while($data_vl = pg_fetch_array($rs_vl)) {
                $fakturacni_id = $data_vl["fakturacni"];
            }

            $stranka = fix_link_to_another_adminator("/vlastnici2-erase-f.php?id=".$fakturacni_id);
        } elseif($akce == 4) {
            $rs_vl = pg_query($this->conn_pgsql, "SELECT fakturacni FROM vlastnici WHERE id_cloveka = '$id_cloveka' ");

            while($data_vl = pg_fetch_array($rs_vl)) {
                $fakturacni_id = $data_vl["fakturacni"];
            }

            $stranka = fix_link_to_another_adminator("/vlastnici2-change-fakt.php?update_id=".$fakturacni_id);
        } elseif($akce == 5) {
            $stranka = fix_link_to_another_adminator("/opravy-index.php?typ=1&id_vlastnika=".$id_cloveka);
        } elseif($akce == 6) {
            $stranka = fix_link_to_another_adminator("/opravy-vlastnik.php?typ=2&id_vlastnika=".$id_cloveka."&ok=OK");
        } elseif($akce == 7) { //tisk smlouvy
            echo $html_init;

            $url = "/print/smlouva-2012-05";
            echo "<form action=\"".$url."\" method=\"post\" name=\"frm\" >\n\n";

            echo $this->csrf_html;

            $rs_vl1 = pg_query($this->conn_pgsql, "SELECT fakturacni_skupina_id FROM vlastnici WHERE id_cloveka = '".intval($id_cloveka)."' ");

            while($data = pg_fetch_array($rs_vl1)) {
                $fakturacni_skupina_id = $data["fakturacni_skupina_id"];
            }

            $rs_fs = $this->conn_mysql->query("SELECT typ_sluzby FROM fakturacni_skupiny WHERE id = '".intval($fakturacni_skupina_id)."' ");
            while($data = $rs_fs->fetch_array()) {
                $fakturacni_skupina_typ = $data["typ_sluzby"];
            }

            echo "<input type=\"hidden\" name=\"id_cloveka\" value=\"".intval($id_cloveka)."\" >\n";

            $sql = "SELECT t1.id_cloveka, t1.jmeno, t1.prijmeni, t1.ulice, t1.psc, t1.mesto, t1.mail, t1.telefon, t1.k_platbe, t1.vs,
                            t1.fakturacni, t1.fakturacni_skupina_id, t1.billing_freq, t1.typ_smlouvy, t1.trvani_do, 
                            t2.ftitle, t2.fulice, t2.fmesto, t2.fpsc, t2.ico, t2.dic
                                        
                            FROM ( vlastnici AS t1 LEFT JOIN fakturacni AS t2 ON t1.fakturacni=t2.id )
                            WHERE id_cloveka = '".intval($id_cloveka)."'";

            $rs_vl = pg_query($this->conn_pgsql, $sql);

            while($data_vl = pg_fetch_array($rs_vl)) {

                $vs = intval($data_vl["vs"]);
                $ec = ($vs == 0) ? '' : $vs;

                print "<input type=\"hidden\" name=\"ec\" value=\"".$ec."\" >\n";

                print "<input type=\"hidden\" name=\"vs\" value=\"".$vs."\" >\n";

                $jmeno = $data_vl["jmeno"]." ".$data_vl["prijmeni"];
                print "<input type=\"hidden\" name=\"jmeno\" value=\"".$jmeno."\" >\n";
                print "<input type=\"hidden\" name=\"adresa\" value=\"".$data_vl["ulice"]."\" >\n";
                print "<input type=\"hidden\" name=\"telefon\" value=\"".$data_vl["telefon"]."\" >\n";
                print "<input type=\"hidden\" name=\"email\" value=\"".$data_vl["mail"]."\" >\n";

                print "<input type=\"hidden\" name=\"mesto\" value=\"".$data_vl["psc"]." ".$data_vl["mesto"]."\" >\n\n";

                /*
                //cetnost fakturaci
                if($data_vl["billing_freq"] == 1){
                    print "<input type=\"hidden\" name=\"platba\" value=\"2\" >\n\n";
                }

                //smlouva - typ, atd
                if( $data_vl["typ_smlouvy"] == 2){
                    print "<input type=\"hidden\" name=\"min_plneni\" value=\"2\" >\n";

                    //list($a, $b, $c) = explode("-", $data_vl["trvani_do"]);
                    //print "<input type=\"hidden\" name=\"min_plneni_doba\" value=\"".$c.".".$b.".".$a."\" >\n\n";

                }
                */

                //firemní údaje
                if($data_vl["fakturacni"] > 0) {

                    print "<input type=\"hidden\" name=\"nazev_spol\" value=\"".$data_vl["ftitle"]."\" >\n\n";

                    print "<input type=\"hidden\" name=\"f_adresa\" value=\"".$data_vl["fulice"]."\" >\n\n";

                    print "<input type=\"hidden\" name=\"f_mesto\" value=\"".$data_vl["fmesto"].", ".$data_vl["fpsc"]."\" >\n\n";

                    print "<input type=\"hidden\" name=\"ico\" value=\"".$data_vl["ico"]."\" >\n";

                    print "<input type=\"hidden\" name=\"dic\" value=\"".$data_vl["dic"]."\" >\n";

                }

                //FS
                $rs_fs = $this->conn_mysql->query("SELECT typ_sluzby, sluzba_int, sluzba_int_id_tarifu, nazev, 
                                sluzba_iptv, sluzba_iptv_id_tarifu, sluzba_voip 
                                FROM fakturacni_skupiny 
                                WHERE id = '".intval($data_vl["fakturacni_skupina_id"])."'");

                if($rs_fs->num_rows == 1) {
                    //sluzba INTERNET

                    // TODO: check if its right replacement for mysql_seek
                    $rs_fs->data_seek(0);
                    $rs_fs_r = $rs_fs->fetch_row();

                    if($rs_fs_r[1] == 1) {  //sluzba internet - ANO

                        //zjisteni poctu objektu
                        $rs_obj = pg_query($this->conn_pgsql, "SELECT * FROM objekty WHERE id_cloveka = '".intval($id_cloveka)."' ");
                        $rs_obj_num = pg_num_rows($rs_obj);

                        if($rs_obj_num == 1) {
                            print "<input type=\"hidden\" name=\"internet_sluzba\" value=\"1\" >\n";
                        } elseif($rs_obj_num == 2) {
                            print "<input type=\"hidden\" name=\"internet_sluzba\" value=\"2\" >\n";
                        }
                    }

                    //sluzba IPTV
                    if($rs_fs_r[4] == 1) {
                        //sluzba IPTV - ANO
                        print "<input type=\"hidden\" name=\"iptv_sluzba\" value=\"1\" >\n";
                        //tarif
                        //	$iptv_sluzba_id_tarifu = intval(mysql_result($rs_fs, 0, 5));
                        //	print "<input type=\"hidden\" name=\"iptv_sluzba_id_tarifu\" value=\"".$iptv_sluzba_id_tarifu."\" >\n";
                    }

                    //VOIP
                    if($rs_fs_r[6] == 1) {
                        print "<input type=\"hidden\" name=\"voip_sluzba\" value=\"1\" >\n";
                    }
                } //konec if( mysql_num_rows rs_fs == 1 )
                //print "<input type=\"hidden\" name=\"tarif\" value=\"".$tarif."\" >\n";
            }

            echo "</form>\n";

            echo "<script language=\"JavaScript\" >document.frm.submit(); </script>";
            echo "Pokud nedojde k automatickému přesměrování, pokračujte <a href=\"#\" onclick=\"document.frm.submit()\" >zde</a>";

            echo "</body>\n</html>\n";
            exit;

        } elseif($akce == 8) { //vlozeni vypovedi
            echo $html_init;

            $url = fix_link_to_another_adminator("/vypovedi-vlozeni.php");
            echo "<form action=\"".$url."\" method=\"post\" name=\"frm\" >";

            print "<input type=\"hidden\" name=\"firma\" value=\"2\" >";
            print "<input type=\"hidden\" name=\"klient\" value=\"".$id_cloveka."\" >";

            echo "
            </form> 
            <script language=\"JavaScript\" > document.frm.submit(); </script>";

            echo "</body></html>";
            exit;

        } elseif($akce == 9) { //vlozIT hot. platbu
            echo $html_init;

            $url = fix_link_to_another_adminator("/platby-akce2.php");
            echo "<form action=\"".$url."\" method=\"post\" name=\"frm\" >";

            print "<input type=\"hidden\" name=\"firma\" value=\"2\" >";
            print "<input type=\"hidden\" name=\"klient\" value=\"".$id_cloveka."\" >";

            echo "</form>";

            echo "<script language=\"JavaScript\" > document.frm.submit(); </script>";

            echo "</body></html>";
            exit;

        } elseif($akce == 10) { //vypis plateb
            $stranka = fix_link_to_another_adminator("/platby-vypis.php?id_vlastnika=".$id_cloveka."&ok=OK");
        } elseif($akce == 11) { //vypis neuhr. faktur
            $stranka = fix_link_to_another_adminator("/faktury/fn-index.php?id_cloveka=".$id_cloveka."&filtr_stav_emailu=99");
        } elseif($akce == 12) { //online xml faktury
            $stranka = fix_link_to_another_adminator("/platby-vypis-xml.php?id_vlastnika=".$id_cloveka);
        } elseif($akce == 13) { //historie
            $stranka = "archiv-zmen?id_cloveka=".$id_cloveka;
        } elseif($akce == 14) { // online faktury - voip
            $stranka = fix_link_to_another_adminator("/platby-vypis-xml-voip.php?id_vlastnika=".$id_cloveka);
        } elseif($akce == 15) { // priradit objekt stb
            $stranka = fix_link_to_another_adminator("/vlastnici2-add-obj-stb.php?id_vlastnika=".$id_cloveka);
        } elseif($akce == 16) { // vypis faktur - pohoda SQL
            $stranka = fix_link_to_another_adminator("/pohoda_sql/phd_list_fa.php?id_vlastnika=".$id_cloveka);
        }

        // vlastnici2-add-obj.php
        if (filter_var($stranka, FILTER_VALIDATE_URL)) {
            $full_url = $stranka;
        } else {
            if (isset($_SERVER['HTTPS'])) {
                $prot = "https://";
            } else {
                $prot = "http://";
            }
            $full_url = $prot . $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"]. '/' . $stranka;
        }

        $this->logger->info(__CLASS__ . "\\" . __FUNCTION__ . ": redirecting to URL: " . var_export($full_url, true));

        header("Location: ".$full_url);

        echo $html_init;
        echo "<div><a href=\"" . $full_url . "\" >" . $full_url . "</a></div>";

        echo "</body></html>";

        return true;
    }

    public function action()
    {
        $update_id = intval($_POST["update_id"]);
        $odeslano = $_POST["odeslano"];
        $send = $_POST["send"];
        $firma_add = $_GET["firma_add"];

        if(($update_id > 0)) {
            $update_status = 1;
        }

        if(($update_status == 1 and !(isset($send)))) { //rezim upravy

            $dotaz_upd = pg_query($this->conn_pgsql, "SELECT * FROM vlastnici WHERE id_cloveka='".intval($update_id)."' ");
            $radku_upd = pg_num_rows($dotaz_upd);

            if($radku_upd == 0) {
                echo "Chyba! Požadovaná data nelze načíst! ";
            } else {

                while($data = pg_fetch_array($dotaz_upd)):

                    // primy promenny
                    $nick2 = $data["nick"];
                    $vs = $data["vs"];
                    $k_platbe = $data["k_platbe"];
                    $jmeno = $data["jmeno"];
                    $prijmeni = $data["prijmeni"];
                    $ulice = $data["ulice"];
                    $mesto = $data["mesto"];
                    $psc = $data["psc"];
                    $email = $data["mail"];
                    $icq = $data["icq"];
                    $tel = $data["telefon"];
                    $firma = $data["firma"];
                    $poznamka = $data["poznamka"];
                    $ucetni_index = $data["ucetni_index"];
                    $archiv = $data["archiv"];
                    $fakt_skupina = $data["fakturacni_skupina_id"];
                    $typ_smlouvy = $data["typ_smlouvy"];
                    $fakturacni = $data["fakturacni"];
                    $splatnost = $data["splatnost"];
                    $trvani_do = $data["trvani_do"];
                    $datum_podpisu = $data["datum_podpisu"];
                    $sluzba_int = $data["sluzba_int"];
                    $sluzba_iptv = $data["sluzba_iptv"];
                    $sluzba_voip = $data["sluzba_voip"];

                    $sluzba_int_id_tarifu = $data["sluzba_int_id_tarifu"];
                    $sluzba_iptv_id_tarifu = $data["sluzba_iptv_id_tarifu"];

                    $billing_freq = $data["billing_freq"];

                    $billing_suspend_status = $data["billing_suspend_status"];
                    $billing_suspend_reason = $data["billing_suspend_reason"];

                    $billing_suspend_start  = $data["billing_suspend_start"];
                    $billing_suspend_stop   = $data["billing_suspend_stop"];

                    //konverze z DB formatu
                    list($b_s_s_rok, $b_s_s_mesic, $b_s_s_den) = explode("-", $billing_suspend_start);
                    $billing_suspend_start = $b_s_s_den.".".$b_s_s_mesic.".".$b_s_s_rok;

                    list($b_s_t_rok, $b_s_t_mesic, $b_s_t_den) = explode("-", $billing_suspend_stop);
                    $billing_suspend_stop = $b_s_t_den.".".$b_s_t_mesic.".".$b_s_t_rok;

                endwhile;

            }

        } else { // rezim pridani, ukladani

            $nick2 = trim($_POST["nick2"]);
            $vs = trim($_POST["vs"]);
            $k_platbe = trim($_POST["k_platbe"]);
            $jmeno = trim($_POST["jmeno"]);
            $prijmeni = trim($_POST["prijmeni"]);
            $ulice = trim($_POST["ulice"]);

            $mesto = $_POST["mesto"];
            $psc = $_POST["psc"];
            $email = $_POST["email"];
            $icq = $_POST["icq"];
            $tel = $_POST["tel"];

            $fakturacni = $_POST["fakturacni"];
            $ftitle = $_POST["ftitle"];
            $fulice = $_POST["fulice"];
            $fmesto = $_POST["fmesto"];
            $fpsc = $_POST["fpsc"];
            $ico = $_POST["ico"];
            $dic = $_POST["dic"];
            $ucet = $_POST["ucet"];
            $splatnost = $_POST["splatnost"];
            $cetnost = $_POST["cetnost"];
            $firma = $_POST["firma"];
            $poznamka = $_POST["poznamka"];
            $ucetni_index = $_POST["ucetni_index"];
            $archiv = $_POST["archiv"];
            $fakt_skupina = intval($_POST["fakt_skupina"]);
            $splatnost = $_POST["splatnost"];

            $typ_smlouvy = intval($_POST["typ_smlouvy"]);

            $trvani_do = $_POST["trvani_do"];
            $datum_podpisu = $_POST["datum_podpisu"];

            $sluzba_int = intval($_POST["sluzba_int"]);
            $sluzba_iptv = intval($_POST["sluzba_iptv"]);
            $sluzba_voip = intval($_POST["sluzba_voip"]);

            $sluzba_int_id_tarifu = intval($_POST["sluzba_int_id_tarifu"]);
            $sluzba_iptv_id_tarifu = intval($_POST["sluzba_iptv_id_tarifu"]);

            $billing_freq = intval($_POST["billing_freq"]);

            $billing_suspend_status = intval($_POST["billing_suspend_status"]);
            $billing_suspend_reason = $_POST["billing_suspend_reason"];
            $billing_suspend_start  = $_POST["billing_suspend_start"];
            $billing_suspend_stop   = $_POST["billing_suspend_stop"];

            //systémove
            $send = $_POST["send"];

            if($firma_add == 2) {
                $firma = "";
            } elseif ($firma_add == 1) {
                $firma = "1";
            }

            if((strlen($splatnost) < 1)) {
                $splatnost = "15";
            }

        }

        //kontrola promených
        if(isset($send)) {
            if((strlen($nick2) > 0)) {
                vlastnici2pridani::checknick($nick2);
            }

            if((strlen($vs) > 0)) {
                vlastnici2pridani::checkvs($vs);
            }

            if((strlen($k_platbe) > 0)) {
                vlastnici2pridani::check_k_platbe($k_platbe);
            }

            if((strlen($splatnost) > 0)) {
                vlastnici2pridani::check_splatnost($splatnost);
            }

            if((strlen($icq) > 0)) {
                vlastnici2pridani::check_icq($icq);
            }

            if((strlen($email) > 0)) {
                vlastnici2pridani::check_email($email);
            }

            if((strlen($ucetni_index) > 0)) {
                vlastnici2pridani::check_uc_index($ucetni_index);
            }

            if((strlen($tel) > 0)) {
                vlastnici2pridani::check_tel($tel);
            }

            if((strlen($datum_podpisu) > 0)) {
                vlastnici2pridani::check_datum($datum_podpisu, "Datum podpisu");
            }

            if($typ_smlouvy == 2) {
                vlastnici2pridani::check_datum($trvani_do, "Trvání do");
            } elseif((strlen($trvani_do) > 0)) {
                vlastnici2pridani::check_datum($trvani_do, "Trvání do");
            }

            if($billing_suspend_status == 1) {

                vlastnici2pridani::check_datum($billing_suspend_start, "Poz. fakturace - od kdy");
                vlastnici2pridani::check_datum($billing_suspend_stop, "Poz. fakturace - do kdy");

            }

            if((strlen($billing_suspend_reason) > 0) and ($billing_suspend_status == 1)) {
                vlastnici2pridani::check_b_reason($billing_suspend_reason);
            }

        }

        if(($update_status == 1 and !(isset($send)))) {
            // $trvani_do = "";
            if((strlen($trvani_do) > 0)) {
                list($trvani_do_rok, $trvani_do_mesic, $trvani_do_den) = explode("\-", $trvani_do);
                $trvani_do = $trvani_do_den.".".$trvani_do_mesic.".".$trvani_do_rok;
            }

            if((strlen($datum_podpisu) > 0)) {
                list($datum_podpisu_rok, $datum_podpisu_mesic, $datum_podpisu_den) = explode("\-", $datum_podpisu);
                $datum_podpisu = $datum_podpisu_den.".".$datum_podpisu_mesic.".".$datum_podpisu_rok;
            }

        }

        // jestli uz se odeslalo , checkne se jestli jsou vsechny udaje
        if(($nick2 != "") and ($vs != "") and ($k_platbe != "") and (($fakt_skupina > 0) or ($firma <> 1) or ($archiv == 1))):

            if($update_status != 1) {

                //zjisti jestli neni duplicitni : nick, vs
                $MSQ_NICK = pg_query("SELECT * FROM vlastnici WHERE nick LIKE '$nick2' ");
                if (pg_num_rows($MSQ_NICK) > 0) {
                    $error .= "<h4>Nick ( ".$nick2." ) již existuje!!!</h4>";
                    $fail = "true";
                }

            }

            // check v modu uprava
            if(($update_status == 1 and (isset($odeslano)))) {

                //zjisti jestli neni duplicitni : nick, vs
                $MSQ_NICK = pg_query("SELECT * FROM vlastnici WHERE nick LIKE '$nick2' and id_cloveka <> '$update_id' ");
                if (pg_num_rows($MSQ_NICK) > 0) {
                    $error .= "<h4>Nick ( ".$nick2." ) již existuje!!!</h4>";
                    $fail = "true";
                }

            }

            //checkem jestli se macklo na tlacitko "OK" :)
            if (preg_match("/^OK$/", $odeslano)) {
                echo "";
            } else {
                $fail = "true";
                $error .= "<div class=\"vlastnici2-add-no-click-ok\"><h4>Data neuloženy, nebylo použito tlačítko \"OK\", ";
                $error .= "pro uložení klepněte na tlačítko \"OK\" v dolní části obrazovky!!!</h4></div>";
            }

        //ulozeni
        if (!(isset($fail))) {

            if ($update_status == "1") {
                // rezim upravy

                //prvne stavajici data docasne ulozime
                $pole2 .= "<b>akce: uprava vlastnika; </b><br>";

                $vysl4 = pg_query("select * from vlastnici WHERE id_cloveka='".intval($update_id)."' ");
                if((pg_num_rows($vysl4) <> 1)) {
                    echo "<p>Chyba! Nelze zjistit puvodni data pro ulozeni do archivu </p>";
                } else {
                    while ($data4 = pg_fetch_array($vysl4)):

                        $nick3 = $data4["nick"];
                        $vlast_upd_old["id_cloveka"] = $data4["id_cloveka"];

                        //novy zpusob archivace - pro porovnavani zmen
                        $pole_puvodni_data["id_cloveka"] = $data4["id_cloveka"];
                        $pole_puvodni_data["nick"] = $nick3;
                        $pole_puvodni_data["jmeno"] = $data4["jmeno"];
                        $pole_puvodni_data["prijmeni"] = $data4["prijmeni"];
                        $pole_puvodni_data["ulice"] = $data4["ulice"];
                        $pole_puvodni_data["mesto"] = $data4["mesto"];
                        $pole_puvodni_data["psc"] = $data4["psc"];
                        $pole_puvodni_data["icq"] = $data4["icq"];
                        $pole_puvodni_data["mail"] = $data4["mail"];
                        $pole_puvodni_data["telefon"] = $data4["telefon"];
                        $pole_puvodni_data["poznamka"] = $data4["poznamka"];
                        $pole_puvodni_data["vs"] = $data4["vs"];
                        $pole_puvodni_data["datum_podpisu"] = $data4["datum_podpisu"];
                        $pole_puvodni_data["k_platbe"] = $data4["k_platbe"];
                        $pole_puvodni_data["ucetni_index"] = $data4["ucetni_index"];
                        $pole_puvodni_data["archiv"] = $data4["archiv"];
                        $pole_puvodni_data["fakturacni_skupina_id"] = $data4["fakturacni_skupina_id"];
                        $pole_puvodni_data["splatnost"] = $data4["splatnost"];
                        $pole_puvodni_data["typ_smlouvy"] = $data4["typ_smlouvy"];
                        $pole_puvodni_data["firma"] = $data4["firma"];
                        $pole_puvodni_data["trvani_do"] = $data4["trvani_do"];

                        $pole_puvodni_data["sluzba_int"] = $data4["sluzba_int"];
                        $pole_puvodni_data["sluzba_iptv"] = $data4["sluzba_iptv"];
                        $pole_puvodni_data["sluzba_voip"] = $data4["sluzba_voip"];

                        $pole_puvodni_data["billing_freq"] = $data4["billing_freq"];

                        $pole_puvodni_data["billing_suspend_status"] = $data4["billing_suspend_status"];
                        $pole_puvodni_data["billing_suspend_reason"] = $data4["billing_suspend_reason"];

                        $pole_puvodni_data["billing_suspend_start"]  = $data4["billing_suspend_start"];
                        $pole_puvodni_data["billing_suspend_stop"]   = $data4["billing_suspend_stop"];

                        if($sluzba_int == 1) {
                            $pole_puvodni_data["sluzba_int_id_tarifu"] = $data4["sluzba_int_id_tarifu"];
                        }

                        if($sluzba_iptv == 1) {
                            $pole_puvodni_data["sluzba_iptv_id_tarifu"] = $data4["sluzba_iptv_id_tarifu"];
                        }

                        //$pole_puvodni_data["fakturacni"]=$data4["fakturacni"];

                    endwhile;
                }

                if((strlen($trvani_do) > 0)) {
                    list($trvani_do_den, $trvani_do_mesic, $trvani_do_rok) = split("\.", $trvani_do);
                    $trvani_do = $trvani_do_rok."-".$trvani_do_mesic."-".$trvani_do_den;
                }

                if((strlen($datum_podpisu) > 0)) {
                    list($datum_podpisu_den, $datum_podpisu_mesic, $datum_podpisu_rok) = split("\.", $datum_podpisu);
                    $datum_podpisu = $datum_podpisu_rok."-".$datum_podpisu_mesic."-".$datum_podpisu_den;
                }

                if((strlen($billing_freq) <> 1)) {
                    $billing_freq = 0;
                }

                $vlast_upd = array( "nick" => trim($nick2), "jmeno" => trim($jmeno), "prijmeni" => trim($prijmeni), "ulice" => trim($ulice), "mesto" => trim($mesto), "psc" => $psc,
                    "vs" => $vs, "k_platbe" => $k_platbe, "archiv" => $archiv, "fakturacni_skupina_id" => $fakt_skupina,
                    "splatnost" => $splatnost, "trvani_do" => $trvani_do, "sluzba_int" => $sluzba_int,
                    "sluzba_iptv" => $sluzba_iptv, "sluzba_voip" => $sluzba_voip,
                    "billing_freq" => $billing_freq );

                if ((strlen($firma) > 0)) {
                    $vlast_upd["firma"] = $firma;
                } else {
                    $vlast_upd["firma"] = null;
                } // u firmy else musi byt

                if ((strlen($email) > 0)) {
                    $vlast_upd["mail"] = $email;
                } else {
                    $vlast_upd["mail"] = null;
                }

                if ($icq > 0) {
                    $vlast_upd["icq"] = $icq;
                } else {
                    $vlast_upd["icq"] = "";
                }

                if ((strlen($tel) > 0)) {
                    $vlast_upd["telefon"] = $tel;
                } else {
                    $vlast_upd["telefon"] = null;
                }

                if ($ucetni_index > 0) {
                    $vlast_upd["ucetni_index"] = $ucetni_index;
                } else {
                    $vlast_upd["ucetni_index"] = null;
                }

                //if ( (strlen($poznamka) > 0 ) )
                { $vlast_upd["poznamka"] = $poznamka; }

                if ((strlen($datum_podpisu) > 0)) {
                    $vlast_upd["datum_podpisu"] = $datum_podpisu;
                } else {
                    $vlast_upd["datum_podpisu"] = null;
                }

                if ((strlen($typ_smlouvy) > 0)) {
                    $vlast_upd["typ_smlouvy"] = $typ_smlouvy;
                } else {
                    $vlast_upd["typ_smlouvy"] = 0;
                }

                if ($fakt_skupina < 1) {
                    $vlast_upd["fakturacni_skupina_id"] = null;
                }

                if($sluzba_int == 1) {
                    $vlast_upd["sluzba_int_id_tarifu"] = $sluzba_int_id_tarifu;
                }

                if($sluzba_iptv == 1) {
                    $vlast_upd["sluzba_iptv_id_tarifu"] = $sluzba_iptv_id_tarifu;
                }

                if($billing_suspend_status == 1) {
                    $vlast_upd["billing_suspend_status"] = intval($billing_suspend_status);
                    $vlast_upd["billing_suspend_reason"] = $conn_mysql->real_escape_string($billing_suspend_reason);

                    list($b_s_s_den, $b_s_s_mesic, $b_s_s_rok) = preg_split("/\./", $billing_suspend_start);
                    $billing_suspend_start = $b_s_s_rok."-".$b_s_s_mesic."-".$b_s_s_den;

                    list($b_s_t_den, $b_s_t_mesic, $b_s_t_rok) = preg_split("/\./", $billing_suspend_stop);
                    $billing_suspend_stop = $b_s_t_rok."-".$b_s_t_mesic."-".$b_s_t_den;

                    $vlast_upd["billing_suspend_start"]  = $conn_mysql->real_escape_string($billing_suspend_start);
                    $vlast_upd["billing_suspend_stop"]   = $conn_mysql->real_escape_string($billing_suspend_stop);
                } else {
                    $vlast_upd["billing_suspend_status"] = 0;
                    $vlast_upd["billing_suspend_reason"] = null;
                    $vlast_upd["billing_suspend_start"]  = null;
                    $vlast_upd["billing_suspend_stop"] = null;
                }

                // echo "<pre>" . var_export($vlast_upd, true) ."</pre>";

                // echo "<pre>ID: " . var_export( $vlast_id, true ) ."</pre>";

                try {
                    $affected = DB::connection('pgsql')
                                ->table('vlastnici')
                                ->where('id_cloveka', $update_id)
                                ->update($vlast_upd);
                } catch (Exception $e) {
                    $error = $e->getMessage();
                }

                if($affected == 1) {
                    echo "<br><H3><div style=\"color: green; \" >Data v databázi úspěšně změněny.</div></H3> (affected: " . $affected . ")\n";
                } else {
                    echo "<div style=\"color: red; \">Chyba! Data v databázi nelze změnit. </div><br>(Error: " . $error . ")\n";
                }

                require("vlastnici2-change-archiv-zmen-inc.php");

                $updated = "true";
            } else {
                // rezim pridani

                if((strlen($trvani_do) > 0)) {
                    list($trvani_do_den, $trvani_do_mesic, $trvani_do_rok) = preg_split("/\./", $trvani_do);
                    $trvani_do = $trvani_do_rok."-".$trvani_do_mesic."-".$trvani_do_den;
                }

                if((strlen($datum_podpisu) > 0)) {
                    list($datum_podpisu_den, $datum_podpisu_mesic, $datum_podpisu_rok) = preg_split("/\./", $datum_podpisu);
                    $datum_podpisu = $datum_podpisu_rok."-".$datum_podpisu_mesic."-".$datum_podpisu_den;
                }


                $vlastnik_add = array( "nick" => $nick2 ,  "vs" => $vs, "k_platbe" => $k_platbe,
                    "jmeno" => $jmeno, "prijmeni" => $prijmeni, "ulice" => $ulice,
                    "mesto" => $mesto, "psc" => $psc, "ucetni_index" => $ucetni_index,
                    "fakturacni_skupina_id" => $fakt_skupina, "splatnost" => $splatnost,
                    "typ_smlouvy" => $typ_smlouvy, "sluzba_int" => $sluzba_int,
                    "sluzba_iptv" => $sluzba_iptv, "sluzba_voip" => $sluzba_voip,
                    "billing_freq" => $billing_freq );

                if ((strlen($firma) > 0)) {
                    $vlastnik_add["firma"] = $firma;
                }
                if ((strlen($email) > 0)) {
                    $vlastnik_add["mail"] = $email;
                }
                if ($icq > 0) {
                    $vlastnik_add["icq"] = $icq;
                }
                if ((strlen($tel) > 0)) {
                    $vlastnik_add["telefon"] = $tel;
                }
                if ($ucetni_index > 0) {
                    $vlastnik_add["ucetni_index"] = $ucetni_index;
                }
                if ((strlen($poznamka) > 0)) {
                    $vlastnik_add["poznamka"] = $poznamka;
                }
                if ((strlen($trvani_do) > 0)) {
                    $vlastnik_add["trvani_do"] = $trvani_do;
                }
                if ((strlen($datum_podpisu) > 0)) {
                    $vlastnik_add["datum_podpisu"] = $datum_podpisu;
                }

                if ($fakt_skupina < 1) {
                    $vlast_upd["fakturacni_skupina_id"] = null;
                }

                if($sluzba_int == 1) {
                    $vlast_add["sluzba_int_id_tarifu"] = $sluzba_int_id_tarifu;
                }
                if($sluzba_iptv == 1) {
                    $vlast_add["sluzba_iptv_id_tarifu"] = $sluzba_iptv_id_tarifu;
                }

                if($billing_suspend_status == 1) {
                    $vlastnik_add["billing_suspend_status"] = intval($billing_suspend_status);
                    $vlastnik_add["billing_suspend_reason"] = $conn_mysql->real_escape_string($billing_suspend_reason);

                    list($b_s_s_den, $b_s_s_mesic, $b_s_s_rok) = preg_split("/\./", $billing_suspend_start);
                    $billing_suspend_start = $b_s_s_rok."-".$b_s_s_mesic."-".$b_s_s_den;

                    list($b_s_t_den, $b_s_t_mesic, $b_s_t_rok) = preg_split("/\./", $billing_suspend_stop);
                    $billing_suspend_stop = $b_s_t_rok."-".$b_s_t_mesic."-".$b_s_t_den;

                    $vlastnik_add["billing_suspend_start"] = $conn_mysql->real_escape_string($billing_suspend_start);
                    $vlastnik_addd["billing_suspend_stop"] = $conn_mysql->real_escape_string($billing_suspend_stop);
                }

                $res = pg_insert($db_ok2, 'vlastnici', $vlastnik_add);

                if($res) {
                    echo "<br><H3><div style=\"color: green; \" >Data úspěšně uloženy do databáze vlastníků. </div></H3>\n";
                } else {
                    echo "<div style=\"color: red; \">Chyba! Data do databáze vlastníků nelze uložit. </div>".pg_last_error($db_ok2)."<br>\n";
                }

                // pridame to do archivu zmen
                $pole = "<b>akce: pridani vlastnika ; </b><br>";

                foreach($vlastnik_add as $key => $val) {
                    $pole = $pole." [".$key."] => ".$val."\n";
                }

                if ($res) {
                    $vysledek_write = 1;
                } else {
                    $vysledek_write = 0;
                }

                $id = DB::table('archiv_zmen')
                        ->insertGetId([
                            'akce' => $pole,
                            'vysledek' => $vysledek_write,
                            'provedeno_kym' => \Cartalyst\Sentinel\Native\Facades\Sentinel::getUser()->email
                        ]);

                if($id > 0) {
                    echo "<br><H3><div style=\"color: green;\" >Změna byla úspěšně zaznamenána do archivu změn.</div></H3>\n";
                } else {
                    echo "<br><H3><div style=\"color: red;\" >Chyba! Změnu do archivu změn se nepodařilo přidat.</div></H3>\n";
                }

                // $add=$conn_mysql->query("INSERT INTO archiv_zmen (akce,provedeno_kym,vysledek) VALUES ('$pole','" . \Cartalyst\Sentinel\Native\Facades\Sentinel::getUser()->email . "','$vysledek_write')");

                $writed = "true";

                // konec else - rezim pridani
            }

        } else {
        } // konec else ( !(isset(fail) ), else tu musi bejt, pac jinak nefunguje nadrazeny if-elseif

        elseif (isset($send)):
            $error = "<h4>Chybí povinné údaje !!! ( aktuálně jsou povinné:  nick, vs, k platbě, Fakturační skupina ) </H4>";
        endif;

        if ($update_status == 1) {
            echo '<h3 align="center">Úprava vlastníka</h3>';
        } else {
            echo '<h3 align="center">Přidání nového vlastníka</h3>';
        }

        // jestli byli zadany duplicitni udaje, popr. se jeste form neodesilal, zobrazime form
        if ((isset($error)) or (!isset($send))):
            echo $error;

            // vlozeni vlastniho formu
            echo $this->actionForm();
            // require("vlastnici2-change-inc.php");

        elseif ((isset($writed) or isset($updated))):

            $back = pg_query($this->conn_pgsql, "SELECT * FROM vlastnici WHERE nick LIKE '$nick2' ");
            $back_radku = pg_num_rows($back);

            while ($data_back = pg_fetch_array($back)) {
                $firma_back = $data_back["firma"];
                $archiv_back = $data_back["archiv"];
            }

            if ($archiv_back == 1) {
                $stranka = "vlastnici-archiv.php";
            } elseif ($firma_back == 1) {
                $stranka = "vlastnici2.php";
            } else {
                $stranka = "vlastnici.php";
            }

        echo '<table border="0" width="50%" >
            <tr>
            <td align="right">Zpět na vlastníka </td>
            <td><form action="'.$stranka.'" method="GET" >
            <input type="hidden" value="' . $nick2 . '" name="find" >
            <input type="submit" value="ZDE" name="odeslat" > </form></td>

            <td align="right">Restart (all iptables ) </td>
            <td><form action="work.php" method="POST" ><input type="hidden" name="iptables" value="1" >
                <input type="submit" value="ZDE" name="odeslat" > </form> </td>
            </tr>
            </table>';

        echo '<br>';

        if ($firma_back == 1) {
            echo "<div style=\"padding-top: 10px; padding-bottom: 20px; font-size: 18px; \">
            <span style=\"font-weight: bold; \">Upozornění!</span> Změny je nutné dát vědet účetní. </div>";
        }

        echo '
        Objekt byl přidán/upraven , zadané údaje:<br><br> 
        <b>Nick</b>: ' . $nick2 . ' <br> 
        <b>VS</b>: ' . $vs . ' <br> 
        <b>K_platbě</b>: ' . $k_platbe . ' <br>';

        echo '<br>';

        echo '<b>Jméno</b>: ' . $jmeno . ' <br>
        <b>Příjmení</b>: ' . $prijmeni . ' <br>
        <b>Ulice</b>: ' . $ulice . '<br>
        <b>PSČ</b>: ' . $psc . '<br>';

        echo '<br>';

        echo '<b>e-mail</b>: ' . $email . '<br>
        <b>icq</b>: ' . $icq . '<br>
        <b>telefon</b>: ' . $tel . '<br> 
        <br>';

        echo '<b>firma</b>: ';

        if($firma == 1) {
            echo "Vlastníci2 - Copmany, s.r.o.";
        } else {
            echo "Vlastníci - FO";
        }

        echo "<br>";
        echo "<b>Archivovat: </b>";

        if($archiv == 1) {
            echo " Ano ";
        } else {
            echo " Ne ";
        }

        echo "<br><b>Fakturační skupina: </b> ".$fakt_skupina."<br>";

        echo '<b>Typ smlouvy:</b>: ';

        if($typ_smlouvy == 0) {
            echo "[nezvoleno]";
        } elseif($typ_smlouvy == 1) {
            echo "[na dobu neurčitou]";
        } elseif($typ_smlouvy == 2) {
            echo "[na dobu určitou]";
            echo " ( doba trvání do: ";

            list($trvani_do_rok, $trvani_do_mesic, $trvani_do_den) = explode("-", $trvani_do);
            $trvani_do = $trvani_do_den.".".$trvani_do_mesic.".".$trvani_do_rok;

            echo $trvani_do." )";
        } else {
            echo "[nelze zjistit]";
        }

        echo '<br>';

        echo '<b>Datum podpisu</b>: ';

        if((strlen($datum_podpisu) > 0)) {
            list($datum_podpisu_rok, $datum_podpisu_mesic, $datum_podpisu_den) = explode("-", $datum_podpisu);
            $datum_podpisu = $datum_podpisu_den.".".$datum_podpisu_mesic.".".$datum_podpisu_rok;
        }

        echo $datum_podpisu;

        echo '<br><br>';

        echo '<b>Služba Internet:</b>';

        if($sluzba_inet == 0) {
            echo "Ne";
        } elseif($sluzba_inet == 1) {
            echo "Ano";
        } else {
            echo "Nelze zjistit - hodnota: ".$sluzba_inet;
        }

        echo '<br>'
            . '<b>Služba IPTV:</b>';

        if($sluzba_iptv == 0) {
            echo "Ne";
        } elseif($sluzba_iptv == 1) {
            echo "Ano";
        } else {
            echo "Nelze zjistit - hodnota: ".$sluzba_iptv;
        }

        echo '<br>'
            . '<b>Služba VoIP:</b>';

        if($sluzba_voip == 0) {
            echo "Ne";
        } elseif($sluzba_voip == 1) {
            echo "Ano";
        } else {
            echo "Nelze zjistit - hodnota: ".$sluzba_voip;
        }

        echo '<br><br>';

        echo '<b>Pozastavené fakturace:</b>';

        if($billing_suspend_status == 1) {
            echo "Ano";
        } else {
            echo "Ne";
        }

        echo "<br>";

        if($billing_suspend_status == 1) {
            list($b_s_s_rok, $b_s_s_mesic, $b_s_s_den) = explode("-", $billing_suspend_start);
            $billing_suspend_start = $b_s_s_den.".".$b_s_s_mesic.".".$b_s_s_rok;

            list($b_s_t_rok, $b_s_t_mesic, $b_s_t_den) = explode("-", $billing_suspend_stop);
            $billing_suspend_stop = $b_s_t_den.".".$b_s_t_mesic.".".$b_s_t_rok;

            echo "<b>od kdy</b>: ".$billing_suspend_start."<br>\n";
            echo "<b>do kdy</b>: ".$billing_suspend_stop."<br>\n";

            echo "<b>důvod</b>: ".$billing_suspend_reason."<br>\n";
        }

        echo '<br>'
        . '<br><br>';

        endif;
    }

    private function actionForm(): string
    {
        $output = "";

        $output .= '<form name="form1" method="post" action="" >
                <input type="hidden" name="send" value="true">
                <input type="hidden" name="update_id" value="'.intval($update_id).'" >'
                . '<input type="hidden" name="fakturacni" value="'.intval($fakturacni).'" >';
        
        $output .= $this->csrf_html;

        $output .= '<table border="0" width="100%">
            <tr>
            <td width="70">nick:
            <input type="Text" name="nick2" size="10" maxlength="20" value="'.$nick2.'" ></td>'

            . '<td colspan="3" width="80" align="left" >'


            . 'vs: <input type="Text" name="vs" size="" maxlength="" value="'.$vs.'" >'

            . '<span style="padding-left: 10px; padding-right: 10px; ">'
            . 'k platbě: </span><input type="text" name="k_platbe" size="" maxlength="" value="'.$k_platbe.'" >'

            . '<span style="padding-left: 10px; padding-right: 10px; ">Splatnost (ke dni):';

        if ($firma == 1) {
            $output .= '<input type="text" name="splatnost" size="8" maxlength="" value="'.$splatnost.'" >';
        } else {
            $output .= "<span style=\"color: grey; \" > není dostupné </span>";
        }

        $output .= '</span>'

            . '</td>'
            . '</tr>'

            . '<tr><td><br></td></tr>'

            . '<tr>'
            . '<td> jméno a příjmení: </td>'
            . '<td colspan="" >'
                . '<input type="text" name="jmeno" value="'.$jmeno.'" >'
                . '<input type="text" name="prijmeni" value="'.$prijmeni.'" >'
                . '</td>'

                . '<td>účetní index: <span style="padding-left: 10px; "></span>';

        //if ( $firma == 1)
        { $output .= '<input type="text" name="ucetni_index" value="'.$ucetni_index.'" >'; }
        //else
        //{ $output .= "<span style=\"color: grey; \" >není dostupné</span>"; }

        $output .= '</td>
                </tr>'
            . '<tr><td><br></td></tr>'

            . '<tr>
                <td>Ulice a čp. :</td>
                <td colspan="1" ><input type="text" name="ulice" size="35" maxlength="" value="'.$ulice.'" ></td>'
                . '<td>Fakturační skupina: ';

        if($firma == 1) {

            $output .= '<span style="padding-left: 10px;" >'

                . '<select name="fakt_skupina" size="1" >'

                ."\t\t".'<option value="0" class="vlastnici2-fakt-skupina" ';
            if ($fakt_skupina == 0) {
                $output .= " selected ";
            }
            $output .= ' > žádná </option> '."\n";

            if ($fakturacni > 0) {
                $sql = "SELECT * FROM fakturacni_skupiny WHERE typ = 2 order by nazev DESC";
            } else {
                $sql = "SELECT * FROM fakturacni_skupiny WHERE typ = 1 order by nazev DESC";
            }

            try {
                $dotaz_fakt_skup = $conn_mysql->query($sql);
                $dotaz_fakt_skup_radku = $dotaz_fakt_skup->num_rows;
            } catch (Exception $e) {
                die("<h2 style=\"color: red; \">Error: Database query failed! Caught exception: " . $e->getMessage() . "\n" . "</h2></body></html>\n");
            }

            if($dotaz_fakt_skup_radku > 0) {
                while($data_fakt_skup = $dotaz_fakt_skup->fetch_array()) {
                    $output .= "\t\t<option value=\"".$data_fakt_skup["id"]."\" ";
                    if ($fakt_skupina == $data_fakt_skup["id"]) {
                        $output .= " selected ";
                    }
                    $output .= " > ".$data_fakt_skup["nazev"];

                    if($data_fakt_skup["typ"] == 1) {
                        $output .= " (DÚ) ";
                    } elseif($data_fakt_skup["typ"] == 2) {
                        $output .= " (FÚ) ";
                    } else {
                        $output .= $data_fakt_skup["typ"];
                    }

                    $output .= " </option>\n";
                } // konec while
            } // kone if dotaz > 0

        } // konec if firma == 1
        else {
            $output .= "<span style=\"color: grey; \" >není dostupné</span>";
        }

        $output .= '        
                </select>
                </td>
            </tr>'
            . '<tr><td><br></td></tr>'

            . '<tr>
                <td>Město , PSČ: </td>
                <td colpsan="2" >
                    <input type="text" name="mesto" size="" maxlength="" value="'.$mesto.'">
                    <input type="text" name="psc" size="10" value="'.$psc.'">
                </td>'

            . '<td valign="top" rowspan="7" >'
            . 'Poznámka: <br>
                <textarea rows="10" name="poznamka" cols="40">' . $poznamka . '</textarea>
            </td>'
            . '</tr>'

            . '<tr><td><br></td></tr>'

            . '<tr>
                <td>Email: </td>
                <td colspan="3" ><input type="text" name="email" size="30" value="'.$email.'" ></td>
            </tr>'

            . '<tr><td><br></td></tr>'

            . '<tr>
            <td>ICQ:</td>
            <td colspan="3" >
                <input type="text" name="icq" size="30" value="'.$icq.'">
            </td>'

            . '</tr>'

            . '<tr><td><br></td></tr>'

             .'<tr>
                <td>Telefon: </td>
                <td><input type="text" name="tel" size="30" value="'.$tel.'"> 
                </td>'

            . '</tr>'

            . '<tr><td colspan="2"><br></td></tr>'

            . '<tr>
            <td> Firma: </td>
            <td colspan="1" >'

            . '<select name="firma" size="1" onChange="self.document.forms.form1.submit()" >
            <option value="" ';
        if (($firma == "")) {
            $output .= " selected ";
        } $output .= ' >Fyzická os. ( vlastníci )</option>
            <option value="1" ';
        if (($firma == 1)) {
            $output .= " selected ";
        } $output .= ' >Company, s.r.o. ( vlastníci2 ) </option>
            </select>'
        . '</td>'
        . '<td>';

        if ($update_status == "1") {
            $output .= "<span style=\"padding-right: 20px; \" >Archivovat: </span>";

            $output .= " <select name=\"archiv\" size=\"1\" >
                <option value=\"0\"";
            if (($archiv != "1")) {
                $output .= " selected ";
            } $output .= " > Ne </option>
                <option value=\"1\"";
            if (($archiv == "1")) {
                $output .= " selected ";
            } $output .= " > Ano </option>";
        } else {
            $output .= "<br>";
        }

        $output .= '</td>
                </tr>'

                . '<tr><td colspan="3"><br></td><tr>'

                . '<tr>
                <td colspan="" >Smlouva na dobu: </td>
                <td colspan="" >';

        if($firma == 1) {
            $output .= '<select name="typ_smlouvy" size="1" onChange="self.document.forms.form1.submit()" >    
                <option value="0"';
            if(($typ_smlouvy == 0) or (!isset($typ_smlouvy))) {
                $output .= " selected ";
            }

            $output .= 'class="vlastnici-nezvoleno" >Nevybráno</option>
                <option value="1"';
            if($typ_smlouvy == 1) {
                $output .= " selected ";
            } $output .= ' >Neurčitou</option>
                <option value="2"';
            if($typ_smlouvy == 2) {
                $output .= " selected ";
            } $output .= ' >Určitou</option>
                </select>';
        } else {
            $output .= "<span style=\"color: gray; \" >Není dostupné</span>";
        }

        $output .= "</td>";

        $output .= "<td><span style=\"font-weight: bold;\" >Aktivace / deaktivace služeb:</span></td>";

        $output .= '</td>
                <tr>

                <tr><td colspan="3" ><br></td></tr>

                <tr>
                <td>Trvání do:</td>';

        $output .= "<td colspan=\"\" >";

        if((($typ_smlouvy == 2) and ($firma == 1))) {
            $output .= "<input type=\"text\" name=\"trvani_do\" value=\"".$trvani_do."\" >";
            $output .= "<span style=\"padding-left: 15px; \" >formát: ( dd.mm.rrrr )</span>";
        } else {
            $output .= "<span style=\"color: grey; \">Není dostupné</span>";
        }

        if($firma == 1) {
            //sluzba internet
            $output .= "<td><span style=\"padding-right: 40px; \" ><b>Internet:</b></span>";

            $output .= "<select name=\"sluzba_int\" size=\"1\" onChange=\"self.document.forms.form1.submit()\" >";
            $output .= "<option value=\"0\" ";
            if($sluzba_int == 0 or !isset($sluzba_int)) {
                $output .= " selected ";
            } $output .= " >Ne</option>";
            $output .= "<option value=\"1\" ";
            if($sluzba_int == 1) {
                $output .= " selected ";
            } $output .= " >Ano</option>";

            $output .= "</select>";

            $output .= "</td>";

        } else {
            $output .= "<td><span style=\"color: grey; \">Není dostupné</span></td>";
        }

        $output .= '</tr>';

        if($sluzba_int == 1) {
            $output .= "<tr>
                <td colspan=\"2\" >&nbsp;</td>";
            $output .= "<td><span style=\"padding-right: 17px; \" >Vyberte tarif: </span>";

            //vypis tarifu
            $output .= "<select name=\"sluzba_int_id_tarifu\" size=\"1\" onChange=\"self.document.forms.form1.submit()\" >";

            $output .= "<option value=\"999\" ";
            if($sluzba_int_id_tarifu == 999 or !isset($sluzba_int_id_tarifu)) {
                $output .= " selected ";
            }
            $output .= " style=\"color: gray; \">Nevybráno</option>";

            try {
                $dotaz_tarify_id_tarifu = $conn_mysql->query("SELECT * FROM tarify_int ORDER BY id_tarifu ");
            } catch (Exception $e) {
                die("<h2 style=\"color: red; \">Error: Database query failed! Caught exception: " . $e->getMessage() . "\n" . "</h2></body></html>\n");
            }

            while($data_tarify = $dotaz_tarify_id_tarifu->fetch_array()) {
                $output .= "<option value=\"".$data_tarify["id_tarifu"]."\" ";
                if($sluzba_int_id_tarifu == $data_tarify["id_tarifu"]) {
                    $output .= " selected ";
                }
                $output .= " >".$data_tarify["jmeno_tarifu"]." (".$data_tarify["zkratka_tarifu"].")</option>";

            }

            $output .= "</select>";
            $output .= "</td>";
            $output .= "</tr>";
        }

        $output .= '<tr><td colspan="3" ><br></td></tr>
            <tr>
            <td colspan="" >Datum podpisu: </td>
            <td>';

        if ($firma == 1) {
            $output .= '<input type="text" name="datum_podpisu" size="10" class=tcal value='."\"".$datum_podpisu."\" > (formát: dd.mm.yyyy)";
        } else {
            $output .= "<span style=\"color: grey; \">Není dostupné</span>";
        }

        $output .= "</td>";

        if($firma == 1) {
            //sluzba iptv
            $output .= "<td><span style=\"padding-right: 5px; \" ><b>IPTV</b> (televize): </span>";

            $output .= "<select name=\"sluzba_iptv\" size=\"1\" onChange=\"self.document.forms.form1.submit()\" >";
            $output .= "<option value=\"0\" ";
            if($sluzba_iptv == 0 or !isset($sluzba_iptv)) {
                $output .= " selected ";
            }
            $output .= ">Ne</option>";
            $output .= "<option value=\"1\" ";
            if($sluzba_iptv == 1) {
                $output .= " selected ";
            }
            $output .= ">Ano</option>";

            $output .= "</select>";

            $output .= "</td>";
        } else {
            $output .= "<td><span style=\"color: grey; \">Není dostupné</span></td>";
        }

        $output .= "</tr>";

        if($sluzba_iptv == 1) {
            $output .= "<tr>
                <td colspan=\"2\" >&nbsp;</td>";
            $output .= "<td><span style=\"padding-right: 17px; \" >Vyberte tarif: </span>";

            //vypis tarifu
            $output .= "<select name=\"sluzba_iptv_id_tarifu\" size=\"1\" onChange=\"self.document.forms.form1.submit()\" >";

            $output .= "<option value=\"999\" ";
            if($sluzba_iptv_id_tarifu == 999 or !isset($sluzba_iptv_id_tarifu)) {
                $output .= " selected ";
            }
            $output .= " style=\"color: gray; \">Nevybráno</option>";

            try {
                $dotaz_iptv_id_tarifu = $conn_mysql->query("SELECT * FROM tarify_iptv ORDER BY id_tarifu ");
            } catch (Exception $e) {
                die("<h2 style=\"color: red; \">Error: Database query failed! Caught exception: " . $e->getMessage() . "\n" . "</h2></body></html>\n");
            }

            while($data_iptv = $dotaz_iptv_id_tarifu->fetch_array()) {
                $output .= "<option value=\"".$data_iptv["id_tarifu"]."\" ";
                if($sluzba_iptv_id_tarifu == $data_iptv["id_tarifu"]) {
                    $output .= " selected ";
                }
                $output .= " >".$data_iptv["jmeno_tarifu"]." (".$data_iptv["zkratka_tarifu"].")</option>";
            }

            $output .= "</select>";
            $output .= "</td>";

            $output .= "</tr>";

        }

        $output .= "<tr><td colspan=\"3\" ><br></td></tr>";

        $output .= "<tr>";
        $output .= "<td>Frekvence fakturování:</td>";

        if($firma == 1) {
            $output .= "<td>".
            "<select size=\"1\" name=\"billing_freq\">";

            $output .= "<option value=\"0\" ";
            if($billing_freq == 0 or empty($billing_freq)) {
                $output .= "selected";
            } $output .= " >Měsíční</option>";
            $output .= "<option value=\"1\" ";
            if($billing_freq == 1) {
                $output .= "selected";
            } $output .= " >Čtvrtletní</option>";

            $output .= "</select>".
                "</td>";
        } else {
            $output .= "<td><span style=\"color: grey; \">Není dostupné</span></td>";
        }

        if($firma == 1) {
            //sluzba VoIP
            $output .= "<td><span style=\"padding-right: 10px; \" ><b>VoIP</b> (telefon): </span>";

            $output .= "<select name=\"sluzba_voip\" size=\"1\" onChange=\"self.document.forms.form1.submit()\" >";
            $output .= "<option value=\"0\" ";
            if($sluzba_voip == 0 or !isset($sluzba_voip)) {
                $output .= " selected ";
            }
            $output .= " >Ne</option>";
            $output .= "<option value=\"1\" ";
            if($sluzba_voip == 1) {
                $output .= " selected ";
            }
            $output .= ">Ano</option>";

            $output .= "</select>";

            $output .= "</td>";
        } else {
            $output .= "<td><span style=\"color: grey; \">Není dostupné</span></td>";
        }

        $output .= '<tr><td colspan="3" ><br></td></tr>';

        $output .= '<tr>
                    <td>Pozastavené fakturace:</td>
                    <td>';

        $output .= "<select size=\"1\" name=\"billing_suspend_status\" onChange=\"self.document.forms.form1.submit()\">";

        $output .= "<option value=\"0\" ";
        if(($billing_suspend_status == 0) or (!isset($billing_suspend_status))) {
            $output .= " selected ";
        } $output .= ">Ne</option>";
        $output .= "<option value=\"1\" ";
        if($billing_suspend_status == 1) {
            $output .= " selected ";
        } $output .= ">Ano</option>";

        $output .= "</select>";

        $output .= '</td>
            <td>Důvod pozastavení:</td>
            </tr>

            <tr>
            <td colspan="2" ><br></td>';


        if($billing_suspend_status == 1) {
            $output .= "<td rowspan=\"3\">
                <textarea type=\"text\" name=\"billing_suspend_reason\" cols=\"40\" rows=\"4\" >".htmlspecialchars($billing_suspend_reason)."</textarea>
                </td>";
        } else {
            $output .= "<td rowspan=\"3\"><span style=\"color: grey; \">Není dostupné</span></td>";
            $output .= "<input type=\"hidden\" name=\"billing_suspend_reason\" value=\"".htmlspecialchars($billing_suspend_reason)."\" >";
        }

        $output .= '</tr>
                <tr>
                    <td>Poz. fakturace - od kdy:</td>
                <td>';

        if($billing_suspend_status == 1) {
            $output .= "<input type=\"text\" name=\"billing_suspend_start\" size=\"10\" class=\"tcal\" value=\"".
                htmlspecialchars($billing_suspend_start)."\" > datum (formát: dd.mm.yyyy)";
        } else {
            $output .= "<span style=\"color: grey; \">Není dostupné</span>";
            $output .= "<input type=\"hidden\" name=\"billing_suspend_start\" value=\"".htmlspecialchars($billing_suspend_start)."\" >";
        }


        $output .= '
            </td>
            </tr>

            <tr>
            <td>Poz. fakturace - do kdy:</td>
            <td>';

        if($billing_suspend_status == 1) {
            $output .= "<input type=\"text\" name=\"billing_suspend_stop\" size=\"10\" value=\"".
            htmlspecialchars($billing_suspend_stop)."\" class=\"tcal\"> datum (formát: dd.mm.yyyy)";
        } else {
            $output .= "<span style=\"color: grey; \">Není dostupné</span>";
            $output .= "<input type=\"hidden\" name=\"billing_suspend_stop\" value=\"".htmlspecialchars($billing_suspend_stop)."\" >";
        }

        $output .= '
            </td>
            </tr>
            
            <tr><td colspan="3" ><br></td></tr>
            
            <tr>
            <td colspan="2" align="center">
            
            <hr>
            <input name="odeslano" type="submit" value="OK" >
            </td>
            <td><br></td>
            
            </tr>

            <tr><td colspan="3" ><br></td></tr>

        </table>
        </form>';

        return $output;
    }
}
