<?php

require("include/main.function.shared.php");
require_once("include/config.php"); 
require_once("include/check_login.php");
require_once("include/check_level.php");

if ( !( check_level($level,128) ) )
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

<title>Adminator2 - Topology - smazání routeru</title> 

</head> 

<body> 

<? include ("head.php"); ?> 

<? include ("category.php"); ?>

  <tr>
   <td colspan="2" bgcolor="silver" >
      <? include("topology-cat2.php"); ?>
   </td>
  </tr>
    
  <tr>
   <td colspan="2">
   <!-- zde zacatek vlastniho obsahu -->																																																																																		       
  <?
  $erase_id=$_POST["erase_id"];
  
  $send = $_POST["send"];
  
  echo "<div style=\"padding-top: 20px; padding-bottom: 20px; font-size: 18px; \">";
  echo "Smazání routeru: </div>";
  
  // echo "erase_id: ".$erase_id;
  
  if ( $send )
  {

     if ( !( ereg('^([[:alnum:]]+)$',$erase_id) ) )
     { echo " Špatný formát proměnné erase_id"; exit; }

     $dotaz_routery = mysql_query("SELECT * FROM router_list WHERE parent_router = '$erase_id' ");
     $dotaz_routery_radku = mysql_num_rows($dotaz_routery);
     
     if( ( $dotaz_routery_radku > 0) )
     { 
       echo "<div style=\"color: red; font-size: 18px; font-weight: bold; padding-bottom: 10px; \">";
       echo "Router nelze smazat, je uveden jako parent router u jiného záznamu. </div>"; 
       exit; 
     }

     $dotaz_nody = mysql_query("SELECT * FROM nod_list WHERE router_id = '$erase_id' ");
     $dotaz_nody_radku = mysql_num_rows($dotaz_nody);
     
     if( ( $dotaz_nody_radku > 0) )
     { 
       echo "<div style=\"color: red; font-size: 18px; font-weight: bold; padding-bottom: 10px; \">";
       echo "Router nelze smazat, je k němu přiřazen nod/lokalita. </div>"; 
       exit; 
     }

        $pole3 = "<b>akce: smazání routeru;</b><br>";

	// prvne zjistime detaily mazane polozky
	$vyber=$conn_mysql->query("SELECT * FROM router_list WHERE id = '$erase_id' ");
	
	if( ( mysql_num_rows($vyber) <> 1 ) )
	{ $pole3 .= "predchozi data nelze zjistit, "; }
	else
	{
	  $pole3 .= "predchozi data: ";
	  
	 while($data_vyber=mysql_fetch_array($vyber) )
	 {
	  $pole3 .= " [id_routeru]=> ".$data_vyber["id"].", ";
	  $pole3 .= " [nazev]=> ".$data_vyber["nazev"].", ";
//	  $pole3 .= " [adresa]=> ".$data_vyber["adresa"].", ";
	  $pole3 .= " [ip_adresa]=> ".$data_vyber["ip_adresa"].", ";
	 
	 } //konec while
	} // konec else if pocet radku

        $res = mysql_query("DELETE FROM router_list WHERE id = '$erase_id' LIMIT 1");

        if ($res) { echo "<br><H3><div style=\"color: green; \" >Router úspěšně smazána.</div></H3><br>\n"; }
        else { echo "<div style=\"color: red; \">Chyba! Router nelze smazat. </div><br>\n"; }

        if ( $res == 1){ $vysledek_write=1; }
        $add=$conn_mysql->query("INSERT INTO archiv_zmen (akce,provedeno_kym,vysledek) VALUES ('$pole3','" . \Cartalyst\Sentinel\Native\Facades\Sentinel::getUser()->email . "','$vysledek_write')");
  
  } // konec if Send
  else
  {

   echo "<span style=\"font-size: 20px; \">Opravdu chcete odřadit tuto lokalitu / nodu: </span><br><br>";

   $dotaz=$conn_mysql->query("SELECT * FROM router_list WHERE id = '$erase_id' ");
   $dotaz_radku=$dotaz->num_rows;

   if ( $dotaz_radku == 0 )
   {   echo "<br>Chyba! Nelze nacist puvodni data o routeru! <br>"; }
   else
   {

    echo "<form action=\"".$_SERVER["PHP_SELF"]."\" method=\"POST\" >";

     while ($data=$dotaz->fetch_array() )
     {
	echo "<b>id nodu</b>: ".$data["id"]."<br>";
        echo "<b>jméno</b>: ".$data["nazev"]."<br>";
        echo "<b>adresa</b>: ".$data["ip_adresa"]."<br>";
     } // konec while

    echo "<input type=\"hidden\" name=\"erase_id\" value=\"".$erase_id."\" > ";
    echo "<br><br><input type=\"submit\" value=\"OK\" name=\"send\" >";
    echo "</form>";

   }// konec jestli jestli je radku nula

  } // konec else jestli se odeslalo

  ?> 
   <!-- konec vlastniho obsahu -->
   </td>
  </tr>
  
 </table>

</body> 
</html> 

