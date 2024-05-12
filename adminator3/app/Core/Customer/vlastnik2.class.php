<?php

use Psr\Container\ContainerInterface;

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
            $dotaz_vlastnik_pom = pg_query($this->conn_pgsql, "SELECT * FROM vlastnici WHERE id_cloveka = '$id_cloveka' ");

            while($data_vlastnik_pom = pg_fetch_array($dotaz_vlastnik_pom)) {
                $firma_vlastnik = $data_vlastnik_pom["firma"];
                $archiv_vlastnik = $data_vlastnik_pom["archiv"];
            }

            if($archiv_vlastnik == 1) {
                $id_cloveka_res = "vlastnici-archiv.php";
            } elseif($firma_vlastnik == 1) {
                $id_cloveka_res .= "vlastnici2.php";
            } else {
                $id_cloveka_res .= "vlastnici.php";
            }

            $id_cloveka_res .= "?find_id=".$id_cloveka;

            $stranka = $id_cloveka_res;
        } elseif($akce == 1) {
            $stranka = "vlastnici2-add-obj.php?id_vlastnika=".$id_cloveka;
        } elseif($akce == 2) {
            $stranka = "vlastnici2/fakturacni-skupiny/action?id_vlastnika=".$id_cloveka;
        } elseif($akce == 3) {
            $rs_vl = pg_query($this->conn_pgsql, "SELECT * FROM vlastnici WHERE id_cloveka = '$id_cloveka' ");

            while($data_vl = pg_fetch_array($rs_vl)) {
                $fakturacni_id = $data_vl["fakturacni"];
            }

            $stranka = "vlastnici2-erase-f.php?id=".$fakturacni_id;
        } elseif($akce == 4) {
            $rs_vl = pg_query($this->conn_pgsql, "SELECT * FROM vlastnici WHERE id_cloveka = '$id_cloveka' ");

            while($data_vl = pg_fetch_array($rs_vl)) {
                $fakturacni_id = $data_vl["fakturacni"];
            }

            $stranka = "vlastnici2/fakturacni-skupiny/action?update_id=".$fakturacni_id;
        } elseif($akce == 5) {
            $stranka = "opravy-index.php?typ=1&id_vlastnika=".$id_cloveka;
        } elseif($akce == 6) {
            $stranka = "opravy-vlastnik.php?typ=2&id_vlastnika=".$id_cloveka."&ok=OK";
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

            $url = "vypovedi-vlozeni.php";
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

            $url = "platby-akce2.php";
            echo "<form action=\"".$url."\" method=\"post\" name=\"frm\" >";

            print "<input type=\"hidden\" name=\"firma\" value=\"2\" >";
            print "<input type=\"hidden\" name=\"klient\" value=\"".$id_cloveka."\" >";

            echo "</form>";

            echo "<script language=\"JavaScript\" > document.frm.submit(); </script>";

            echo "</body></html>";
            exit;

        } elseif($akce == 10) { //vypis plateb
            $stranka = "platby-vypis.php?id_vlastnika=".$id_cloveka."&ok=OK";
        } elseif($akce == 11) { //vypis neuhr. faktur
            $stranka = "faktury/fn-index.php?id_cloveka=".$id_cloveka."&filtr_stav_emailu=99";
        } elseif($akce == 12) { //online xml faktury
            $stranka = "platby-vypis-xml.php?id_vlastnika=".$id_cloveka;
        } elseif($akce == 13) { //historie
            $stranka = "archiv-zmen?id_cloveka=".$id_cloveka;
        } elseif($akce == 14) { // online faktury - voip
            $stranka = "platby-vypis-xml-voip.php?id_vlastnika=".$id_cloveka;
        } elseif($akce == 15) { // priradit objekt stb
            $stranka = "vlastnici2-add-obj-stb.php?id_vlastnika=".$id_cloveka;
        } elseif($akce == 16) { // vypis faktur - pohoda SQL
            $stranka = "pohoda_sql/phd_list_fa.php?id_vlastnika=".$id_cloveka;
        }

        if (isset($_SERVER['HTTPS'])) {
            $prot = "https://";
        } else {
            $prot = "http://";
        }

        $full_url = $prot . $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"]. '/' . $stranka;

        $this->logger->info(__CLASS__ . "\\" . __FUNCTION__ . ": redirecting to URL: " . var_export($full_url, true));

        header("Location: ".$full_url);

        echo $html_init;
        echo "<div><a href=\"" . $full_url . "\" >" . $full_url . "</a></div>";

        echo "</body></html>";

        return true;
    }

}
