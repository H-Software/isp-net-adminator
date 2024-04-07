<?php

include ("include/config.php"); 
include ("include/check_login.php");

include ("include/check_level.php");

if ( !( check_level($level,24) ) )
{
// neni level

$stranka='nolevelpage.php';
 header("Location: ".$stranka);
 
   echo "<br>Neopravneny pristup /chyba pristupu. STOP <br>";
      Exit;
      
        }
	

echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"> 
      <html> 
      <head> ';

include ("include/charset.php"); 

?>

<title>Adminator 2</title> 

</head> 

<body> 

<? include ("head.php"); ?> 

<? include ("category.php"); ?> 

 <tr>
   <td colspan="2" height="20" bgcolor="silver">
      <? include("admin-subcat2-inc.php"); ?>
   </td>
 </tr>
 
  <tr>
  <td colspan="2">
  
  <table width=100%>
  <tr>
  
  <td valign="top" colspan="2" > 

    <br><div style="font-size: 18px; ">PDF verze: ( nový způsob ) </div>
    <br>
    na adrese <a href="http://tisk.simelon.net">tisk.simelon.net</a><br>
    <br>
    
    <hr>
    <span style="font-size: 18px; " >Starý způsob:</span>
    
    <br>  
	<br><H2> Tisk smlouvy:</H2><br>
    1.	Tisk vzoru <a href="print/smlouva.php?visible=true">zde</a><br>
    2.	Formulár pro generovnání textu smlouvy <a href="print/smlouva-form.php">zde</a><br>
	
    3.	Přímé vylpnění textu do "vzoru" bez formuláře <a href="print/smlouva.php">zde</a>
    <br><br>
    <b>Postup: </b><br><br>
    
    <b><i>A.</b></i> Nastavíme v Opeře tisk: "volby tisku" -> odškrtneme: "tisk pozadí" a "tisk záhlaví a zápatí"<br>			
			    Okraje nastavit na 0,5cm z každé strany
			    
    <br>
    <b><i>B.</b></i> Vytiskneme vzor a necháme šéfa podepsat
    
    <br>
    <b><i>C.</b></i> zvolíme buď odkaz 2. nebo 3., doplníme obsah, a necháme vytisknout textové údaje na vzor<br>
	    ( vložení vzoru do tiskárny: textem vzhůru )	
  
  </td>
  </tr>  
  </table>
  
  </td>
  </tr>
  
 </table>

</body> 
</html> 

