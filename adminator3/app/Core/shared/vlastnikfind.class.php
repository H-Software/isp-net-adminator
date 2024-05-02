<?php

class vlastnikfind
{

    public static function vypis_tab($par)
    {
        if ($par == 1) { echo "\n".'<table border="1" width="100%">'."\n"; 
        }
        elseif ($par == 2) { echo "\n".'</table>'."\n"; 
        }
        else    { echo "chybny vyber"; 
        }
          
            
        // konec funkce vypis_tab
    }
            
    function vypis($sql,$dotaz_source,$co = "")
    {
    
        if ($co == 2) {
    
            // echo "<tr><td>sem fakturacni </td></tr>";
    
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
    
            $dotaz=pg_query($dotaz_sql);
             
        }
        else
        { $dotaz=pg_query($dotaz_source); 
        }
    
        $radku=pg_num_rows($dotaz); 
    
        
        if($radku==0) {
            echo "<tr><td colspan=\"9\" ><span style=\"color: red; \" >Nenalezeny žádné odpovídající výrazy dle ";
            echo "hledaného \"".$sql."\".</span></td></tr>";
        }
        elseif($radku > 25 ) { echo "<tr><td><span style=\"color: red; \" >Nalezeno více záznamů než je limit, specifikujte hledaný výraz. </span></td></tr>";    
        } else
        {
           
            while( $data=pg_fetch_array($dotaz) ) {
        
                // if ($co == 2)      
    
                echo "<tr><td colspan=\"14\"> <br> </td> </tr>
    
	    <tr> <td class=\"vlastnici-td-black\" colspan=\"2\" >[".$data["id_cloveka"]."] ".$data["nick"]."</td>
	    
		<td class=\"vlastnici-td-black\" colspan=\"2\">VS: ".$data["vs"]."</td>
		<td class=\"vlastnici-td-black\" colspan=\"4\"> Platit (bez DPH): ".$data["k_platbe"]."</td>
		<td class=\"vlastnici-td-black\" colspan=\"6\" align=\"right\" width=\"8%\" >"; 
        
        
                // tutady update a smazat, takze nic
                   echo "<br>";
        
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
                    $fakturacni = new fakturacni;
                    $fakturacni->vypis($id_f, $id);
                }
    
                // $sql="%";        
                $co="3";

                //tady dalsi radka asi
    
    
                echo "<tr>";
    
                echo "<td colspan=\"\" ><span style=\"font-weight: bold; font-size: 20px;  \" >Detail vlastníka: ";
    
                $id_cloveka=$data["id_cloveka"];
    
                $firma_vlastnik=$data["firma"]; 
                $archiv_vlastnik=$data["archiv"];
    
                if ($archiv_vlastnik == 1) { echo "V: <a href=\"vlastnici-archiv.php?find_id=".$data["id_cloveka"]."\" >".$data["id_cloveka"]."</a> </span> </td> \n"; 
                }
                else
                { echo "V: <a href=\"vlastnici2.php?find_id=".$data["id_cloveka"]."\" >".$data["id_cloveka"]."</a> </span></td> \n"; 
                }            

                echo "</span></td>";
    
                $orezano = explode(':', $data["pridano"]);
                $pridano=$orezano[0].":".$orezano[1];

                echo "<td colspan=\"2\" width=\"250px\" >datum přidání: ".$pridano." </td>";
    
                echo "<td align=\"center\" width=\"50px\" >";

                echo " <img title=\"poznamka\" src=\"img2/poznamka3.png\" align=\"middle\" ";
                echo " onclick=\"window.alert(' poznámka: ".$data["poznamka"]." ');\" >";

                echo "</td>";

                echo "<td colspan=\"5\" ><br></td>";
            
                echo "</tr>";

                //konec while
            }

            // } //konec else if co == 2
        
            // konec else
        }
    
    
        // konec funkce vypis
    }
    
} // konec class vlastnikfind
