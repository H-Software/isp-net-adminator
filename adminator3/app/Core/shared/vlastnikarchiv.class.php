<?php

class vlastnikarchiv
{

   public static function vypis_tab ($par)
    {
		if ($par == 1) { echo "\n".'<table border="1" width="100%">'."\n"; }
		elseif ($par == 2) { echo "\n".'</table>'."\n"; }
		else    { echo "chybny vyber"; }				
	}
				
				
    public function vypis ($sql,$co,$dotaz_final)
    {
					
    // co - co hledat, 1- podle dns, 2-podle ip
						    
    $dotaz=pg_query($dotaz_final);

	if($dotaz !== false) {
		$radku=pg_num_rows($dotaz);
	}
	else{
		echo("<div style=\"color: red;\">Dotaz selhal! ". pg_last_error($db_ok2). "</div>");
	}

    if ($radku==0) echo "<tr><td><span style=\"color: red; \" >Nenalezeny žádné odpovídající výrazy dle hledaného \"".$sql."\". </span></td></tr>";
    else
        {

        while( $data=pg_fetch_array($dotaz) ) 
	{
	    echo "<tr><td colspan=\"14\"> <br> </td> </tr>
	    <tr> <td class=\"vlastnici-td-black\" colspan=\"2\" width=\"\" >id: [".$data["id_cloveka"]."] 
	    nick: [".$data["nick"]."] účetní-index: [".sprintf("%05d", $data["ucetni_index"])."] </td>
	    
	    <td class=\"vlastnici-td-black\" colspan=\"2\">VS: [".$data["vs"]."] ";
	    
	    if ($data["firma"] == 1){ echo " firma: [Simelon, s.r.o.]"; }
	    else{ echo " firma: [M. Lopušný] "; }
	    
	    echo"</td>
	
	    <td class=\"vlastnici-td-black\" colspan=\"4\"> Platit (bez DPH): ".$data["k_platbe"]."</td>
	    <td class=\"vlastnici-td-black\" colspan=\"6\" align=\"right\" width=\"\" >";
	
	    echo "<table border=\"0\" width=\"70%\" > <tr> <td class=\"vlastnici-td-black\" width=\"\" >";
	
	// sem mazani
	
	global $vlastnici_erase_povolen;
	
	if ( ! ( $vlastnici_erase_povolen == "true" ) )
	{ echo "<span style=\"\" > smazat </span> "; }
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
	
	if ( !( $vlastnici_update_povolen =="true") )
	{ echo "<span style=\"\" >  upravit  </span> \n"; }
	
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
		  <tr> <td colspan=\"2\">".$data["jmeno"]." ".$data["prijmeni"]."<br>
		 ".$data["ulice"]." ";
		     
	    echo "<a href=\"http://www.mapy.cz?query=".$data["ulice"].",".$data["mesto"]."\" target=\"_blank\" >ukaž na mapě</a>";
	    
	    
		 echo "<br>".$data["mesto"]." ".$data["psc"]."</td>
		 <td colspan=\"12\">icq: ".$data["icq"]." <br>
		 mail: ".$data["mail"]." <br>
		 tel: ".$data["telefon"]." </td>
		 </tr>";
		
		
	 $id=$data["id_cloveka"];
	 $id_v=$id;
	 
	 $id_f=$data["fakturacni"];
	
	// tady asi bude generovani fakturacnich udaju
	
	if ( ( $id_f > 0 ) )
	{
	
	     fakturacni::vypis($id_f,$id_v);
	
	 }
	
	// $sql="%";
	$co="3";
	
	// $id=$data["id_cloveka"];
	// print "debug: id: $id";
	
	 objekt_a2::vypis($sql,$co,$id);



	//tady dalsi radka asi
	
	
	    echo "<tr>";
	    
	    echo "<td>další funkce: </td>
	        <td colspan=\"13\">";
			
	    //echo "<a href=\"vlastnici2-add-obj.php?id_vlastnika=".$data["id_cloveka"]."\">přidání objektu</a>";
	    echo "<span style=\"color: gray; \">přidání objektu</span>";
	    
	    echo "<span style=\"margin: 10px; \"></span>";
	      
	    echo "<a href=\"platby-vypis.php?id_vlastnika=".intval($data["id_cloveka"])."\" > výpis plateb - starý (do 2/2012)</a>";

	    echo "<span style=\"margin-left: 20px; \">
		    <a href=pohoda_sql/phd_list_fa.php?id_vlastnika=".$data["id_cloveka"]."\" > výpis plateb - (od 3/2012)</a>".
		  "</span>";
	
	    echo "<span style=\"margin: 10px; \">fakturační adresa:</span>";
	      
	    /*
	    if ( ( $data["fakturacni"] > 0 ) )
	    { echo " přidání fakturační adresy "; }
	    else
	    { echo "<a href=\"vlastnici2-add-fakt.php?id_vlastnika=".$data["id_cloveka"]."\" > přidání fakturační adresy </a>"; }
	    */
	    echo "<span style=\"color: grey; \"> přidání</span>";
	    
	    echo "<span style=\"margin: 25px; \"></span>";
	    
	    if ( ( $data["fakturacni"] > 0 ) )
	    { echo "<a href=\"vlastnici2-erase-f.php?id=".$data["fakturacni"]."\" > smazání </a>"; }
	    else 
	    { echo " smazání "; }
	    
	    echo "<span style=\"margin: 25px; \" ></span>";
	    
	    if ( ( $data["fakturacni"] > 0 ) )
	    { echo "<a href=\"vlastnici2-change-fakt.php?id=".$data["fakturacni"]."\" > úprava </a>"; }
	    else
	    { echo " úprava "; }

	    echo "</td></tr>";
	    
	    //druha radka			
	    echo "<tr>";
	    
	    // echo "<td><br></td>";
	    	    
	    $orezano = explode(':', $data["pridano"]);
	    $pridano=$orezano[0].":".$orezano[1];
		      
		          
	    echo "<td colspan=\"1\" >";
	    
	    echo "datum přidání: ".$pridano." ";
	        
	    echo "</td>";
	    
	    echo "<td align=\"center\" >";
	    
		echo " <img title=\"poznamka\" src=\"img2/poznamka3.png\" align=\"middle\" ";
		echo " onclick=\"window.alert(' poznámka: ".$data["poznamka"]." ');\" >";
	    
	    echo "</td>";
	    
	    echo "<td colspan=\"4\">";
		echo "<form method=\"POST\" action=\"platby-akce.php\" >";
		
		echo "<input type=\"hidden\" name=\"firma\" value=\"2\" >";
		echo "<input type=\"hidden\" name=\"klient\" value=\"".$data["id_cloveka"]."\" >";
		
		echo "<input type=\"submit\" name=\"akce\" value=\"Vložení hotovostní platby\" >";
	    
		echo "</form>";
	    echo "</td>";


	    echo "<td colspan=\"4\">";
		echo "<form method=\"POST\" action=\"vypovedi-vlozeni.php\" >";
		
		echo "<input type=\"hidden\" name=\"firma\" value=\"2\" >";
		echo "<input type=\"hidden\" name=\"klient\" value=\"".$data["id_cloveka"]."\" >";
		
		echo "<input type=\"submit\" name=\"akce\" value=\"Vložit žádost o výpověď\" >";
	    
		echo "</form>";
	    echo "</td>";
	    
	    //echo "<td colspan=\"3\"><br></td>";
	    
	    echo "<td colspan=\"1\">";
	    // zde dalsi veci
	    echo "<span style=\"color: grey; padding-left: 10px; \" >H: </span>";
	    echo "<a href=\"archiv-zmen.php?id_cloveka=".$data["id_cloveka"]."\">".$data["id_cloveka"]."</a>";
					
	    echo "</td>";
						    
	    echo "<td> ";
	    //tisk smlouvy
	    echo "<form method=\"POST\" action=\"https://tisk.simelon.net/smlouva-pdf.php\" >";
										
	    echo "<input type=\"hidden\" name=\"ec\" value=\"".$data["vs"]."\" >";
	    echo "<input type=\"hidden\" name=\"jmeno\" value=\"".$data["jmeno"]." ".$data["prijmeni"]."\" >";
	    echo "<input type=\"hidden\" name=\"ulice\" value=\"".$data["ulice"]."\" >";
	    echo "<input type=\"hidden\" name=\"mesto\" value=\"".$data["psc"]." ".$data["mesto"]."\" >";
	    echo "<input type=\"hidden\" name=\"telefon\" value=\"".$data["telefon"]."\" >";
	    echo "<input type=\"hidden\" name=\"email\" value=\"".$data["mail"]."\" >";
	
	    if( ( $data["fakturacni"] > 0 ) )
	    {
	        echo "<input type=\"hidden\" name=\"fakturace\" value=\"2\" >";
	        //echo "<input type=\"hidden\" name=\"jmeno\" value=\"".$data["jmeno"]." ".$data["prijmeni"]."\" >";
	        //echo "<input type=\"hidden\" name=\"ulice\" value=\"".$data["ulice"]."\" >";
	        //echo "<input type=\"hidden\" name=\"mesto\" value=\"".$data["psc"]." ".$data["mesto"]."\" >";
	    }
	    if ( $data["k_platbe"] == "250" )
	    { echo "<input type=\"hidden\" name=\"tarif\" value=\"1\" >"; }
	    elseif( $data["k_platbe"] == "420" )
	    { echo "<input type=\"hidden\" name=\"tarif\" value=\"2\" >"; }
	    else
	    { echo "<input type=\"hidden\" name=\"tarif\" value=\"3\" >"; }
	
	    echo "<input type=\"submit\" name=\"akce\" value=\"Tisk smlouvy\" >";
	    
	    echo "</form>";
	
	    // echo "</tr></table>";
	    echo "</td>";
	    
	    
	    echo "<td colspan=\"2\" >
		    <form action=\"opravy-vlastnik.php\" method=\"get\" >
		    <input type=\"hidden\" name=\"typ\" value=\"2\" >
		    <input type=\"hidden\" name=\"id_vlastnika\" value=\"".$data["id_cloveka"]."\" >
										    
		    <input type=\"submit\" name=\"ok\" value=\"Zobrazit závady/opravy \" ></form>";
	    echo "</td>";
	        
	    echo "</tr>";
	
	//konec while
	}
	
	// konec else
	}
	
	// konec funkce vypis
	}

}
