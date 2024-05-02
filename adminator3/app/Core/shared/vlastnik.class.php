<?php

class vlastnik
{
    var $conn_mysql;

    var $conn_pgsql;

    function vypis_tab($par)
    {
        if ($par == 1) { echo "\n".'<table border="1" width="100%">'."\n"; 
        }
        elseif ($par == 2) { echo "\n".'</table>'."\n"; 
        }
        else    {    echo "chybny vyber"; 
        }
        
        // konec funkce vypis_tab     
    }
    

    function vypis($sql,$co,$mod,$dotaz_source)
    {
    
        $objekt = new objekt_a2();
        $objekt->conn_mysql = $this->conn_mysql;
        $objekt->conn_pgsql = $this->conn_pgsql;

        // co - co hledat, 1- podle dns, 2-podle ip , 3 - dle id_vlastnika
             
        $dotaz=pg_query($this->conn_pgsql, $dotaz_source);          
    
        if($dotaz !== false) {
            $radku=pg_num_rows($dotaz); 
        }
        else{
            echo("<div style=\"color: red;\">Dotaz selhal! ". pg_last_error($db_ok2). "</div>");
        }

        if ($radku==0) { echo "<tr><td><span style=\"color: red; \" >Nenalezeny žádné odpovídající výrazy dle hledaného \"".$sql."\". </span></td></tr>";
        } else
        {
           
            while( $data=pg_fetch_array($dotaz) ) {
              
                echo "<tr><td colspan=\"14\"> <br> </td> </tr>
    
	    <tr> <td class=\"vlastnici-td-black\" colspan=\"2\" >[".$data["id_cloveka"]."] ".$data["nick"]."</td>
	    
		<td class=\"vlastnici-td-black\" colspan=\"2\">VS: ".$data["vs"]."</td>
		<td class=\"vlastnici-td-black\" colspan=\"4\"> Platit (bez DPH): ".$data["k_platbe"]."</td>
		<td class=\"vlastnici-td-black\" colspan=\"6\" align=\"right\" width=\"8%\" >"; 
        
        
                echo "<table border=\"0\" width=\"70%\" > <tr> <td class=\"vlastnici-td-black\" width=\"\" >";
        
                // sem mazani
                global $vlastnici_erase_povolen;
        
                if (! ( $vlastnici_erase_povolen == "true" ) ) { echo "<span style=\"\" > smazat </span> "; 
                }
                else
                {
        
                    echo "<form method=\"POST\" action=\"vlastnici2-erase.php\" >";
                    echo "<input type=\"hidden\" name=\"erase_id\" value=\"".$data["id_cloveka"]."\" >";
                    echo "<input type=\"submit\" value=\"Smazat\" >";
                                            
                    echo "</form> \n";
                                            
                }
        
                echo "</td><td class=\"vlastnici-td-black\" >";
         
                global $vlastnici_update_povolen;
         
                // 6-ta update
                
                if (!( $vlastnici_update_povolen =="true") ) { echo "<span style=\"color: gray;\" >  upravit  </span> \n"; 
                }
                else
                { 
                    echo " <form method=\"POST\" action=\"vlastnici2-change.php\" >";
                    echo "<input type=\"hidden\" name=\"update_id\" value=\"".$data["id_cloveka"]."\" >";
                    echo "<input type=\"submit\" value=\"update\" >";
                     
                    echo "</form> \n";
                }
        
                echo "</td> </tr> </table>";
        
                echo "  </td> 
	    </tr>
	    
	    <tr> <td colspan=\"2\">".$data["jmeno"]." ".$data["prijmeni"]."<br>";
        
                echo $data["ulice"]."  ";
                
                echo "<a href=\"http://www.mapy.cz?query=".$data["ulice"].",".$data["mesto"]."\" target=\"_blank\" >ukaž na mapě</a>";
        
                echo " <br> ".$data["mesto"]." ".$data["psc"]."</td>";
        
                echo "<td colspan=\"11\">icq: ".$data["icq"]." <br>
	    mail: ".$data["mail"]." <br>
	    tel: ".$data["telefon"]." </td>
	    
	    </tr>
    
	    ";


                $id=$data["id_cloveka"];
                $id_f=$data["fakturacni"];
    
                // tady asi bude generovani fakturacnich udaju
                if (( $id_f > 0 ) ) {

                    fakturacni::vypis($id_f, $id);
    
                }
                // $sql="%";        
                $co="3";
        
                // $id=$data["id_cloveka"];
    
                // print "debug: id: $id";

    
                echo "<tr><td colspan=\"9\" >";

                echo "<table border=\"0\" width=\"100%\" >";
        
                $objekt->vypis($sql, $co, $id, "");

                echo "</table>";
      
                echo "</td></tr>\n\n";
    
                $pocet_wifi_obj = $objekt->zjistipocet(1, $id);
    
                $pocet_fiber_obj = $objekt->zjistipocet(2, $id);

                if($pocet_wifi_obj > 0 or $pocet_fiber_obj == 0 ) {
                    //objekty wifi
                    $co="3";
        
                    echo "<tr>
	    <td colspan=\"1\" bgcolor=\"#99FF99\" align=\"center\" >W
	    <td colspan=\"10\" bgcolor=\"#99FF99\" >";
                    echo "<table border=\"0\" width=\"100%\" >";
        
                    $objekt->vypis($sql, $co, $id, "");
        
                    echo "</table>";
                    echo "</td></tr>";
                }
   
                if($pocet_fiber_obj > 0 ) {
    
                    //objekty fiber
                    $co="4";

                    echo "<tr><td colspan=\"9\" bgcolor=\"fbbc86\"  >";
        
                    // echo "<tr>";
                    // echo "<td colspan=\"1\" bgcolor=\"fbbc86\" align=\"center\" >F</td>";
                    // echo "<td colspan=\"10\" bgcolor=\"fbbc86\" >";
       
                    echo "<table border=\"0\" width=\"100%\" >";
        
                    $objekt->vypis($sql, $co, $id);
        
                    echo "</table>";
    
                    echo "</td></tr>";
                }
                
    
                //tady dalsi radka asi
                /*    
                $voip = new voip();
    
                $id_vlastnika = $data["id_cloveka"];
    
                //$dotaz_sql = "SELECT * FROM voip_cisla WHERE id_vlastnika = '".intval($id_vlastnika)."' ";
      
                //$voip_radku = $voip->vypis_cisla_query($dotaz_sql);
   
                if ( $voip_radku > 0)
                {
                echo "<tr>";
    
                echo "<td colspan=\"14\" ><div style=\"padding-top: 10px; padding-bottom: 10px; \">";
    
                $voip->vypis_cisla("2");
    
                echo "</div></td>";
    
                echo "</tr>\n\n";
    
                }
                */
    
                echo "<tr>\n";
    
                echo "<td colspan=\"14\">";

                echo "<span style=\"margin: 25px; \">další funkce:</span>\n\n";
    
                echo "<a href=\"vlastnici2-add-obj.php?mod=1&id_vlastnika=".$data["id_cloveka"]."\" >přidání objektu</a>";    
    
                echo "<span style=\"margin: 25px; \"></span>";
    
                echo "<a href=\"platby-vypis.php?id_vlastnika=".$data["id_cloveka"]."\" > výpis plateb - starý (do 2/2012)</a>"; 
    
                echo "<span style=\"margin-left: 20px; \">".
                "<a href=pohoda_sql/phd_list_fa.php?id_vlastnika=".$data["id_cloveka"]."\" > výpis plateb - nový (od 3/2012)</a>".
                "</span>";
                                                      
                echo "<span style=\"margin: 15px; \"></span>";
      
                if (( $data["fakturacni"] > 0 ) ) { echo " přidání fakturační adresy "; 
                }
                else
                { echo "<a href=\"vlastnici2-add-fakt.php?id_vlastnika=".$data["id_cloveka"]."\" > přidání fakt. adresy </a>"; 
                }
    
                echo "<span style=\"margin: 15px; \"></span>";
    
                if (( $data["fakturacni"] > 0 ) ) { echo "<a href=\"vlastnici2-erase-f.php?id=".$data["fakturacni"]."\" > smazání fakt. adresy </a>"; 
                }
                else
                { echo " smazání fakt. adresy " ;
                }
    
                echo "<span style=\"margin: 15px; \" ></span>";
    
                if (( $data["fakturacni"] > 0 ) ) { echo "<a href=\"vlastnici2-change-fakt.php?id=".$data["fakturacni"]."\" > úprava fakt. adresy </a>"; 
                }
                else
                { echo " úprava fakt. adresy "; 
                }

                echo "<span style=\"margin: 25px; \" ></span>";
    
                echo "<a href=\"vlastnici-gen-xml.php?id_klienta=".$data["id_cloveka"]."\" > import klienta do Pohody (Adresář)</a>";
        
                //konec bunky/radky
                echo "</td></tr>";

                //druha radka
                echo "<tr>";

                echo "<td colspan=\"14\" >";
        
                echo "<table border=\"0\" width=\"100%\">";
        
                //h
                echo "<tr>";

                $orezano = explode(':', $data["pridano"]);
                $pridano=$orezano[0].":".$orezano[1];


                echo "<td colspan=\"1\" >";

                echo "datum přidání: ".$pridano." ";

                echo "</td>";

                echo "<td align=\"center\" >";

                echo " <img title=\"poznamka\" src=\"img2/poznamka3.png\" align=\"middle\" ";
                echo " onclick=\"window.alert(' poznámka: ".$data["poznamka"]." ');\" >";

                echo "</td>";

                echo "<td colspan=\"1\" >";
            
                /*
                echo "<form method=\"POST\" action=\"platby-akce.php\" >";

                echo "<input type=\"hidden\" name=\"firma\" value=\"1\" >";
                echo "<input type=\"hidden\" name=\"klient\" value=\"".$data["id_cloveka"]."\" >";
                    
                echo "<input type=\"submit\" name=\"akce\" value=\"Vložení hotovostní platby\" >";

                echo "</form>";
                */
                echo "</td>";

                echo "<td colspan=\"1\" >";

                echo "<form method=\"POST\" action=\"vypovedi-vlozeni.php\" >";

                echo "<input type=\"hidden\" name=\"firma\" value=\"1\" >";
                echo "<input type=\"hidden\" name=\"klient\" value=\"".$data["id_cloveka"]."\" >";

                echo "<input type=\"submit\" name=\"akce\" value=\"Vložení žádosti o výpověď\" >";

                echo "</form>";
                echo "</td>";

                echo "<td colspan=\"1\">";
        
                // zde dalsi veci
                echo "<span style=\"color: gray; padding-left: 10px; \" >H: </span>";
                echo "<a href=\"archiv-zmen.php?id_cloveka=".$data["id_cloveka"]."\">".$data["id_cloveka"]."</a>";
        
                echo "</td>";

                echo "<td>
		    <form action=\"opravy-vlastnik.php\" method=\"get\" >
		    <input type=\"hidden\" name=\"typ\" value=\"2\" >
		    <input type=\"hidden\" name=\"id_vlastnika\" value=\"".$data["id_cloveka"]."\" >
		    
	    <input type=\"submit\" name=\"ok\" value=\"Zobrazit závady/opravy \" ></form>";
                echo "</td>";                


                echo "<td>
		    <form action=\"opravy-index.php\" method=\"get\" >
		    <input type=\"hidden\" name=\"typ\" value=\"1\" >
		    <input type=\"hidden\" name=\"id_vlastnika\" value=\"".$data["id_cloveka"]."\" >
		    
	    <input type=\"submit\" name=\"ok\" value=\"Vložit závadu/opravu \" ></form>";
                echo "</td>";                

                echo "</tr></table>";    

                //h
    
                echo "</td>";
    
                echo "</tr>";


                //konec while
            }
    
            // konec else
        }
    
    
        // konec funkce vypis
    }

    //konec class-y vlastnik
}