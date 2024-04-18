<?php

$cesta = "../";

require ($cesta."include/main.function.shared.php");
require ($cesta."include/config.php"); 
require ($cesta."include/check_login.php");

require ($cesta."include/check_level.php");

if( !( check_level($level,111) ) )
{
 // neni level
 $stranka='nolevelpage.php';
 header("Location: ".$stranka);
 
 echo "<br>Neopravneny pristup /chyba pristupu. STOP <br>";
 exit;
      
}

echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"> 
      <html> 
      <head> ';

require ($cesta."include/charset.php"); 

?>

<title>Adminator 2 - partner - připojení</title> 

</head> 

<body> 

<?php require ($cesta."head.php"); ?> 

<?php require ($cesta."category.php"); ?> 

<tr>
 <td colspan="2" height="20" bgcolor="silver" >

    <?php require ("partner-klienti-cat.php"); ?>
	  
 </td>
</tr>
	   
 <tr>
   <td colspan="2" >
	
   <br>       
<?php

    echo "<div style=\"padding-left: 40px; padding-bottom: 20px; font-weight: bold; font-size: 18px; \">
               <span style=\"border-bottom: 1px solid grey; \" >Změna stavu připojení </span>
	    </div>";
	
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
    
      // echo "<div style=\"position: relative; \" >";
      	
      echo "\n\n\n<form action=\"\" method=\"GET\" >\n";
	    
      echo "<table border=\"0\" width=\"95%\" >
    	    <tr>\n";
      
        echo "<td width=\"30%\" valign=\"top\" >";
	//prvni sloupec
	echo "<div style=\"padding-left: 20px; padding-bottom: 20px; font-weight: bold; \">
		Vyberte zákazníka: </div>\n\n";
	 
	 //echo "<div style=\"padding-left: 20px; padding-top: 20px; \">
	echo "	<select name=\"id_zadosti\" size=\"5\" >
		  <option value=\"0\" class=\"select-nevybrano\" ";
		     if ( $id_zadosti == 0 or !isset($id_zadosti) ){ echo " selected "; }
		
		  echo ">Nevybráno</option>\n";
		  
	 $dotaz_zadosti=$conn_mysql->query("SELECT * FROM partner_klienti ORDER BY id DESC");
	 
	 while( $data=$dotaz_zadosti->fetch_array() )
	 { 
	   echo "<option value=\"".$data["id"]."\" > ".substr($data["jmeno"], 0, 22).",   ";
	   
	   echo substr($data["adresa"], 0, 22)."</option>\n"; 
	 }
	 
	 echo "</select>";
	 
	 echo "</td>";
	
        echo "<td valign=\"top\" width=\"30%\" >";
	
	 echo "<div style=\"padding-left: 20px; padding-bottom: 20px; font-weight: bold; \">
		Vyberte stav pole \"Připojeno\": </div>\n\n";
	 
	 echo "<div style=\"padding-left: 20px; \" >
	 
		<select name=\"pripojeno\" size=\"1\" >
		 <option value=\"0\" class=\"select-nevybrano\" ";
		   if ( $pripojeno == 0 or !isset($pripojeno) ){ echo " selected "; }
		 echo ">Nevybráno</option>\n
		 <option value=\"1\" >Ano</option>\n
		 <option value=\"2\" >Ne</option>\n
		</select>";
	
	 echo "<div style=\"padding-top: 20px; font-weight: bold; \">
		Vyberte stav pole \"Aktuální linka\": </div>\n\n";
	
	 echo "<div style=\"padding-top: 20px; padding-bottom: 20px; \" >
	 
		<select name=\"akt_tarif\" size=\"1\" >
		 <option value=\"0\" class=\"select-nevybrano\" ";
		   if ( $akt_tarif == 0 or !isset($akt_tarif) ){ echo " selected "; }
		 echo ">Nevybráno</option>\n
		 <option value=\"1\" >SmallCity</option>\n
		 <option value=\"2\" >Metropolitní</option>\n
		 <option value=\"3\" >Jiná</option>\n
		 
		</select>
		
		</div>";
	
	echo "</div></td>"; 
    
        echo "<td valign=\"top\" width=\"30%\" >
	
	 <div style=\"padding-left: 20px; padding-bottom: 20px; font-weight: bold; \">
		Potvrdit: </div>\n\n";
	 
	 echo "<div style=\"padding-left: 20px; \" >
		<input type=\"submit\" name=\"odeslat\" value=\"OK\" >
	       </div></td>"; 
    
	echo "</tr></table>";
	
      echo "</form>";
       
     } // konec else odeslat == OK

  ?>
  
  </td>
  </tr>
  
 </table>

</body> 
</html> 

