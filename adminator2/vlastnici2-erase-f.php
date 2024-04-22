<?php

require("include/main.function.shared.php");
include ("include/config.php"); 
include ("include/check_login.php");

include ("include/check_level.php");

if ( !( check_level($level,67) ) )
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

include ("include/charset.php"); 

?>

<title>Adminator 2</title> 

</head> 

<body> 

<? include ("head.php"); ?> 

<? include ("category.php"); ?> 

 
<!--
 <tr>
 <td colspan="2" height="50"></td>
  </tr>
  -->
  
  <tr>
  <td colspan="2">
  <!-- zacatek vlastniho obsahu -->
  
  <?
  
  $id=$_GET["id"];
  
  if ( preg_match('/^([[:digit:]]+)$/',$id) )
  {
  
   $erase_eq = array( "id" => $id );
   
    $res=pg_delete($db_ok2,'fakturacni', $erase_eq);
    
    if ($res) { echo "<br><H3><div style=\"color: green; \" > Fakturační adresa smazána. </div></H3>\n"; }
	       else { echo "<div style=\"color: red; \">Chyba! Fakturační adresu nelze smazat. </div><br>\n".pg_last_error($db_ok2); }
	      
    // ted to ulozime
	      
     // pridame to do archivu zmen
     $pole = " akce: smazani fakturacni adresy ; ";

     foreach ($erase_eq as $key => $val) { $pole=$pole." [".$key."] => ".$val."\n"; }

    $datum = strftime("%d/%m/%Y %H:%M:%S", time());
    
     $pole .= $pole2.", akci provedl: ".$nick.", vysledek akce dle postgre: ".$res.", datum akce: ".$datum;
		 
    $add=mysql_query("INSERT INTO archiv_zmen (akce) VALUES ('$pole')");
		     
  
    echo "<br><br>";
    
  }
  else
  {

    echo " Chybné vstupní údaje. ";
    
  }
  
  ?>
  
  <!-- konec vlastniho obsahu -->
  </td>
  </tr>
  
 </table>

</body> 
</html> 

