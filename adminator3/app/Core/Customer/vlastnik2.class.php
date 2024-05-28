<?php

use App\Core\adminator;
use Psr\Container\ContainerInterface;

class vlastnik2 extends adminator
{
    public \mysqli|\PDO $conn_mysql;

    public \PgSql\Connection|\PDO|null $conn_pgsql;

    public \Monolog\Logger $logger;

    public $smarty;

    public $container;

    public $adminator; // handler for instance of adminator class

    public $alert_type;

    public $alert_content;

    public $csrf_html;

    public $export_povolen = false;

    public $cross_url = null;

    public $listItemsContent;

    public $listMode; // original local variable "co"

    public $listSql; // original local variable "sql"

    public $istFindId;

    public $listFindId;

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
        $this->smarty = $container->get('smarty');

        // $this->adminator = new \App\Core\adminator($this->conn_mysql, $this->container->get('smarty'), $this->logger);
    }

    private function listPrepareVars()
    {
        // perms for actions/links
        //
        if ($this->adminator->checkLevel(63) === true) {
            $this->export_povolen = true;
        }

        //promenne pro akce objektu
        if ($this->adminator->checkLevel(29) === true) {
            $this->objektListAllowedActionUpdate = true;
        }
        if ($this->adminator->checkLevel(33) === true) {
            $this->objektListAllowedActionErase = true;
        }
        if ($this->adminator->checkLevel(34) === true) {
            $this->objektListAllowedActionGarant = true;
        }

        // akce objekty STB
        if ($this->adminator->checkLevel(137) === true) {
            $this->objektStbListAllowedActionUpdate = true;
        }
        if ($this->adminator->checkLevel(310) === true) {
            $this->objektStbListAllowedActionErase = true;
        }

        // promeny pro mazani, zmenu vlastniku
        if ($this->adminator->checkLevel(45) === true) {
            $this->vlastnici_erase_povolen = true;
        }

        if ($this->adminator->checkLevel(34) === true) {
            $this->vlastnici_update_povolen = true;
        }

        // // odendani objektu od vlastnika
        if ($this->adminator->checkLevel(49) === true) {
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
        $this->cross_url = "/vlastnici/cross";

        $this->listPrepareVars();

        // generovani exportu
        if($this->export_povolen) {
            $this->export();
        }

        // without find search we dont do anything
        if(strlen($this->listItemsContent) > 0) {
            return $this->listItemsContent;
        }

        $this->listItemsContent .= '<div class="vlastnici2-table" style="padding-right: 5px; ">';
        $this->listItemsContent .= $this->vypis_tab(1);

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

        $this->listItemsContent .= $this->vypis($this->listSql, $this->listMode, $dotaz_final);

        $this->listItemsContent .= $this->vypis_tab(2);
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

    public function vypis_tab($par)
    {
        $output = "";

        if ($par == 1) {
            $output .= "\n".'<table border="1" width="100%">'."\n";
        } elseif ($par == 2) {
            $output .= "\n".'</table>'."\n";
        } else {
            $output .= "chybny vyber";
        }

        return $output;
        // konec funkce vypis_tab
    }

    // $dotaz_final - for pq_query
    public function vypis($sql, $co, $dotaz_final)
    {
        // co - co hledat, 1- podle dns, 2-podle ip

        $output = "";

        $objekt = new \App\Core\objekt($this->container);
        $objekt->csrf_html = $this->csrf_html;

        $objekt->listAllowedActionUpdate = $this->objektListAllowedActionUpdate;
        $objekt->listAllowedActionErase = $this->objektListAllowedActionErase;
        // $objekt-> = $this->objektListAllowedActionGarant;
        $objekt->allowedUnassignFromVlastnik = $this->vlastnikAllowedUnassignObject;

        // echo "<pre>" . var_export($dotaz_final, true) . "</pre>";

        $dotaz = pg_query($this->conn_pgsql, $dotaz_final);

        if($dotaz !== false) {
            $radku = pg_num_rows($dotaz);
        } else {
            $output .= "<div style=\"color: red;\">Dotaz selhal! ". pg_last_error($this->conn_pgsql). "</div>";
        }

        if($radku == 0) {
            $output .= "<tr><td><span style=\"color: red; \" >Nenalezeny žádné odpovídající výrazy dle hledaného \"".$sql."\". </span></td></tr>";
        } else {

            while($data = pg_fetch_array($dotaz)) {
                $output .= "<tr><td colspan=\"16\"> <br> </td> </tr>
                            <tr>
                            <td class=\"vlastnici-td-black\"><br></td>
                            <td class=\"vlastnici-td-black\" colspan=\"3\" width=\"\" >
                            
                            id: [".$data["id_cloveka"]."]".

                                    ", Účetní index: [";

                if($data["archiv"] == 1) {
                    $output .= "27VYŘ";
                } elseif((($data["billing_freq"] == 1) and ($data["fakturacni"] > 0))) {
                    $output .= "37";
                } elseif($data["billing_freq"] == 1) { //ctvrtletni fakturace
                    $output .= "47";
                } elseif(($data["fakturacni"] > 0)) { //faturacni
                    $output .= "27";
                } else {  //domaci uzivatel
                    $output .= "27DM";
                }

                $output .=  sprintf("%05d", $data["ucetni_index"]);

                $output .= "], Splatnost ke dni: [".$data["splatnost"]."]</td>
	    
                            <td class=\"vlastnici-td-black\" colspan=\"2\">VS: ".$data["vs"]."</td>
                        
                            <td class=\"vlastnici-td-black\" colspan=\"4\"> Platit (bez DPH): ".$data["k_platbe"]."</td>
                            <td class=\"vlastnici-td-black\" colspan=\"6\" align=\"right\" width=\"\" >";

                $output .= "<table border=\"0\" width=\"70%\" > <tr> <td class=\"vlastnici-td-black\" width=\"\" >";

                // sem mazani
                if($this->vlastnici_erase_povolen === false) {
                    $output .= "<span style=\"\" > smazat </span> ";
                } else {
                    $output .= "<form method=\"POST\" action=\"vlastnici2-erase.php\" >";
                    $output .= "<input type=\"hidden\" name=\"erase_id\" value=\"".$data["id_cloveka"]."\" >";
                    $output .= "<input type=\"submit\" value=\"Smazat\" >"."</form> \n";
                }

                $output .= "</td>
                            <td class=\"vlastnici-td-black\" >";

                // 6-ta update
                if($this->vlastnici_update_povolen === false) {
                    $output .= "<span style=\"\" >  upravit  </span> \n";
                } else {
                    $output .= " <form method=\"POST\" action=\"/vlastnici2/change\" >";
                    $output .= $this->csrf_html;
                    $output .= "<input type=\"hidden\" name=\"update_id\" value=\"".$data["id_cloveka"]."\" >";
                    $output .= "<input type=\"submit\" value=\"update\" ></form> \n";
                }

                $output .= "</td> </tr></table>";
                $output .= "</td></tr>";

                $output .= "<tr>";
                $output .= "<td class=\"vlastnici-td-black\" ><br></td>";
                $output .= "<td class=\"vlastnici-td-black\" colspan=\"1\">Datum podpisu:  ";

                if ((strlen($data["datum_podpisu"]) > 0)) {
                    list($datum_podpisu_rok, $datum_podpisu_mesic, $datum_podpisu_den) = explode("-", $data["datum_podpisu"]);
                    $datum_podpisu = $datum_podpisu_den.".".$datum_podpisu_mesic.".".$datum_podpisu_rok;
                    $output .= $datum_podpisu;
                }

                $output .= "</td>";

                $output .= "<td class=\"vlastnici-td-black\" colspan=\"1\">Četnost Fa: ";
                if($data["billing_freq"] == 0) {
                    $output .= "měsíční";
                } elseif($data["billing_freq"] == 1) {
                    $output .= "čtvrtletní";
                } else {
                    $output .= "N/A";
                }

                $output .= "</td>";

                $output .= "<td class=\"vlastnici-td-black\" colspan=\"6\">Fakt. skupina: ";

                $fakturacni_skupina_id = $data["fakturacni_skupina_id"];

                $dotaz_fakt_skup = $this->conn_mysql->query("SELECT nazev, typ FROM fakturacni_skupiny WHERE id = '".intval($fakturacni_skupina_id)."' ");
                $dotaz_fakt_skup_radku = $dotaz_fakt_skup->num_rows;

                if(($dotaz_fakt_skup_radku < 1)) {
                    $output .= " [žádná fakt. skupina] ";
                } else {
                    while($data_fakt_skup = $dotaz_fakt_skup->fetch_array()) {
                        $nazev_fakt_skup = $data_fakt_skup["nazev"];
                        $typ_fakt_skup = $data_fakt_skup["typ"];
                    }

                    $output .= " [".$nazev_fakt_skup;
                    if ($typ_fakt_skup == 2) {
                        $output .= " (FÚ) ";
                    } else {
                        $output .= " (DÚ) ";
                    }
                    $output .= "] ";

                }

                $output .= " </td>";
                $output .= "<td class=\"vlastnici-td-black\" colspan=\"7\">";

                $output .= "Smlouva: ";

                if($data["typ_smlouvy"] == 0) {
                    $output .= "[nezvoleno]";
                } elseif($data["typ_smlouvy"] == 1) {
                    $output .= "[na dobu neurčitou]";
                } elseif($data["typ_smlouvy"] == 2) {
                    $output .= "[s min. dobou plnění]"." ( do: ";
                    list($trvani_do_rok, $trvani_do_mesic, $trvani_do_den) = explode("-", $data["trvani_do"]);
                    $trvani_do = $trvani_do_den.".".$trvani_do_mesic.".".$trvani_do_rok;

                    $output .= $trvani_do." )";
                } else {
                    $output .= "[nelze zjistit]";
                }

                $output .= "</td>";
                $output .= "</tr>";

                //zde treti radek
                $output .= "<tr>\n";
                $output .= "<td class=\"vlastnici-td-black\" ><br></td>\n";
                $output .= "<td class=\"vlastnici-td-black\" colspan=\"1\">
		                    <div style=\"float: left; \">Pozastavené fakturace:</div>  ";

                $output .= "<div style=\"text-align: right; padding-right: 20px;\">";

                if($data["billing_suspend_status"] == 1) {
                    $output .= "Ano";
                } elseif($data["billing_suspend_status"] == 0) {
                    $output .= "Ne";
                }

                $output .= "</div>";
                $output .= "</td>";

                if($data["billing_suspend_status"] == 1) {
                    //dalsi info o pozast. fakturacich

                    $output .= "<td class=\"vlastnici-td-black\">od kdy: <span style=\"padding-left: 20px;\">";
                    if((strlen($data["billing_suspend_start"]) > 0) or ($data["billing_suspend_start"] != null)) {
                        $output .= htmlspecialchars($data["billing_suspend_start_f"]);
                    } else {
                        $output .= "není zadáno";
                    }

                    $output .= "</span></td>";

                    //doba
                    $output .= "<td class=\"vlastnici-td-black\" colspan=\"3\">do kdy: <span style=\"padding-left: 20px;\">";

                    if((strlen($data["billing_suspend_stop"]) > 0) or ($data["billing_suspend_stop"] != null)) {
                        $output .= htmlspecialchars($data["billing_suspend_stop_f"]);
                    } else {
                        $output .= " není zadáno ";
                    }

                    $output .= "</span></td>";

                    //důvod
                    $output .= "<td class=\"vlastnici-td-black\" colspan=\"5\">důvod: <span style=\"padding-left: 20px;\">";

                    if(strlen($data["billing_suspend_reason"]) == 0) {
                        $output .= "není zadáno";
                    } else {
                        $output .= htmlspecialchars($data["billing_suspend_reason"]);
                    }

                    $output .= "</span></td>";

                } else {
                    $output .= "<td class=\"vlastnici-td-black\" colspan=\"9\">&nbsp;</td>";
                }

                $output .= "</tr>";

                $output .= " 
                            <tr> 
                            <td><br></td>
                            <td colspan=\"3\" >".$data["jmeno"]." ".$data["prijmeni"]."<br>
                            ".$data["ulice"]." ";

                $output .= "<a href=\"http://www.mapy.cz?query=".$data["ulice"].",".$data["mesto"]."\" target=\"_blank\" >ukaž na mapě</a>";

                $output .= "<br>".$data["mesto"]." ".$data["psc"]."</td>
                            <td colspan=\"6\" >";

                //druhy sloupec - pomyslny
                $output .= "icq: ".$data["icq"]." <br>
                            mail: ".$data["mail"]." <br>
                            tel: ".$data["telefon"]." </td>";

                //treti sloupec - sluzby
                $output .= "<td colspan=\"\" valign=\"top\" >";

                if($data["sluzba_int"] == 1) {
                    $output .= "<div style=\"\" ><span style=\"font-weight: bold; \"><span style=\"color: #ff6600; \" >Služba Internet</span> - aktivní </span>";
                    if($data["sluzba_int_id_tarifu"] == 999) {
                        $output .= "<span style=\"color: gray; \" >- tarif nezvolen</span></div>";
                    } else {
                        $output .= " (<a href=\"/admin/tarify?id_tarifu=".$data["sluzba_int_id_tarifu"]."\" >tarif)</a></div>";
                    }

                    $sluzba_int_aktivni = "1";
                } else {
                    $sluzba_int_aktivni = "0";
                }

                if($data["sluzba_iptv"] == 1) {
                    $output .= "<div style=\"float: left;\" >".
                    "<span style=\"font-weight: bold; \"><span style=\"color: #00cbfc; \" >Služba IPTV</span> - aktivní </span>";

                    if($data["sluzba_iptv_id_tarifu"] == 999) {
                        $output .= "<span style=\"color: gray; \" >- tarif nezvolen</span></div>";
                    } else {
                        $output .= " (<a href=\"admin-tarify-iptv.php?id_tarifu=".$data["sluzba_iptv_id_tarifu"]."\" >tarif)</a></div>";
                    }

                    $sluzba_iptv_aktivni = "1";

                    //link portál
                    // $mq_prefix = mysql_query("SELECT value FROM settings WHERE name LIKE 'iptv_portal_sub_code_prefix' ");
                    // $iptv_prefix_name = mysql_result($mq_prefix, 0, 0);
                    $iptv_prefix_name = "";

                    $output .= "<div style=\"float: left; padding-left: 15px; \" >";
                    $output .= "<a href=\"http://app01.cho01.iptv.grapesc.cz:9080/admin/admin/provisioning/".
                    "subscriber-search.html?type=SUBSCRIBER_CODE&subscriptionNewState=&subscriptionStbAccountState=".
                    "&localityId=&offerId=&submit=OK&searchText=".urlencode($iptv_prefix_name.$data["prijmeni"])."\" target=\"_new\" >".
                    "<img src=\"/img2/Letter-P-icon-small.png\" alt=\"letter-p-small\" width=\"20px\" >".
                    "</a>";
                    $output .= "</div>";

                    $output .= "<div style=\"clear: both; \"></div>";

                } else {
                    $sluzba_iptv_aktivni = "0";
                }

                if($data["sluzba_voip"] == 1) {
                    $output .= "<div><span style=\"font-weight: bold;\" ><span style=\"color: #e42222; \" >Služba VoIP</span> - aktivní </span>";

                    /*if( $data["sluzba_iptv_id_tarifu"] == 999 )
                    { $output .= "<span style=\"color: gray; \" >- tarif nezvolen</span></div>"; }
                    else
                    { $output .= " (<a href=\"\" >tarif)</a></div>"; }
                    */

                    $sluzba_voip_aktivni = "1";
                } else {
                    $sluzba_voip_aktivni = "0";
                }

                if(($sluzba_int_aktivni != 1) and ($sluzba_iptv_aktivni != 1) and ($sluzba_voip_aktivni != 1)) {
                    $output .= "<div style=\"color: Navy; font-weight: bold; \" >Žádná služba není aktivovaná</div>";
                } else {
                }

                //$output .= "<hr class=\"cara3\" />";
                $output .= "<div style=\"border-bottom: 1px solid gray; width: 220px; \" ></div>";

                if(($sluzba_int_aktivni != 1) and ($sluzba_iptv_aktivni != 1) and ($sluzba_voip_aktivni != 1)) {
                    $output .= "<div style=\"color: #555555; \" >Všechny služby dostupné</div>";
                } else {
                    if($sluzba_int_aktivni != 1) {
                        $output .= "<div style=\"\" ><span style=\"color: #ff6600; \" >Služba Internet</span>";
                        $output .= "<span style=\"color: #555555; \"> - dostupné </span></div>";
                    } else {
                    }

                    if($sluzba_iptv_aktivni != 1) {
                        $output .= "<div style=\"\" ><span style=\"color: #27b0db; \" >Služba IPTV</span>";
                        $output .= "<span style=\"color: #555555; \"> - dostupné </span></div>";
                    } else {
                    }

                    if($sluzba_voip_aktivni != 1) {
                        $output .= "<div style=\"\" ><span style=\"color: #e42222; \" >Služba VoIP</span>";
                        $output .= "<span style=\"color: #555555; \"> - dostupné </span></div>";
                    } else {
                    }

                }

                $output .= "</td>";
                $output .= "</tr>"; //konec radku

                $id = $data["id_cloveka"];
                $id_v = $id;

                $id_f = $data["fakturacni"];

                // tady asi bude generovani fakturacnich udaju
                if(($id_f > 0)) {
                    $fakturacni = new \App\Customer\fakturacni($this->container);

                    $fakturacni->firma = $data['firma'];

                    $output .= $fakturacni->vypis($id_f, $id_v);
                }

                $pocet_wifi_obj = $objekt->zjistipocet(1, $id);

                $pocet_fiber_obj = $objekt->zjistipocet(2, $id);

                // echo "<pre>pocty objs: " . $pocet_wifi_obj . " a " . $pocet_fiber_obj . "</pre>";

                if($pocet_wifi_obj > 0) {
                    //objekty wifi
                    $co = "3";

                    $output .= "<tr>
                                <td colspan=\"1\" bgcolor=\"#99FF99\" align=\"center\" >W
                                <td colspan=\"10\" bgcolor=\"#99FF99\" >";
                    $output .= "<table border=\"0\" width=\"100%\" >";

                    $output .= $objekt->vypis($sql, $co, $id);

                    $output .= "</table>";
                    $output .= "</td></tr>";
                }

                if($pocet_fiber_obj > 0) {
                    //objekty fiber
                    $co = "4";

                    $output .= "<tr>";
                    $output .= "<td colspan=\"1\" bgcolor=\"fbbc86\" align=\"center\" >F</td>";
                    $output .= "<td colspan=\"10\" bgcolor=\"fbbc86\" >";

                    $output .= "<table border=\"0\" width=\"100%\" >";

                    $output .= $objekt->vypis($sql, $co, $id);

                    $output .= "</table>";
                    $output .= "</td></tr>";
                }

                //stb
                $stb = new App\Core\stb($this->container);

                $stb->enable_modify_action = $this->objektStbListAllowedActionUpdate;
                $stb->enable_delete_action = $this->objektStbListAllowedActionErase;
                $stb->csrf_html = $this->csrf_html;

                $pocet_stb = $stb->zjistipocetobj($id);

                if($pocet_stb > 0) {
                    $output .= "<tr>";
                    $output .= "<td colspan=\"1\" bgcolor=\"#c1feff\" align=\"center\" >S</td>\n";
                    $output .= "<td colspan=\"10\" bgcolor=\"#c1feff\" valign=\"center\" >\n";

                    $output .= "<table border=\"0\" width=\"100%\" >\n";

                    $output .= $stb->vypis("1", $id);

                    $output .= "</table>\n";
                    $output .= "</td></tr>\n";
                }

                //tady dalsi radka asi
                /*
                $voip = new voip();
                $id_vlastnika = $data["id_cloveka"];

                $dotaz_sql = "SELECT * FROM voip_cisla WHERE id_vlastnika = '$id_vlastnika' ";
                $voip_radku = $voip->vypis_cisla_query($dotaz_sql);

                if ( $voip_radku > 0)
                {
                $output .= "<tr>";
                $output .= "<td colspan=\"14\" ><div style=\"padding-top: 10px; padding-bottom: 10px; \">";

                $voip->vypis_cisla("2");

                $output .= "</div></td>";
                $output .= "</tr>";
                }
                */

                //druha radka
                $output .= "<tr>";
                $output .= "<td colspan=\"14\">";

                $output .= "<table border=\"0\" width=\"100%\" >";
                $output .= "<tr>";

                $orezano = explode(':', $data["pridano"]);
                $pridano = $orezano[0].":".$orezano[1];

                $output .= "<td colspan=\"1\" width=\"250px\" >";
                $output .= "<span style=\"margin: 20px; \">datum přidání: ".$pridano." </span>";
                $output .= "</td>";

                $output .= "<td align=\"center\" >";
                $output .= " <img title=\"poznamka\" src=\"img2/poznamka3.png\" align=\"middle\" ";
                $output .= " onclick=\"window.alert(' poznámka: ".$data["poznamka"]." ');\" >";
                $output .= "</td>";

                $output .= "<td>
                                <span style=\"\">vyberte akci: </span>
                            </td>";

                $output .= "<td colspan=\"1\">";

                if($this->cross_url != null) {
                    $output .= "<form action=\"" . $this->cross_url . "\" method=\"get\" >";

                } else {
                    $output .= "<form action=\"\" method=\"get\" >";
                    $this->logger->error(__CLASS__ . "\\" . __FUNCTION__ . ": unknown cross_url");
                }

                $output .= "<select name=\"akce\" size=\"1\" >";

                $output .= "<option value=\"0\" class=\"select-nevybrano\" >Nevybráno</option>";

                $output .= "<optgroup label=\"objekty\">";
                $output .= "<option value=\"1\" ";
                if($_GET["akce"] == 1) {
                    $output .= " selected ";
                } $output .= " > přiřadit objekt </option>";
                $output .= "<option value=\"15\" ";
                if($_GET["akce"] == 15) {
                    $output .= " selected ";
                } $output .= " > přiřadit objekt STB</option>";

                $output .= "</optgroup>";

                $output .= "<optgroup label=\"fakturacni adresa\">";
                $output .= "<option value=\"2\" ";
                if($_GET["akce"] == 2) {
                    $output .= " selected ";
                } $output .= " >přidání fakturační adresy </option>";
                $output .= "<option value=\"3\" ";
                if($_GET["akce"] == 3) {
                    $output .= " selected ";
                } $output .= " >smazání fakturační adresy </option>";
                $output .= "<option value=\"4\" ";
                if($_GET["akce"] == 4) {
                    $output .= " selected ";
                } $output .= " >úprava fakturační adresy </option>";
                $output .= "</optgroup>";

                $output .= "<optgroup label=\"Závady/opravy\" >";
                $output .= "<option value=\"5\" ";
                if($_GET["akce"] == 5) {
                    $output .= " selected ";
                } $output .= " >Vložit závadu/opravu</option>";
                $output .= "<option value=\"6\" ";
                if($_GET["akce"] == 6) {
                    $output .= " selected ";
                } $output .= " >zobrazit závady/opravy</option>";
                $output .= "</optgroup>";

                $output .= "<optgroup label=\"Smlouvy/výpovědi\" >";
                $output .= "<option value=\"7\" ";
                if($_GET["akce"] == 7) {
                    $output .= " selected ";
                } $output .= " >Tisk smlouvy</option>";
                $output .= "<option value=\"8\" ";
                if($_GET["akce"] == 8) {
                    $output .= " selected ";
                } $output .= " >Vložit zádost o výpověď</option>";
                $output .= "</optgroup>";

                $output .= "<optgroup label=\"Platby/faktury\" >";
                //    $output .= "<option value=\"9\" "; if( $_GET["akce"] == 9) $output .= " selected "; $output .= " >Vložit hotovostní platbu</option>";
                $output .= "<option value=\"10\" ";
                if($_GET["akce"] == 10) {
                    $output .= " selected ";
                } $output .= " >Výpis plateb za internet</option>";
                $output .= "<option value=\"11\" ";
                if($_GET["akce"] == 11) {
                    $output .= " selected ";
                } $output .= " >Výpis všech neuhrazených faktur</option>";
                //    $output .= "<option value=\"12\" "; if( $_GET["akce"] == 12) $output .= " selected "; $output .= " >online faktury (XML) - Internet</option>";
                //    $output .= "<option value=\"14\" "; if( $_GET["akce"] == 14) $output .= " selected "; $output .= " >online faktury (XML) - VoIP (hlas)</option>";
                $output .= "<option value=\"16\" ";
                if($_GET["akce"] == 16) {
                    $output .= " selected ";
                } $output .= " >Výpis faktur/Plateb (Pohoda SQL)</option>";

                $output .= "</optgroup>";

                $output .= "<optgroup label=\"Historie\" >";
                $output .= "<option value=\"13\" ";
                if($_GET["akce"] == 13) {
                    $output .= " selected ";
                } $output .= " >Zobrazení historie</option>";
                $output .= "</optgroup>";

                $output .= "</select>";

                $output .= "<span style=\"padding-left: 20px;\" >
		    	      <input type=\"submit\" name=\"odeslat\" value=\"OK\">
			    </span>";

                $output .= "<input type=\"hidden\" name=\"id_cloveka\" value=\"".$data["id_cloveka"]."\">";

                $output .= "</form>";

                $output .= "</td>";
                $output .= "</tr></table>";

                $output .= "</td>";
                $output .= "</tr>";

                /*
                $output .= "<tr>";
                $output .= "<td colspan=\"10\" >";


                $output .= "</td>";
                $output .= "</tr>";
                */

                //konec while
            }

            // konec else
        }

        return $output;
        // konec funkce vypis
    }

    public function export()
    {
        // tafy generovani exportu
        if($this->export_povolen === true) {

            // $fp = fopen("export/vlastnici-sro.xls", "w");   // Otevřeme soubor tabulka.xls, pokud existuje, bude smazán, jinak se vytvoří nový sobor

            $fp = false;
            if($fp === false) {
                // echo "<div style=\"color: red; font-weight: bold; \">Chyba: Soubor pro export nelze otevřít </div>\n";
                // @phpstan-ignore-next-line
            } else {
                fputs($fp, "<table border='1'> \n \n");   // Zapíšeme do souboru začátek tabulky

                fputs($fp, "<tr>");   // Zapíšeme do souboru začátek řádky, kde budou názvy sloupců (polí)

                $vysledek_pole = pg_query($this->conn_pgsql, "SELECT column_name FROM information_schema.columns WHERE table_name ='vlastnici' ORDER BY ordinal_position ");
                // Vybereme z databáze názvy polí tabulky tabulka a postupně je zapíšeme do souboru

                // echo "vysledek_pole: $vysledek_pole ";

                while ($vysledek_array_pole = pg_fetch_row($vysledek_pole)) {
                    fputs($fp, "<td><b> ".$vysledek_array_pole[0]." </b></td> \n");
                }

                fputs($fp, "<td><b> id_f </b></td> \n");
                fputs($fp, "<td><b> f. jméno </b></td> \n");
                fputs($fp, "<td><b> f. ulice </b></td> \n");
                fputs($fp, "<td><b> f. mesto </b></td> \n");
                fputs($fp, "<td><b> f. PSČ </b></td> \n");
                fputs($fp, "<td><b> f. ičo </b></td> \n");
                fputs($fp, "<td><b> f. dič </b></td> \n");
                fputs($fp, "<td><b> f. účet </b></td> \n");
                fputs($fp, "<td><b> f. splatnost </b></td> \n");
                fputs($fp, "<td><b> f. cetnost </b></td> \n");

                fputs($fp, "</tr>");   // Zapíšeme do souboru konec řádky, kde jsou názvy sloupců (polí)

                // $vysledek=pg_query("select * from platby where hotove='1' ");
                // Vybereme z databáze všechny záznamy v tabulce tabulka a postupě je zapíšeme do souboru

                $vysledek = pg_query($this->conn_pgsql, "SELECT * FROM vlastnici WHERE (archiv ='0' OR archiv is NULL) ORDER BY id_cloveka ASC");

                while ($data = pg_fetch_array($vysledek)) {
                    fputs($fp, "\n <tr>");

                    fputs($fp, "<td> ".$data["id_cloveka"]."</td> ");
                    fputs($fp, "<td> ".$data["nick"]."</td> ");
                    fputs($fp, "<td> ".$data["jmeno"]."</td> ");
                    fputs($fp, "<td> ".$data["prijmeni"]."</td> ");
                    fputs($fp, "<td> ".$data["ulice"]."</td> ");
                    fputs($fp, "<td> ".$data["mesto"]."</td> ");
                    fputs($fp, "<td> ".$data["psc"]."</td> ");
                    fputs($fp, "<td> ".$data["icq"]."</td> ");
                    fputs($fp, "<td> ".$data["mail"]."</td> ");
                    fputs($fp, "<td> ".$data["telefon"]."</td> ");
                    fputs($fp, "<td> ".$data["poznamka"]."</td> ");
                    fputs($fp, "<td> ".$data["zaplaceno"]."</td> ");
                    fputs($fp, "<td> ".$data["fakturacni"]."</td> ");
                    fputs($fp, "<td> ".$data["vs"]."</td> ");
                    fputs($fp, "<td> ".$data["k_platbe"]."</td> ");
                    fputs($fp, "<td> ".$data["firma"]."</td> ");

                    fputs($fp, "<td> ".$data["pridano"]."</td> ");
                    fputs($fp, "<td> ".$data["ucetni_index"]."</td> ");
                    fputs($fp, "<td> ".$data["archiv"]."</td> ");
                    fputs($fp, "<td> ".$data["fakturacni_skupina_id"]."</td> ");

                    fputs($fp, "<td> ".$data["splatnost"]."</td> ");
                    fputs($fp, "<td> ".$data["typ_smlouvy"]."</td> ");
                    fputs($fp, "<td> ".$data["trvani_do"]."</td> ");
                    fputs($fp, "<td> ".$data["datum_podpisu"]."</td> ");

                    fputs($fp, "<td> ".$data["sluzba_int"]."</td> ");
                    fputs($fp, "<td> ".$data["sluzba_iptv"]."</td> ");

                    fputs($fp, "<td> ".$data["sluzba_voip"]."</td> ");
                    fputs($fp, "<td> ".$data["sluzba_int_id_tarifu"]."</td> ");
                    fputs($fp, "<td> ".$data["sluzba_iptv_id_tarifu"]."</td> ");
                    fputs($fp, "<td> ".$data["sluzba_voip_fa"]."</td> ");

                    fputs($fp, "<td> ".$data["billing_freq"]."</td> ");

                    fputs($fp, "<td> ".$data["billing_suspend_status"]."</td> ");
                    fputs($fp, "<td> ".$data["billing_suspend_length"]."</td> ");
                    fputs($fp, "<td> ".$data["billing_suspend_reason"]."</td> ");
                    fputs($fp, "<td> ".$data["billing_suspend_start"]."</td> ");

                    if ($data["fakturacni"] > 0) {
                        $id_f = $data["fakturacni"];

                        $vysl_f = pg_query($this->conn_pgsql, "SELECT * FROM fakturacni WHERE id = '".intval($id_f)."' ");

                        while ($data_f = pg_fetch_array($vysl_f)) {

                            fputs($fp, "<td> ".$data_f["id"]."</td> ");
                            fputs($fp, "<td> ".$data_f["ftitle"]."</td> ");
                            fputs($fp, "<td> ".$data_f["fulice"]."</td> ");
                            fputs($fp, "<td> ".$data_f["fmesto"]."</td> ");
                            fputs($fp, "<td> ".$data_f["fpsc"]."</td> ");
                            fputs($fp, "<td> ".$data_f["ico"]."</td> ");
                            fputs($fp, "<td> ".$data_f["dic"]."</td> ");
                            fputs($fp, "<td> ".$data_f["ucet"]."</td> ");
                            fputs($fp, "<td> ".$data_f["splatnost"]."</td> ");
                            fputs($fp, "<td> ".$data_f["cetnost"]."</td> ");

                        }

                    }

                    fputs($fp, "</tr> \n ");
                    // echo "vysledek_array: ".$vysledek_array[$i];

                } //konec while

                fputs($fp, "</table>");   // Zapíšeme do souboru konec tabulky

                fclose($fp);   // Zavřeme soubor

            } //konec else if fp === true

        } //konec if export_povolen

    } //end of function export
}
