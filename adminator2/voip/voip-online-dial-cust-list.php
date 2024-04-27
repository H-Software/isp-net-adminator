<?php

global $cesta;

$cesta = "../";

require($cesta."include/main.function.shared.php");
require ($cesta."include/config.php"); 
require ($cesta."include/check_login.php");
require ($cesta."include/check_level.php");

if ( !( check_level($level,125) ) )
{
 // neni level

 $stranka=$cesta.'nolevelpage.php';
 header("Location: ".$stranka);
 
 echo "<br>Neopravneny pristup /chyba pristupu. STOP <br>";
 exit;
      
}
	

echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"> 
      <html> 
      <head> ';

include ($cesta."include/charset.php"); 

?>

<title>Adminator 2 - VoIP Online systém Dial </title> 

</head> 

<body> 

<? include ($cesta."head.php"); ?> 

<? include ($cesta."category.php"); ?> 

 
 <tr>
 <td colspan="2" bgcolor="silver" height=""><? include("voip-subcat-inc.php"); ?></td>
  </tr>
 
  <tr>
  <td colspan="2">
  <!-- zacatek vlastniho obsahu -->
  
 <?php

 $show_deleting = $_POST["show_deleting"];
 
 $cas1 = explode(" ", microtime()); 
 $cas1 = $cas1[1] + $cas1[0]; 
 $rd = "10000"; /* zaokrouhlování */
 
 echo "<div style=\"padding-top: 10px; \">
	<span style=\"font-weight: bold; font-size: 18px; border-bottom: 1px solid grey; \" >Online Voip systém DialTelecom</span>
	<span style=\"padding-left: 20px; border-bottom: 1px solid grey;\" >- Výpis klientů</span>
      </div>";
 
 echo "<div style=\"padding-top: 10px; padding-left: 5px; padding-bottom: 20px; \">";
 
  echo "<form action=\"\" method=\"POST\" name=\"form4\" >";
         
  echo "<div style=\"padding-bottom: 5px; \">
         <span style=\"color: grey; font-weight: bold; \">Filtr:</span>		     
          <span style=\"padding-left: 10px; \">Zobrazit prvek pro \"Odstranění\":</span>
	  <span style=\"padding-left: 10px; \">
	    <input type=\"checkbox\" value=\"Y\" name=\"show_deleting\" ";
	    if( $show_deleting == "Y"){ echo " checked=\"checked\" "; }
	    echo ">
	  </span>
	  
	  <span style=\"padding-left: 20px; \">
	    <input type=\"submit\" name=\"odeslat\" value=\"OK\" >
	  </span>
	  
	 </div>";
	  
  echo "</form>";
  
  echo "<div style=\"padding-top: 0px; padding-bottom: 20px; \">
         <a href=\"voip-online-dial-cust-add.php\" >Přidání klienta</a>
        </div>";
		     
    ob_flush();
    flush();
    	
    if( $show_deleting == "Y"){ $del = "1"; }	       
    else{ $del = "0"; }
    
    system("/var/www/cgi-bin/cgi-adm2/customer_list.pl ".$del);
			 
 echo "</div>";
 
 $cas2 = explode(" ", microtime()); 
 $cas = (round((($cas2[1] + $cas2[0]) - $cas1) * $rd)) / $rd;
 
 echo "<div>".$cas."</div>";
 
 ?> 
  <!-- konec vlastniho obsahu -->  
  
  </td>
  </tr>
  
 </table>

</body> 
</html> 
