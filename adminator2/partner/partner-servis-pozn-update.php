<?php

$cesta = "../";

require ($cesta."include/main.function.shared.php");
require ($cesta."include/config.php");
require ($cesta."include/check_login.php");
require ($cesta."include/check_level.php");

$level_col = "lvl_partner_servis_pozn_update";

if( !( check_level($level, 307) ) )
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

<title>Adminator 2 - partner  - servis - pozn update</title> 

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
   
   if($_GET["edit"] <> 1)
   {

        $list = intval($_GET["list"]);
        $filtr_prio = intval($_GET["filtr_prio"]);
	      $filtr_akceptovano = 1;
	
        //priprava dotazu

        if($filtr_akceptovano > 0){ $filtr .= " AND akceptovano = ".$filtr_akceptovano." "; }
        if($filtr_prio > 0){ $filtr .= " AND prio = ".$filtr_prio." "; }

        $basic = "SELECT id, tel, jmeno, adresa, email, poznamky, prio, vlozil, akceptovano, ".
          "akceptovano_kym, akceptovano_pozn, DATE_FORMAT(datum_vlozeni, '%d.%m.%Y %H:%i:%s') ".
          "as datum_vlozeni2 FROM partner_klienti_servis ";

        $dotaz_sql = $basic;

        if( isset($user) )
        { $dotaz_sql .= " WHERE ( vlozil = '".$conn_mysql->real_escape_string($user_plaint)."' ".$filtr." ) "; }
        else
        { $dotaz_sql .= " WHERE ( id > 0 ".$filtr." ) "; }

        $dotaz_sql .= " ORDER BY id DESC ";

        $poradek = "filtr_akceptovano=".$filtr_akceptovano."&filtr_prio=".$filtr_prio;

        // $format_css = "font-size: 13px; padding-top: 5px; padding-bottom: 15px; ";

        // //vytvoreni objektu
        // $listovani = new c_listing_partner_servis($conn_mysql, "./partner-servis-list.php?".$poradek, 30, $list, "<center><div style=\"".$format_css."\">\n", "</div></center>\n", $dotaz_sql);

        // if(($list == "")||($list == "1")){ $bude_chybet = 0; }
        // else{ $bude_chybet = (($list-1) * $listovani->interval); }

        // $interval = $listovani->interval;

        // $dotaz_limit = " LIMIT ".intval($interval)." OFFSET ".intval($bude_chybet)." ";

        // $dotaz_sql .= $dotaz_limit;

        // $listovani->listInterval();

	$ps->update = true;

	$ps->list_show_legend();
        
	$ps->list_show_items($filtr_akceptovano,$filtr_prio,$dotaz_sql);

	// $listovani->listInterval();

   } //konec if update_id > 0
   else
   {

    echo "<div style=\"padding-left: 40px; padding-bottom: 20px; padding-top: 20px; font-weight: bold; font-size: 18px; \">
               <span style=\"border-bottom: 1px solid grey; \" >Změna poznámky technika</span>
	    </div>";
	
    if ( !( ereg('^([[:digit:]])+$',$_GET["id"]) ) )
    {
      echo "<div style=\"color: red; font-weight: bold; padding-left: 20px; padding-bottom: 20px; \">
        Chyba! Zákazníka nelze akceptovat! Vstupní data jsou ve špatném formátu! </div> ";
      
      exit;
    }
    
    if( $_GET["odeslat"] == "OK" )
    {
    //budem ukladat
	    
        $pozn = $conn_mysql->real_escape_string($_GET["pozn"]);
        $id = intval($_GET["id"]);
	
        $uprava=$conn_mysql->query("UPDATE partner_klienti_servis SET akceptovano_pozn = '".$pozn."' WHERE id=".$id." Limit 1 ");
  
       if($uprava == 1){
    	  echo "<br><H3><div style=\"color: green; padding-left: 20px;\" >Poznámka u zákazníka úspěšně aktualizována.</div></H3><br>\n"; 
       }
       else
       { 
    	  echo "<div style=\"color: red; \">Chyba! Poznámku nelze upravit.</div><br>\n";
       
          //echo mysql_error($uprava); 
       }
    
    } // konec if odeslat == "OK"
    else
    { //zobrazime form pro poznamku
	
      $id = intval($_GET["id"]);
      
      //nacteme predchozi data
      $dotaz=$conn_mysql->query("SELECT id, akceptovano_pozn FROM partner_klienti_servis WHERE id = '$id' ");
      
      while( $data = $dotaz->fetch_array() )
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

