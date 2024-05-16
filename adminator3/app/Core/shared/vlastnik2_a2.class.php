<?php

class vlastnik2_a2
{
    public $conn_mysql;

    public $conn_pgsql;

    public $logger;

    public $container;

    public $csrf_html;

    public $level;

    public $export_povolen = false;

    public $echo = true;

    public $objektListAllowedActionUpdate = false;

    public $objektListAllowedActionErase = false;

    public $objektListAllowedActionGarant = false;

    public $vlastnikAllowedUnassignObject = false;

    public $objektStbListAllowedActionUpdate = false;

    public $objektStbListAllowedActionErase = false;

    public $vlastnici_erase_povolen = false;

    public $vlastnici_update_povolen = false;

    public $cross_url = null;

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

        if($this->echo === true) {
            echo $output;
        } else {
            return $output;
        }

        // konec funkce vypis_tab
    }

    // $dotaz_final - for pq_query
    public function vypis($sql, $co, $dotaz_final)
    {
        // co - co hledat, 1- podle dns, 2-podle ip

        $output = "";

        if (!$this->conn_pgsql) {
            die("An error occurred. The connection with pgsql does not exist.\n <br> (type of handler variable: " . gettype($this->conn_pgsql) . ")");
        }

        if (!$this->conn_mysql) {
            die("An error occurred. The connection with mysql does not exist.\n <br> (type of handler variable: " . gettype($this->conn_mysql) . ")");
        }

        $objekt = new objekt_a2();
        $objekt->echo = false;
        $objekt->logger = $this->logger;
        $objekt->conn_mysql = $this->conn_mysql;
        $objekt->conn_pgsql = $this->conn_pgsql;
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
                    $output .= " <form method=\"POST\" action=\"vlastnici2-change.php\" >";
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
                        $output .= " (<a href=\"admin-tarify.php?id_tarifu=".$data["sluzba_int_id_tarifu"]."\" >tarif)</a></div>";
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
                    $fakturacni = new fakturacni();
                    $fakturacni->echo = false;
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
                if($this-> echo === false) {
                    $stb = new App\Core\stb($this->container);

                    $stb->enable_modify_action = $this->objektStbListAllowedActionUpdate;
                    $stb->enable_delete_action = $this->objektStbListAllowedActionErase;
                    $stb->level = $this->level;
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
                    $output .= "<form action=\"vlastnici-cross.php\" method=\"get\" >";
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

        if($this->echo === true) {
            echo $output;
        } else {
            return $output;
        }

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

                $vysledek_pole = pg_query("SELECT column_name FROM information_schema.columns WHERE table_name ='vlastnici' ORDER BY ordinal_position ");
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

                $vysledek = pg_query("SELECT * FROM vlastnici WHERE (archiv ='0' OR archiv is NULL) ORDER BY id_cloveka ASC");

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

                        $vysl_f = pg_query("SELECT * FROM fakturacni WHERE id = '".intval($id_f)."' ");

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
