<?php

class vlastnik
{
    public $conn_mysql;

    public $conn_pgsql;

    public $csrf_html;

    public $echo = true;

    public $vlastnici_erase_povolen = false;

    public $vlastnici_update_povolen = false;

    public $odendani_povoleno = false;

    public $objekt_update_povolen = false;
    public $objekt_mazani_povoleno = false;
    public $objekt_garant_akce = false;

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

        if($this->echo) {
            echo $output;
        } else {
            return $output;
        }
    }

    public function vypis($sql, $co, $mod, $dotaz_source)
    {
        $output = "";

        $objekt = new objekt_a2();
        $objekt->conn_mysql = $this->conn_mysql;
        $objekt->conn_pgsql = $this->conn_pgsql;
        $objekt->echo = false;
        $objekt->csrf_html = $this->csrf_html;

        $objekt->listAllowedActionUpdate = $this->objekt_update_povolen;
        $objekt->listAllowedActionErase = $this->objekt_mazani_povoleno;
        // $objekt-> = $this->objektListAllowedActionGarant;
        $objekt->allowedUnassignFromVlastnik = $this->odendani_povoleno;

        // co - co hledat, 1- podle dns, 2-podle ip , 3 - dle id_vlastnika
        $dotaz = pg_query($this->conn_pgsql, $dotaz_source);

        if($dotaz !== false) {
            $radku = pg_num_rows($dotaz);
        } else {
            $output .= "<div style=\"color: red;\">Dotaz selhal! ". pg_last_error($this->conn_pgsql). "</div>";
        }

        if ($radku == 0) {
            $output .= "<tr><td><span style=\"color: red; \" >Nenalezeny žádné odpovídající výrazy dle hledaného \"".$sql."\". </span></td></tr>";
        } else {

            while($data = pg_fetch_array($dotaz)) {

                $output .= "<tr><td colspan=\"14\"> <br> </td> </tr>
                    <tr> <td class=\"vlastnici-td-black\" colspan=\"2\" >[".$data["id_cloveka"]."] ".$data["nick"]."</td>
                    <td class=\"vlastnici-td-black\" colspan=\"2\">VS: ".$data["vs"]."</td>
                    <td class=\"vlastnici-td-black\" colspan=\"4\"> Platit (bez DPH): ".$data["k_platbe"]."</td>
                    <td class=\"vlastnici-td-black\" colspan=\"6\" align=\"right\" width=\"8%\" >";

                $output .= "<table border=\"0\" width=\"70%\" > <tr> <td class=\"vlastnici-td-black\" width=\"\" >";

                // sem mazani
                if($this->vlastnici_erase_povolen === false) {
                    $output .= "<span style=\"\" > smazat </span> ";
                } else {
                    $output .= "<form method=\"POST\" action=\"vlastnici2-erase.php\" >";
                    $output .= "<input type=\"hidden\" name=\"erase_id\" value=\"".$data["id_cloveka"]."\" >";
                    $output .= "<input type=\"submit\" value=\"Smazat\" >";

                    $output .= "</form> \n";
                }

                $output .= "</td><td class=\"vlastnici-td-black\" >";

                // 6-ta update
                if ($this->vlastnici_update_povolen === false) {
                    $output .= "<span style=\"\" >  upravit  </span> \n";
                } else {
                    $output .= " <form method=\"POST\" action=\"vlastnici2-change.php\" >";
                    $output .= "<input type=\"hidden\" name=\"update_id\" value=\"".$data["id_cloveka"]."\" >";
                    $output .= "<input type=\"submit\" value=\"update\" >";

                    $output .= "</form> \n";
                }

                $output .= "</td> </tr> </table>";

                $output .= " </td> 
                        </tr>
                        
                        <tr> <td colspan=\"2\">".$data["jmeno"]." ".$data["prijmeni"]."<br>";

                $output .= $data["ulice"]."  ";

                $output .= "<a href=\"http://www.mapy.cz?query=".$data["ulice"].",".$data["mesto"]."\" target=\"_blank\" >ukaž na mapě</a>";

                $output .= " <br> ".$data["mesto"]." ".$data["psc"]."</td>";

                $output .= "<td colspan=\"11\">icq: ".$data["icq"]." <br>
                        mail: ".$data["mail"]." <br>
                        tel: ".$data["telefon"]." </td>
                        
                        </tr>";

                $id = $data["id_cloveka"];
                $id_f = $data["fakturacni"];

                // tady asi bude generovani fakturacnich udaju
                if (($id_f > 0)) {
                    $fakturacni = new fakturacni();
                    $fakturacni->echo = false;

                    $output .= $fakturacni->vypis($id_f, $id);
                }

                $pocet_wifi_obj = $objekt->zjistipocet(1, $id);

                $pocet_fiber_obj = $objekt->zjistipocet(2, $id);

                if($pocet_wifi_obj > 0 or $pocet_fiber_obj == 0) {
                    //objekty wifi
                    $co = "3";

                    $output .= "<tr>
                        <td colspan=\"9\" bgcolor=\"#99FF99\" >";
                    $output .= "<table border=\"0\" width=\"100%\" >";

                    $output .= $objekt->vypis($sql, $co, $id, "");

                    $output .= "</table>";
                    $output .= "</td></tr>";
                }

                if($pocet_fiber_obj > 0) {

                    //objekty fiber
                    $co = "4";

                    $output .= "<tr><td colspan=\"9\" bgcolor=\"fbbc86\"  >";

                    // $output .= "<tr>";
                    // $output .= "<td colspan=\"1\" bgcolor=\"fbbc86\" align=\"center\" >F</td>";
                    // $output .= "<td colspan=\"10\" bgcolor=\"fbbc86\" >";

                    $output .= "<table border=\"0\" width=\"100%\" >";

                    $output .= $objekt->vypis($sql, $co, $id);

                    $output .= "</table>";

                    $output .= "</td></tr>";
                }


                //tady dalsi radka asi
                /*
                $voip = new voip();

                $id_vlastnika = $data["id_cloveka"];

                //$dotaz_sql = "SELECT * FROM voip_cisla WHERE id_vlastnika = '".intval($id_vlastnika)."' ";

                //$voip_radku = $voip->vypis_cisla_query($dotaz_sql);

                if ( $voip_radku > 0)
                {
                $output .= "<tr>";

                $output .= "<td colspan=\"14\" ><div style=\"padding-top: 10px; padding-bottom: 10px; \">";

                $voip->vypis_cisla("2");

                $output .= "</div></td>";

                $output .= "</tr>\n\n";

                }
                */

                $output .= "<tr>\n";

                $output .= "<td colspan=\"14\">";

                $output .= "<span style=\"margin: 25px; \">další funkce:</span>\n\n";

                $output .= "<a href=\"" . fix_link_to_another_adminator("/vlastnici2-add-obj.php?mod=1&id_vlastnika=".$data["id_cloveka"])."\" >přidání objektu</a>";

                $output .= "<span style=\"margin: 25px; \"></span>";

                $output .= "<a href=\"" . fix_link_to_another_adminator("/platby-vypis.php?id_vlastnika=".$data["id_cloveka"]) . "\" > výpis plateb - starý (do 2/2012)</a>";

                $output .= "<span style=\"margin-left: 20px; \">".
                            "<a href=\"" . fix_link_to_another_adminator("/pohoda_sql/phd_list_fa.php?id_vlastnika=".$data["id_cloveka"]) . "\" > výpis plateb - nový (od 3/2012)</a>".
                            "</span>";

                $output .= "<span style=\"margin: 15px; \"></span>";

                if ($data["fakturacni"] > 0) {
                    $output .= " přidání fakturační adresy ";
                } else {
                    $output .= "<a href=\"" . fix_link_to_another_adminator("/vlastnici2-add-fakt.php?id_vlastnika=".$data["id_cloveka"]) . "\" > přidání fakt. adresy </a>";
                }

                $output .= "<span style=\"margin: 15px; \"></span>";

                if ($data["fakturacni"] > 0) {
                    $output .= "<a href=\"" . fix_link_to_another_adminator("/vlastnici2-erase-f.php?id=".$data["fakturacni"]) . "\" > smazání fakt. adresy </a>";
                } else {
                    $output .= " smazání fakt. adresy " ;
                }

                $output .= "<span style=\"margin: 15px; \" ></span>";

                if (($data["fakturacni"] > 0)) {
                    $output .= "<a href=\"" . fix_link_to_another_adminator("/vlastnici2-change-fakt.php?id=".$data["fakturacni"]) . "\" > úprava fakt. adresy </a>";
                } else {
                    $output .= " úprava fakt. adresy ";
                }

                $output .= "<span style=\"margin: 25px; \" ></span>";

                $output .= "<a href=\"" . fix_link_to_another_adminator("/vlastnici-gen-xml.php?id_klienta=".$data["id_cloveka"]) . "\" > import klienta do Pohody (Adresář)</a>";

                //konec bunky/radky
                $output .= "</td></tr>";

                //druha radka
                $output .= "<tr>";

                $output .= "<td colspan=\"14\" >";

                $output .= "<table border=\"0\" width=\"100%\">";

                //h
                $output .= "<tr>";

                $orezano = explode(':', $data["pridano"]);
                $pridano = $orezano[0].":".$orezano[1];


                $output .= "<td colspan=\"1\" >";

                $output .= "datum přidání: ".$pridano." ";

                $output .= "</td>";

                $output .= "<td align=\"center\" >";

                $output .= " <img title=\"poznamka\" src=\"/img2/poznamka3.png\" align=\"middle\" ";
                $output .= " onclick=\"window.alert(' poznámka: ".$data["poznamka"]." ');\" >";

                $output .= "</td>";

                $output .= "<td colspan=\"1\" >";

                /*
                $output .= "<form method=\"POST\" action=\"platby-akce.php\" >";

                $output .= "<input type=\"hidden\" name=\"firma\" value=\"1\" >";
                $output .= "<input type=\"hidden\" name=\"klient\" value=\"".$data["id_cloveka"]."\" >";

                $output .= "<input type=\"submit\" name=\"akce\" value=\"Vložení hotovostní platby\" >";

                $output .= "</form>";
                */
                $output .= "</td>";

                $output .= "<td colspan=\"1\" >";

                $output .= "<form method=\"POST\" action=\"" . fix_link_to_another_adminator("/vypovedi-vlozeni.php") . "\" >";

                $output .= "<input type=\"hidden\" name=\"firma\" value=\"1\" >";
                $output .= "<input type=\"hidden\" name=\"klient\" value=\"".$data["id_cloveka"]."\" >";

                $output .= "<input type=\"submit\" name=\"akce\" value=\"Vložení žádosti o výpověď\" >";

                $output .= "</form>";
                $output .= "</td>";

                $output .= "<td colspan=\"1\">";

                // zde dalsi veci
                $output .= "<span style=\"color: gray; padding-left: 10px; \" >H: </span>";
                $output .= "<a href=\"/archiv-zmen?id_cloveka=".$data["id_cloveka"]."\">".$data["id_cloveka"]."</a>";

                $output .= "</td>";

                $output .= "<td>
                    <form action=\"" . fix_link_to_another_adminator("/opravy-vlastnik.php") . "\" method=\"get\" >
                    <input type=\"hidden\" name=\"typ\" value=\"2\" >
                    <input type=\"hidden\" name=\"id_vlastnika\" value=\"".$data["id_cloveka"]."\" >
                    
                    <input type=\"submit\" name=\"ok\" value=\"Zobrazit závady/opravy \" ></form>";
                $output .= "</td>";

                $output .= "<td>
                        <form action=\"" . fix_link_to_another_adminator("/opravy-index.php") . "\" method=\"get\" >
                        <input type=\"hidden\" name=\"typ\" value=\"1\" >
                        <input type=\"hidden\" name=\"id_vlastnika\" value=\"".$data["id_cloveka"]."\" >
                        
                    <input type=\"submit\" name=\"ok\" value=\"Vložit závadu/opravu \" ></form>";
                $output .= "</td>";

                $output .= "</tr></table>";

                $output .= "</td>";
                $output .= "</tr>";
            }
        }

        if($this->echo) {
            echo $output;
        } else {
            return $output;
        }
    }
}
