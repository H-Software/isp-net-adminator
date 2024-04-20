<?php

require("include/main.function.shared.php");
include ("include/config.php"); 
include ("include/check_login.php");

include ("include/check_level.php");

if ( !( check_level($level,49) ) )
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

<title>Adminator 2 - odendani objektu</title> 

</head> 

<body> 

<? include ("head.php"); ?> 

<? include ("category.php"); ?> 

 <tr>
 <td colspan="2" height="50">Odendání objektu</td>
  </tr>
 
  <tr>
  <td colspan="2">
  
  <?
  
  $send=$_GET["send"];
  $id_komplu=$_GET["id_komplu"];
  
  if ( $send )
  {
  
     if( !( preg_match('/^([[:alnum:]]+)$/',$id_komplu) ) ){ 
    	  echo " Špatný formát proměnné id_komplu"; 
	      exit; 
     }
     	    
	$pole3 = "<b>akce: odrazeni objektu; </b><br>";
	$pole3 .= " [id_komplu]=> ".$id_komplu."";
	
	$rs_dns =pg_query("SELECT id_cloveka, id_komplu FROM objekty WHERE id_komplu = '".intval($id_komplu)."'");
	$id_dns = pg_fetch_result($rs_dns, 0, 0);
	
	$pole3 .= " , [id_vlastnika] => ".$id_dns."";
	
	$obj_upd = array( "id_cloveka" => "" );
		    
	$obj_id = array( "id_komplu" => $id_komplu );
			  			       
	$res = pg_update($db_ok2, 'objekty', $obj_upd, $obj_id);
				    
	if($res) { echo "<br><H3><div style=\"color: green; \" >Data v databázi úspěšně změněny.</div></H3><br>\n"; }
	else { echo "<div style=\"color: red; \">Chyba! Data v databázi nelze změnit. </div><br>\n".pg_last_error($db_ok2); }
				    
	
	if ( $res == 1){ $vysledek_write="1"; }
	   
	$add=$conn_mysql->query("INSERT INTO archiv_zmen (akce,provedeno_kym,vysledek) VALUES ('$pole3','$nick','$vysledek_write')");
							      
  }
  else
  {
  
   echo "<br><span style=\"font-size: 20px; \">Opravdu chcete odřadit tento objekt: </span><br><br>";
  
  $dotaz=pg_query("SELECT * FROM objekty WHERE id_komplu='$id_komplu' ");
  
  $dotaz_radku=pg_num_rows($dotaz);
  
  if ( $dotaz_radku == 0 )
  {
  echo "<br>Chyba! Nelze nacist puvodni data o objektu! <br>";
  }
  else
  {
  
   echo "<form action=\"".$_SERVER["PHP_SELF"]."\" method=\"GET\" >";
   
    while ($data=pg_fetch_array($dotaz) )
    {
    
    echo "<b>dns jméno</b>: ".$data["dns_jmeno"]."<br><br>";
    
    echo "<b>ip adresa</b>: ".$data["ip"]."<br>";
    
    } // konec while
  
   echo "<input type=\"hidden\" name=\"id_komplu\" value=\"".$id_komplu."\" > ";
 
  echo "<br><br><input type=\"submit\" value=\"OK\" name=\"send\" >";
 
    echo "</form>";
    
  }// konec jestli jestli je radku nula
  
  } // konec else jestli se odeslalo
  
  ?>
  
  
   </td>
  </tr>
  
 </table>

</body> 
</html> 

