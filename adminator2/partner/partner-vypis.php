<?php

$cesta = "../";

require ($cesta."include/main.function.shared.php");
require ($cesta."include/config.php"); 
require ($cesta."include/check_login.php");

require ($cesta."include/check_level.php");

if ( !( check_level($level,76) ) )
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

<title>Adminator 2 :: Partner program</title> 

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

   require($cesta."include/c_listing-partner.php");

   require("/var/www/html/virtuals/partner/vypis-inc2.php");
	   
   global $list;

   // priprava form. promennych       
   $list=$_GET["list"];

   if( !(strlen($filtr_akceptovano) > 0) )
   { $filtr_akceptovano = $_GET["filtr_akceptovano"]; }
	 
  $filtr_pripojeno = $_GET["filtr_pripojeno"];

  //priprava dotazu              
  if( $filtr_akceptovano == 1 ){ $filtr .= " AND akceptovano = 1 "; }
  elseif( $filtr_akceptovano == 2 ){ $filtr .= " AND akceptovano = 0 "; }
			   
  if( $filtr_pripojeno == 1 ){ $filtr .= " AND pripojeno = 1 "; }
  elseif( $filtr_pripojeno == 2 ){ $filtr .= " AND pripojeno = 0 "; }
  
  $basic = "SELECT *,DATE_FORMAT(datum_vlozeni, '%d.%m.%Y %H:%i:%s') as datum_vlozeni2 FROM partner_klienti ";
  					     
  $dotaz_sql = $basic;
  
  if( isset($user) )
  { $dotaz_sql .= " WHERE ( vlozil = '$user_plaint' ".$filtr." ) "; }
  else
  { $dotaz_sql .= " WHERE ( id > 0 ".$filtr." ) "; }
  
  $dotaz_sql .= " ORDER BY id DESC ";																											 
	   
  $poradek = "filtr_akceptovano=".$filtr_akceptovano."&filtr_pripojeno=".$filtr_pripojeno;
	       
  $format_css = "font-size: 13px; padding-top: 5px; padding-bottom: 15px; ";
  
  //vytvoreni objektu
  $listovani = new c_Listing("./partner-vypis.php?".$poradek, 30, $list, "<center><div style=\"".$format_css."\">\n", "</div></center>\n", $dotaz_sql);
		       
  if (($list == "")||($list == "1")){ $bude_chybet = 0; }
  else{ $bude_chybet = (($list-1) * $listovani->interval); }
			     
  $interval = $listovani->interval;
				
  $dotaz_limit = " LIMIT ".intval($interval)." OFFSET ".intval($bude_chybet)." ";

  $dotaz_sql .= $dotaz_limit;
    
  $listovani->listInterval();
    
  $partner = new partner;
    
  $partner->show_legend(); // prom vyrizeni update asi zde prazdne
    
  $partner->show_art($filtr_akceptovano,$filtr_pripojeno,$dotaz_sql);
    
  $listovani->listInterval();    					         

  ?>
    
  </td>
  </tr>
 
 </table>

</body> 
</html> 
