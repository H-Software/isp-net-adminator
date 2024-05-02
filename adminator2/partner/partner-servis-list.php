<?php

$cesta = "../";

require ($cesta."include/main.function.shared.php");
require_once ($cesta."include/config.php"); 
require ($cesta."include/check_login.php");
require ($cesta."include/check_level.php");

// $level_col = "lvl_partner_servis_list";

if( !( check_level($level, 305) ) )
{ // neni level
  header("Location: ".$cesta."nolevelpage.php");

  echo "<br>Neopravneny pristup /chyba pristupu. STOP <br>";
  exit;
}

echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"> 
      <html> 
      <head> ';

require ($cesta."include/charset.php"); 

?>

  <title>Adminator 2 - partner - servis výpis</title> 

</head> 

<body>

<?php require ($cesta."head.php"); ?> 

<?php require ($cesta."category.php"); ?> 

 <tr>
   <td colspan="2" height="20" bgcolor="silver" >
    
	<?php require ("partner-servis-cat.php"); ?>
		
   </td>
 </tr>
  
 <tr>
    <td colspan="2" >
    
<?php
 
 $ps = new partner_servis($conn_mysql);
 
 //priprava form. promennych
 
 $list = intval($_GET["list"]);
 $filtr_akceptovano = intval($_GET["filtr_akceptovano"]);
 $filtr_prio = intval($_GET["filtr_prio"]);

 //priprava dotazu
 
 if($filtr_akceptovano > 0){ $filtr .= " AND akceptovano = ".$filtr_akceptovano." "; }
 if($filtr_prio > 0){ $filtr .= " AND prio = ".$filtr_prio." "; }

 $basic = "SELECT tel, jmeno, adresa, email, poznamky, prio, vlozil, akceptovano, ".
	  "akceptovano_kym, akceptovano_pozn, DATE_FORMAT(datum_vlozeni, '%d.%m.%Y %H:%i:%s') ".
	  "as datum_vlozeni2 FROM partner_klienti_servis ";
	  
 $dotaz_sql = $basic;

 if( isset($user) )
 { $dotaz_sql .= " WHERE ( vlozil = '".$conn_mysql->real_escape_string($user_plaint)."' ".$filtr." ) "; }
 else
 { $dotaz_sql .= " WHERE ( id > 0 ".$filtr." ) "; }

  $dotaz_sql .= " ORDER BY id DESC ";
 
  $poradek = "filtr_akceptovano=".$filtr_akceptovano."&filtr_prio=".$filtr_prio;

  $format_css = "font-size: 13px; padding-top: 5px; padding-bottom: 15px; ";

  //vytvoreni objektu
  $listovani = new c_listing_partner_servis($conn_mysql, "./partner-servis-list.php?".$poradek, 30, $list, "<center><div style=\"".$format_css."\">\n", "</div></center>\n", $dotaz_sql);

  if(($list == "")||($list == "1")){ $bude_chybet = 0; }
  else{ $bude_chybet = (($list-1) * $listovani->interval); }
  
  $interval = $listovani->interval;
  
  $dotaz_limit = " LIMIT ".$interval." OFFSET ".$bude_chybet." ";

  $dotaz_sql .= $dotaz_limit;

  $logger->info("parner-service-list: final SQL: " . var_export($dotaz_sql, true));

  $listovani->listInterval();

  $ps->list_show_legend(); // promena vyrizeni a update asi zde prazdne

  $ps->list_show_items($filtr_akceptovano,$filtr_prio,$dotaz_sql);

  $listovani->listInterval();
 
?>
    <!-- konec vnejsi tabulky -->
    </td>
 </tr>
  
 </table>

</body> 
</html> 
