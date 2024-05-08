<?php


    if ( ( !( preg_match('/^([[:digit:]])+$/',$_GET["id_zadosti"]) ) and ( $_GET["odeslat"] == "OK" ) ) )
    {
      echo "<div style=\"color: red; font-weight: bold; padding-left: 20px; padding-bottom: 20px; \">
        Chyba! Zákazníka nelze akceptovat! Vstupní data \"id zadosti\" jsou ve špatném formátu! </div> ";
      
      exit;
    }
    
    if ( ( !( preg_match('/^([[:digit:]])+$/',$_GET["pripojeno"]) ) and ( $_GET["odeslat"] == "OK" ) ) )
    {
      echo "<div style=\"color: red; font-weight: bold; padding-left: 20px; padding-bottom: 20px; \">
        Chyba! Zákazníka nelze akceptovat! Vstupní data \"pripojeno\" jsou ve špatném formátu! </div> ";
      
      exit;
    }
    
    if ( ( !( preg_match('/^([[:digit:]])+$/',$_GET["akt_tarif"]) ) and ( $_GET["odeslat"] == "OK" ) ) )
    {
      echo "<div style=\"color: red; font-weight: bold; padding-left: 20px; padding-bottom: 20px; \">
        Chyba! Zákazníka nelze akceptovat! Vstupní data \"akt_tarif\" jsou ve špatném formátu! </div> ";
      
      exit;
    }
    
    if ( $_GET["odeslat"] == "OK" )
    {
		//budem ukladat
			
		$pozn = $_GET["pozn"];
		$id_zadosti = $_GET["id_zadosti"];
		
		$pripojeno = $_GET["pripojeno"];
		$akt_tarif = $_GET["akt_tarif"];
	
        $uprava=$conn_mysql->query("UPDATE partner_klienti SET pripojeno='$pripojeno', pripojeno_linka='$akt_tarif' 
				where id=".$id_zadosti." Limit 1 ");
  
       if ($uprava) 
       {
         echo "<br><H3><div style=\"color: green; padding-left: 20px; \" >
    			Pole \"Připojeno, Aktuální tarif\" úspěšně upraveno.</div></H3><br>\n"; 
       }
       else
       { 
    	  echo "<div style=\"color: red; padding-left: 20px; \">
		  Chyba! Pole \"Připojeno, Aktuální tarif\" nelze upravit. Data nelze uložit do databáze. 
		</div><br>\n";
       
          //echo mysql_error($uprava); 
       }
    
    } // konec if odeslat == "OK"
    else
    { //zobrazime form 
    
      	

       
     } // konec else odeslat == OK

  ?>
  
  </td>
  </tr>
  
 </table>

</body> 
</html> 

