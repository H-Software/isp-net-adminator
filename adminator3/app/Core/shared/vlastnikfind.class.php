<?php

class vlastnikfind
{
    public $conn_mysql;
    public $conn_pgsql;

    public $csrf_html;

    public $echo = false;

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

    public function vypis($sql, $dotaz_source, $co = "")
    {
        $output = "";

        if ($co == 2) {

            // $output .= "<tr><td>sem fakturacni </td></tr>";

            $dotaz_sql = "SELECT t1.id_cloveka,t1.jmeno, t1.prijmeni, t1.mail, t1.telefon, t1.k_platbe, t1.ucetni_index, t1.poznamka,t1.fakturacni,
		    t1.ulice,t1.mesto,t1.psc,t1.vs,t1.icq,t1.pridano,t1.firma, t1.archiv,
                         t2.ftitle, t2.fulice, t2.fmesto, t2.fpsc, t2.ico, t2.dic, t2.ucet, t2.splatnost, t2.cetnost
			 
		    FROM ( vlastnici AS t1 LEFT JOIN fakturacni AS t2 ON t1.fakturacni=t2.id )
		    WHERE 
		    (
		    ( t2.ftitle LIKE '$sql' OR t2.fulice LIKE '$sql' OR t2.fmesto LIKE '$sql' OR t2.fpsc LIKE '$sql'
		     OR t2.ico LIKE '$sql' OR t2.dic LIKE '$sql' OR ucet LIKE '$sql' OR t2.splatnost LIKE '$sql' 
		     OR t2.cetnost LIKE '$sql' )
		     AND  ( archiv = 0 or archiv is null ) )
		     ";

            $dotaz = pg_query($this->conn_pgsql, $dotaz_sql);

        } else {
            $dotaz = pg_query($this->conn_pgsql, $dotaz_source);
        }

        $radku = pg_num_rows($dotaz);


        if($radku == 0) {
            $output .= "<tr><td colspan=\"9\" ><span style=\"color: red; \" >Nenalezeny žádné odpovídající výrazy dle ";
            $output .= "hledaného \"".$sql."\".</span></td></tr>";
        } elseif($radku > 25) {
            $output .= "<tr><td><span style=\"color: red; \" >Nalezeno více záznamů než je limit, specifikujte hledaný výraz. </span></td></tr>";
        } else {

            while($data = pg_fetch_array($dotaz)) {

                // if ($co == 2)

                $output .= "<tr><td colspan=\"14\"> <br> </td> </tr>

                    <tr> <td class=\"vlastnici-td-black\" colspan=\"2\" >[".$data["id_cloveka"]."] ".$data["nick"]."</td>
                    
                    <td class=\"vlastnici-td-black\" colspan=\"2\">VS: ".$data["vs"]."</td>
                    <td class=\"vlastnici-td-black\" colspan=\"4\"> Platit (bez DPH): ".$data["k_platbe"]."</td>
                    <td class=\"vlastnici-td-black\" colspan=\"6\" align=\"right\" width=\"8%\" >";


                // tutady update a smazat, takze nic
                $output .= "<br>";

                $output .= "  </td> 
                        </tr>
                        
                        <tr> <td colspan=\"2\">".$data["jmeno"]." ".$data["prijmeni"]."<br>";

                $output .= $data["ulice"]."  ";

                $output .= "<a href=\"http://www.mapy.cz?query=".$data["ulice"].",".$data["mesto"]."\" target=\"_blank\" >ukaž na mapě</a>";

                $output .= " <br> ".$data["mesto"]." ".$data["psc"]."</td>";

                $output .= "<td colspan=\"11\">icq: ".$data["icq"]." <br>
                        mail: ".$data["mail"]." <br>
                        tel: ".$data["telefon"]." </td>
                        
                        </tr>
                    
                        ";

                $id = $data["id_cloveka"];
                $id_f = $data["fakturacni"];

                // tady asi bude generovani fakturacnich udaju
                if (($id_f > 0)) {
                    $fakturacni = new fakturacni();
                    $fakturacni->vypis($id_f, $id);
                }

                // $sql="%";
                $co = "3";

                //tady dalsi radka asi
                $output .= "<tr>";
                $output .= "<td colspan=\"\" ><span style=\"font-weight: bold; font-size: 20px;  \" >Detail vlastníka: ";

                $id_cloveka = $data["id_cloveka"];

                $firma_vlastnik = $data["firma"];
                $archiv_vlastnik = $data["archiv"];

                if ($archiv_vlastnik == 1) {
                    $output .= "V: <a href=\"vlastnici-archiv.php?find_id=".$data["id_cloveka"]."\" >".$data["id_cloveka"]."</a> </span> </td> \n";
                } else {
                    $output .= "V: <a href=\"vlastnici2.php?find_id=".$data["id_cloveka"]."\" >".$data["id_cloveka"]."</a> </span></td> \n";
                }

                $output .= "</span></td>";

                $orezano = explode(':', $data["pridano"]);
                $pridano = $orezano[0].":".$orezano[1];

                $output .= "<td colspan=\"2\" width=\"250px\" >datum přidání: ".$pridano." </td>";

                $output .= "<td align=\"center\" width=\"50px\" >";

                $output .= " <img title=\"poznamka\" src=\"img2/poznamka3.png\" align=\"middle\" ";
                $output .= " onclick=\"window.alert(' poznámka: ".$data["poznamka"]." ');\" >";

                $output .= "</td>";

                $output .= "<td colspan=\"5\" ><br></td>";

                $output .= "</tr>";

                //konec while
            }

            // } //konec else if co == 2

            // konec else
        }


        if($this->echo) {
            echo $output;
        } else {
            return $output;
        }
    }

} // konec class vlastnikfind
