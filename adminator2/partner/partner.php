<?php

$cesta = "../";

require_once ($cesta."include/config.php"); 
require ($cesta."include/check_login.php");

require ($cesta."include/check_level.php");

if( !( check_level($level,75) ) )
{  // neni level

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

<title>Adminator 2 - partner</title> 

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

 $jmeno_klienta = $_POST["jmeno_klienta"]; 
 $bydliste = $_POST["bydliste"];
 $email = $_POST["email"];
 $tel = $_POST["tel"];
 $typ_balicku = $_POST["typ_balicku"];
 $typ_linky = $_POST["typ_linky"];

 $pozn = $_POST["pozn"];
 $odeslat = $_POST["odeslat"];
 

 // zde kontrola, popr. naplneni promenne error

 $cesta="/var/www/html/virtuals/partner/";
    
 require($cesta."vlozeni-kontrola-inc.php");

 if( ( isset($odeslat) and ($fail == false) ) )
 { // mod ukladani

    require($cesta."vlozeni-ukladani-inc.php");

 }
 else
 { // zobrazime formular

    echo "<form action=\"\" method=\"post\" >";

    if( isset($odeslat) ){ echo $error; }

    require($cesta."vlozeni-form-inc.php");
 
    echo "</form>";

 }


?>
  
  <!-- konec vnejsi tabulky -->
  </td>
  </tr>
  
 </table>

</body> 
</html> 

