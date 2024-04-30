<?php

$cesta = "../";

require ($cesta."include/main.function.shared.php");
require_once ($cesta."include/config.php");
require_once ($cesta."include/check_login.php");
require_once ($cesta."include/check_level.php");

if ( !( check_level($level,119) ) )
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

<title>Adminator 2 - partner program</title> 

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

              
   if( ! ( $_GET["edit"] == 1 ) )
   {
   
      $list = intval($_GET["list"]);
      
      // require($cesta."include/c_listing-partner.php");

      $poradek = "filtr_akceptovano=".$filtr_akceptovano."&filtr_pripojeno=".$filtr_pripojeno;
  
      $format_css = "font-size: 13px; padding-top: 5px; padding-bottom: 15px; ";
    
      $dotaz_sql = "SELECT *,DATE_FORMAT(datum_vlozeni, '%d.%m.%Y %H:%i:%s') ".
		"as datum_vlozeni2 FROM partner_klienti WHERE ( id > 0 ".$filtr." ) ORDER BY id DESC ".$dotaz_limit;
   
      // $listovani = new c_listing_partner($conn_mysql, "./partner-pozn-update.php?".$poradek, 30, $list, "<center><div style=\"".$format_css."\">\n", "</div></center>\n", $dotaz_sql);

      // if(($list == "")||($list == "1")){ $bude_chybet = 0; }
      // else{ $bude_chybet = (($list-1) * $listovani->interval); }

      // $interval = $listovani->interval;
   
      // $dotaz_limit = " LIMIT ".$interval." OFFSET ".$bude_chybet." ";

      
    global $update, $dotaz_limit;
   
    $update = true;
   
    // $filtr_akceptovano = "2";
    // $listovani->listInterval();
    
    echo "<div style=\"padding-left: 40px; padding-bottom: 20px; font-weight: bold; font-size: 18px; \">
    <span style=\"border-bottom: 1px solid grey; \" >Změna poznámky technika</span>
    </div>";

    echo "<div> zde nejaky vypis zadosti</div>";
    // require("/var/www/html/virtuals/partner/vypis-inc.php");
   
    // $listovani->listInterval();
    
   } //konec if update_id > 0
   else
   {

    echo "<div style=\"padding-left: 40px; padding-bottom: 20px; font-weight: bold; font-size: 18px; \">
               <span style=\"border-bottom: 1px solid grey; \" >Změna poznámky technika</span>
	    </div>";
	
    if( !( ereg('^([[:digit:]])+$',$_GET["id"]) ) )
    {
      echo "<div style=\"color: red; font-weight: bold; padding-left: 20px; padding-bottom: 20px; \">
        Chyba! Poznámku nelze upravit! Vstupní data jsou ve špatném formátu! </div> ";
      
      exit;
    }
    
    if( $_GET["odeslat"] == "OK" )
    {   //budem ukladat
	    
	$pozn = mysql_real_escape_string($_GET["pozn"]);
	$id = intval($_GET["id"]);
	
        $uprava=mysql_query("UPDATE partner_klienti SET akceptovano_pozn = '$pozn' WHERE id=".$id." Limit 1 ");
  
       if ($uprava) { echo "<br><H3><div style=\"color: green; \" >Poznámka úspěšně upravena.</div></H3><br>\n"; }
       else
       { 
    	  echo "<div style=\"color: red; \">Chyba! Poznámku nelze upravit. Data nelze uložit do databáze. </div><br>\n";
       
          //echo mysql_error($uprava); 
       }
    
    } // konec if odeslat == "OK"
    else
    { //zobrazime form pro poznamku
	
      $id = intval($_GET["id"]);
      
      //nacteme predchozi data
      $dotaz = mysql_query("SELECT akceptovano_pozn FROM partner_klienti WHERE id = '$id' ");
      
      while( $data = mysql_fetch_array($dotaz) )
      { $pozn = $data["akceptovano_pozn"]; }

        echo "<form action=\"\" method=\"GET\" >";
      
        echo "<div style=\"padding-left: 40px; padding-bottom: 20px; \" >Upravte poznámku: </div>"; 
      
        echo "<div style=\"padding-left: 40px; padding-bottom: 20px;\" >
    		<textarea name=\"pozn\" cols=\"50\" rows=\"6\">".htmlspecialchars($pozn)."</textarea>
	    </div>"; 
      
        echo "<div style=\"padding-left: 40px; padding-bottom: 20px; \" >
    		<input type=\"submit\" name=\"odeslat\" value=\"OK\" >
	    </div>"; 
     
    	echo "<input type=\"hidden\" name=\"edit\" value=\"1\"> 
		<input type=\"hidden\" name=\"id\" value=\"".$id."\" >";
       echo "</form>";
       
     } // konec else odeslat == OK
   
   } //konec else get == 1

  ?>

  </td>
  </tr>

 </table>

</body> 
</html> 
