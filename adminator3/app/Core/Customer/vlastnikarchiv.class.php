<?php

class vlastnikarchiv
{
    public $conn_mysql;
    public $conn_pgsql;

    public $echo = true;

    public $csrf_html;

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
            $output .= "\n".'<table border="0" width="100%">'."\n";
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

    public function vypis($sql, $co, $dotaz_final)
    {
        $output = "";
        // co - co hledat, 1- podle dns, 2-podle ip

        $dotaz = pg_query($dotaz_final);

        if($dotaz !== false) {
            $radku = pg_num_rows($dotaz);
        } else {
            $output .= "<div style=\"color: red;\">Dotaz selhal! ". pg_last_error(). "</div>";
        }

        if ($radku == 0) {
            $output .= "<tr><td><span style=\"color: red; \" >Nenalezeny žádné odpovídající výrazy dle hledaného \"".$sql."\". </span></td></tr>";
        } else {

            while($data = pg_fetch_array($dotaz)) {
                $output .= "<tr><td colspan=\"14\"> <br> </td> </tr>
                            <tr> <td class=\"vlastnici-td-black\" colspan=\"2\" width=\"\" >id: [".$data["id_cloveka"]."] 
                            nick: [".$data["nick"]."] účetní-index: [".sprintf("%05d", $data["ucetni_index"])."] </td>
                            
                            <td class=\"vlastnici-td-black\" colspan=\"2\">VS: [".$data["vs"]."] ";

                if ($data["firma"] == 1) {
                    $output .= " firma: [Company, s.r.o.]";
                } else {
                    $output .= " firma: [F.O.] ";
                }

                $output .= "</td>	
                        <td class=\"vlastnici-td-black\" colspan=\"4\"> Platit (bez DPH): ".$data["k_platbe"]."</td>
                        <td class=\"vlastnici-td-black\" colspan=\"6\" align=\"right\" width=\"\" >";

                $output .= "<table border=\"0\" width=\"70%\" > <tr> <td class=\"vlastnici-td-black\" width=\"\" >";

                // sem mazani
                if (! ($this->vlastnici_erase_povolen === true)) {
                    $output .= "<span style=\"\" > smazat </span> ";
                } else {
                    $output .= "<form method=\"POST\" action=\"" . fix_link_to_another_adminator("/vlastnici2-erase.php") . "\" >";
                    $output .= "<input type=\"hidden\" name=\"erase_id\" value=\"".$data["id_cloveka"]."\" >";
                    $output .= "<input type=\"submit\" value=\"Smazat\" >";
                    $output .= "</form> \n";
                }
                $output .= "</td><td class=\"vlastnici-td-black\" >";

                // 6-ta update
                if (!($this->vlastnici_update_povolen === true)) {
                    $output .= "<span style=\"\" >  upravit  </span> \n";
                } else {
                    $output .= " <form method=\"POST\" action=\"" . fix_link_to_another_adminator("/vlastnici2-change.php") . "\" >";
                    $output .= "<input type=\"hidden\" name=\"update_id\" value=\"".$data["id_cloveka"]."\" >";
                    $output .= "<input type=\"submit\" value=\"update\" >";
                    $output .= "</form> \n";
                }

                $output .= "</td> </tr> </table>";

                $output .= "</td>
                            </tr>
                        <tr>
                        <td colspan=\"2\">".$data["jmeno"]." ".$data["prijmeni"]."<br>
                        ".$data["ulice"]." ";

                $output .= "<a href=\"http://www.mapy.cz?query=".$data["ulice"].",".$data["mesto"]."\" target=\"_blank\" >ukaž na mapě</a>";


                $output .= "<br>".$data["mesto"]." ".$data["psc"]."</td>
                            <td colspan=\"12\">icq: ".$data["icq"]." <br>
                            mail: ".$data["mail"]." <br>
                            tel: ".$data["telefon"]." </td>
                            </tr>";

                $id = $data["id_cloveka"];
                $id_v = $id;

                $id_f = $data["fakturacni"];

                // tady asi bude generovani fakturacnich udaju

                if (($id_f > 0)) {
                    $fakturacni = new fakturacni();
                    $fakturacni->echo = false;

                    $output .= $fakturacni->vypis($id_f, $id_v);
                }

                // $sql="%";
                $co = "3";

                // $id=$data["id_cloveka"];
                // print "debug: id: $id";
                $objekt_a2 = new objekt_a2();
                $objekt_a2->conn_mysql = $this->conn_mysql;
                $objekt_a2->conn_pgsql = $this->conn_pgsql;
                $objekt_a2->echo = false;

                $output .= $objekt_a2->vypis($sql, $co, $id);

                //tady dalsi radka asi
                $output .= "<tr>";

                $output .= "<td>další funkce: </td>
                        <td colspan=\"13\">";

                //$output .= "<a href=\"vlastnici2-add-obj.php?id_vlastnika=".$data["id_cloveka"]."\">přidání objektu</a>";
                $output .= "<span style=\"color: gray; \">přidání objektu</span>";

                $output .= "<span style=\"margin: 10px; \"></span>";

                $output .= "<a href=\"" . fix_link_to_another_adminator("/platby-vypis.php?id_vlastnika=".intval($data["id_cloveka"])) ."\" > výpis plateb - starý (do 2/2012)</a>";

                $output .= "<span style=\"margin-left: 20px; \">
                            <a href=\"" . fix_link_to_another_adminator("/pohoda_sql/phd_list_fa.php?id_vlastnika=".$data["id_cloveka"]) . "\" > výpis plateb - (od 3/2012)</a>".
                                "</span>";

                $output .= "<span style=\"margin: 10px; \">fakturační adresa:</span>";

                /*
                if ( ( $data["fakturacni"] > 0 ) )
                { $output .= " přidání fakturační adresy "; }
                else
                { $output .= "<a href=\"vlastnici2-add-fakt.php?id_vlastnika=".$data["id_cloveka"]."\" > přidání fakturační adresy </a>"; }
                */
                $output .= "<span style=\"color: grey; \"> přidání</span>";

                $output .= "<span style=\"margin: 25px; \"></span>";

                if (($data["fakturacni"] > 0)) {
                    $output .= "<a href=\"" . fix_link_to_another_adminator("/vlastnici2-erase-f.php?id=".$data["fakturacni"]) . "\" > smazání </a>";
                } else {
                    $output .= " smazání ";
                }

                $output .= "<span style=\"margin: 25px; \" ></span>";

                if (($data["fakturacni"] > 0)) {
                    $output .= "<a href=\"" . fix_link_to_another_adminator("/vlastnici2-change-fakt.php?id=".$data["fakturacni"]) . "\" > úprava </a>";
                } else {
                    $output .= " úprava ";
                }

                $output .= "</td></tr>";

                //druha radka
                $output .= "<tr>";

                // $output .= "<td><br></td>";

                $orezano = explode(':', $data["pridano"]);
                $pridano = $orezano[0].":".$orezano[1];

                $output .= "<td colspan=\"1\" >";

                $output .= "datum přidání: ".$pridano." ";

                $output .= "</td>";

                $output .= "<td align=\"center\" >";

                $output .= " <img title=\"poznamka\" src=\"/img2/poznamka3.png\" align=\"middle\" ";
                $output .= " onclick=\"window.alert(' poznámka: ".$data["poznamka"]." ');\" >";

                $output .= "</td>";

                $output .= "<td colspan=\"2\">";
                $output .= "<form method=\"POST\" action=\"platby-akce.php\" >";

                $output .= "<input type=\"hidden\" name=\"firma\" value=\"2\" >";
                $output .= "<input type=\"hidden\" name=\"klient\" value=\"".$data["id_cloveka"]."\" >";

                $output .= "<input type=\"submit\" name=\"akce\" value=\"Vložení hotovostní platby\" >";

                $output .= "</form>";
                $output .= "</td>";


                $output .= "<td colspan=\"4\">";
                $output .= "<form method=\"POST\" action=\"vypovedi-vlozeni.php\" >";

                $output .= "<input type=\"hidden\" name=\"firma\" value=\"2\" >";
                $output .= "<input type=\"hidden\" name=\"klient\" value=\"".$data["id_cloveka"]."\" >";

                $output .= "<input type=\"submit\" name=\"akce\" value=\"Vložit žádost o výpověď\" >";

                $output .= "</form>";
                $output .= "</td>";

                //$output .= "<td colspan=\"3\"><br></td>";

                $output .= "<td colspan=\"3\">";
                // zde dalsi veci
                $output .= "<span style=\"color: grey; padding-left: 10px; \" >H: </span>";
                $output .= "<a href=\"/archiv-zmen?id_cloveka=".$data["id_cloveka"]."\">".$data["id_cloveka"]."</a>";

                $output .= "</td>";

                $output .= "<td> ";
                //tisk smlouvy
                $output .= "<form method=\"POST\" action=\"/print/smlouva\" >";
                $output .= $this->csrf_html;
                $output .= "<input type=\"hidden\" name=\"ec\" value=\"".$data["vs"]."\" >";
                $output .= "<input type=\"hidden\" name=\"jmeno\" value=\"".$data["jmeno"]." ".$data["prijmeni"]."\" >";
                $output .= "<input type=\"hidden\" name=\"ulice\" value=\"".$data["ulice"]."\" >";
                $output .= "<input type=\"hidden\" name=\"mesto\" value=\"".$data["psc"]." ".$data["mesto"]."\" >";
                $output .= "<input type=\"hidden\" name=\"telefon\" value=\"".$data["telefon"]."\" >";
                $output .= "<input type=\"hidden\" name=\"email\" value=\"".$data["mail"]."\" >";

                if(($data["fakturacni"] > 0)) {
                    $output .= "<input type=\"hidden\" name=\"fakturace\" value=\"2\" >";
                    //$output .= "<input type=\"hidden\" name=\"jmeno\" value=\"".$data["jmeno"]." ".$data["prijmeni"]."\" >";
                    //$output .= "<input type=\"hidden\" name=\"ulice\" value=\"".$data["ulice"]."\" >";
                    //$output .= "<input type=\"hidden\" name=\"mesto\" value=\"".$data["psc"]." ".$data["mesto"]."\" >";
                }
                if ($data["k_platbe"] == "250") {
                    $output .= "<input type=\"hidden\" name=\"tarif\" value=\"1\" >";
                } elseif($data["k_platbe"] == "420") {
                    $output .= "<input type=\"hidden\" name=\"tarif\" value=\"2\" >";
                } else {
                    $output .= "<input type=\"hidden\" name=\"tarif\" value=\"3\" >";
                }

                $output .= "<input type=\"submit\" name=\"akce\" value=\"Tisk smlouvy\" class=\"vlastnici-archiv-button\" >";

                $output .= "</form>";

                $output .= "</td>";

                $output .= "<td colspan=\"2\" >
                    <form action=\"opravy-vlastnik.php\" method=\"get\" >
                    <input type=\"hidden\" name=\"typ\" value=\"2\" >
                    <input type=\"hidden\" name=\"id_vlastnika\" value=\"".$data["id_cloveka"]."\" >			    
		            <input type=\"submit\" name=\"ok\" value=\"Zobrazit závady/opravy \" ></form>";
                $output .= "</td>";

                $output .= "</tr>";
            }
        }

        if($this->echo) {
            echo $output;
        } else {
            return $output;
        }
        // konec funkce vypis
    }

}
