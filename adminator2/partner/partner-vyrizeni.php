<?php

$cesta = "../";

require ($cesta."include/main.function.shared.php");
require_once ($cesta."include/config.php"); 
require_once ($cesta."include/check_login.php");

require_once ($cesta."include/check_level.php");

if ( !( check_level($level,77) ) )
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

<title>Adminator 2</title> 

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
   if ( ! ( $_GET["accept"] == 1 ) )
   {
    global $vyrizeni;
   
    $vyrizeni = true;
   
    $filtr_akceptovano = "2";
   
    //require("/var/www/html/virtuals/partner/vypis-inc.php");
    echo "<div>missing list code</div>";

   } //konec if update_id > 0
   else
   {

    echo "<div style=\"padding-left: 40px; padding-bottom: 20px; font-weight: bold; font-size: 18px; \">
               <span style=\"border-bottom: 1px solid grey; \" >Akceptování žádostí o připojení</span>
	    </div>";
	
    if ( !( ereg('^([[:digit:]])+$',$_GET["id"]) ) )
    {
      echo "<div style=\"color: red; font-weight: bold; padding-left: 20px; padding-bottom: 20px; \">
        Chyba! Zákazníka nelze akceptovat! Vstupní data jsou ve špatném formátu! </div> ";
      
      exit;
    }
    
    if ( $_GET["odeslat"] == "OK" )
    {
    //budem ukladat
	    
	$pozn = $conn_mysql->real_escape_string($_GET["pozn"]);
	$id = intval($_GET["id"]);
	
        $uprava=$conn_mysql->query("UPDATE partner_klienti SET akceptovano='1', akceptovano_kym='".$conn_mysql->real_escape_string(\Cartalyst\Sentinel\Native\Facades\Sentinel::getUser()->email)."', ".
    			     " akceptovano_pozn = '$pozn' WHERE id=".$id." Limit 1 ");
  
       if ($uprava == 1) { 
          echo "<br><H3><div style=\"color: green; \" >Zákazník úspěšně akceptován.</div></H3><br>\n"; }
       else
       { 
    	  echo "<div style=\"color: red; \">Chyba! Zákazníka nelze akceptovat. Data nelze uložit do databáze. </div><br>\n";
       
          //echo mysql_error($uprava); 
       }
    
    } // konec if odeslat == "OK"
    else
    { //zobrazime form pro poznamku
	
      echo "<form action=\"\" method=\"GET\" >";
	    

        echo "<div style=\"padding-left: 40px; padding-bottom: 20px; \" >Pokud je třeba vložte poznámku: </div>"; 
      
        echo "<div style=\"padding-left: 40px; padding-bottom: 20px;\" >
    		<textarea name=\"pozn\" cols=\"50\" rows=\"6\"></textarea>
	    </div>"; 
      
        echo "<div style=\"padding-left: 40px; padding-bottom: 20px; \" >
    		<input type=\"submit\" name=\"odeslat\" value=\"OK\" >
	    </div>"; 
     
    	echo "<input type=\"hidden\" name=\"accept\" value=\"1\"> 
		    <input type=\"hidden\" name=\"id\" value=\"".intval($_GET["id"])."\" >";
       echo "</form>";
       
     } // konec else odeslat == OK
   } //konec else get == 1

  ?>
  
  </td>
  </tr>
  
 </table>

</body> 
</html> 
