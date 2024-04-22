<?php

require("include/main.function.shared.php");
require("include/config.php"); 
require("include/check_login.php");
require("include/check_level.php");

// require_once("include/c_listing-vlastnici2.php");

if( !( check_level($level,38) ) )
{
 // neni level
 header("Location: nolevelpage.php");
 
 echo "<br>Neopravneny pristup /chyba pristupu. STOP <br>";
 exit;
      
}

echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
      <html> 
      <head> ';

require_once("include/charset.php");

?>

<title>Adminator 2 :: Vlastníci2</title>

</head>

<body>

<?php require("head.php"); ?>

<?php require("category.php"); ?>

 <tr>
  <td colspan="2" height="20" bgcolor="silver" >
   <?php  require("vlastnici-cat-inc.php"); ?>
  </td>
 </tr>

  <tr>
  <td colspan="2">
  <!-- zacatek vlastniho obsahu -->
  <?php
	if (!$db_ok2) {
		die("An error occurred. The connection with pqsql does not exist.\n <br> (type of handler variable: " . gettype($db_ok2) . ")");
	}

	if (!$conn_mysql) {
		die("An error occurred. The connection with mysql does not exist.\n <br> (type of handler variable: " . gettype($conn_mysql) . ")");
	}
  ?>

   <form method="GET" action="">
      
   <div> <hr width="250px" align="left" ></div>
    
   <div style="float: left;" >
    
    <input type="radio" name="select" value="1"
    <?php if ( ( !( isset($GET["select"] ) ) or ($_GET["select"] == 1 ) ) ){ echo " checked "; } ?>
    ><label>Všichni</label> |

    <input type="radio" name="select" value="2"
    <?php if ( $_GET["select"] == 2){ echo " checked "; }  ?>
    ><label> FÚ </label> |

    <input type="radio" name="select" value="3"
    <?php if ( $_GET["select"] == 3 ){ echo " checked "; } ?>
    ><label> DÚ </label> |

    <input type="radio" name="select" value="4"
    <?php if ( $_GET["select"] == 4 ){ echo " checked "; } ?>
    ><label> Neplatí(free) </label> |

    <input type="radio" name="select" value="5"
    <?php if ( $_GET["select"] == 5 ){ echo " checked "; } ?>
    ><label> Platí </label> |
   
   </div>

   <div style="float: left; padding-left: 5px; padding-right: 5px; ">Fakturační skupina: </div>
    
   <div style="float: left; padding-left: 5px; "></div>
    
   <div style="float: left; " >
      
    <select name="fakt_skupina" size="1" style="width: 150px;" >

        <option value="0" class="vlastnici2-fakt-skupina" <?php if ($_GET["fakt_skupina"] == 0) { echo " selected "; } ?> > žádná </option>
						
	<?php
	
	$fu_sql_base = " SELECT * FROM fakturacni_skupiny ";
	if( $_GET["select"] == 2)
	{ $fu_sql_select .= " WHERE typ = '2' "; } //Pouze FU
	if( $_GET["select"] == 3 )
	{ $fu_sql_select .= " WHERE typ = '1' "; } //pouze DU
	
	try {
		$dotaz_fakt_skup=$conn_mysql->query($fu_sql_base." ".$fu_sql_select." ORDER BY nazev DESC");
		$dotaz_fakt_skup_radku=$dotaz_fakt_skup->num_rows;
	} catch (Exception $e) {
		die ("<h2 style=\"color: red; \">Error: Database query failed! Caught exception: " . $e->getMessage() . "\n" . "</h2></body></html>\n");
	}

	if( $dotaz_fakt_skup_radku > 0 )
	{
	    while( $data_fakt_skup=$dotaz_fakt_skup->fetch_array() )
	    {
	    
	    echo "\t\t<option value=\"".$data_fakt_skup["id"]."\" ";
	    if ($_GET["fakt_skupina"] == $data_fakt_skup["id"] ){ echo " selected "; }
	    
	    echo " > ".$data_fakt_skup["nazev"];
	    
	    if ($data_fakt_skup["typ"] == 1){ echo " (DÚ) "; }
	    elseif ($data_fakt_skup["typ"] == 2){ echo " (FÚ) "; }
	    
	    echo " </option>\n";
	    	    
	    }
	}
	
	?>
							
    </select>
  </div>
      
  <div style="float: left; padding-left: 5px; ">|</div>
    
  <div style="float: left; padding-left: 5px; padding-right: 5px; ">Řadit dle:</div>

  <div style="float: left; ">
    <select name="razeni" size="1" >
            <option value="1" <?php if( ( $_GET["razeni"] == 1) or !isset($_GET["razeni"]) ){ echo " selected "; } ?> > id klienta  </option>
	    <option value="3" <?php if( $_GET["razeni"] == 3 ){ echo " selected "; } ?> > jména  </option>
	    <option value="4" <?php if( $_GET["razeni"] == 4 ){ echo " selected "; } ?> > Příjmení  </option>
	    <option value="5" <?php if( $_GET["razeni"] == 5 ){ echo " selected "; } ?> > Ulice  </option>
	    <option value="6" <?php if( $_GET["razeni"] == 6 ){ echo " selected "; } ?> > Město  </option>
	    <option value="14" <?php if( $_GET["razeni"] == 14 ){ echo " selected "; } ?> > Var. symbol  </option>
	    <option value="15" <?php if( $_GET["razeni"] == 15 ){ echo " selected "; } ?> > K platbě  </option>
    </select> 
  </div>
      
  <div style="float: left; padding-left: 7px; ">

      <select name="razeni2" size="1" >
					
        <option value="1" <?php if( $_GET["razeni2"] == 1 ){ echo " selected "; } ?> > vzestupně </option>
        <option value="2" <?php if( $_GET["razeni2"] == 2 ){ echo " selected "; } ?>  > sestupně </option>
									
      </select>
  </div>
    
  <div style="clear: both;"></div>
  
  <div><hr width="250px" align="left" ></div>

  <div style="float: left; padding-right: 20px;" ><input type="submit" value="NAJDI" name="najdi"> </div>
  <div style="float: left;" ><label>Hledání : </label><input type="text" name="find" 
   <?php
      if(empty($_GET["find"]) ){ echo 'value="%" >'; } 
      else{ echo 'value="'.$_GET["find"].'" >'; }
   ?>
  </div>

  </form>

<div style="clear: both; border-bottom: 2px solid black; width: 250px; padding-top: 5px; margin-bottom: 5px; height: 2px;"></div>
	
  <table border="0" width="800px" >
    <tr>
     <td>				     
     
<?php

    //prvne vytvorime instanci objektu
     $vlastnik = new vlastnik2_a2;
     $vlastnik->level = $level;
	 $vlastnik->conn_mysql = $conn_mysql;
	 $vlastnik->conn_pgsql = $db_ok2;

    if( check_level($level,40) ) { echo '<a href="vlastnici2-change.php?firma_add=1">Přidání vlastníka</a>'; }
    else 
    { echo "<div style=\"color: grey; font-style: italic\">Přidání vlastníka</div>"; }
	
    echo "</td>";
	    
    if( check_level($level,63) ){ 
	$vlastnik->export_povolen="true"; 
    }
    
    // tafy generovani exportu
    if( $vlastnik->export_povolen )
    {     
	$vlastnik->export();

	echo "<td>";
	
	echo '<a href="export\vlastnici-sro.xls" >export dat zde</a>';
	
	echo "</td><td>";
	
	echo "<a href=\"include/export-ucetni.php\" > export ucetni </a>";
		
	echo "</td>
	
	     <td style=\"padding-left: 25px; \" >
	          <a href=\"admin-login-iptv.php\" target=\"_new\" >aktivace funkcí IPTV portálu (přihlašení)</a>
	     </td>
	                     
	</tr></table>";
	
    }
    	
	$find_id = $_GET["find_id"];
	$find    = $_GET["find"];
	
	// $delka_find_id=strlen($find_id);
	if( ( strlen($find_id) > 0 ) ) 
	{ $co=3; /* hledani podle id_cloveka */   $sql=intval($find_id);  }
	elseif( ( strlen($find) > 0 ) )
	{ $co=1;  /* hledani podle cehokoli */  $sql = $find;  }
	else
	{ /* cokoli dalsiho */ }
	
	//promena pro update objektu
	if( check_level($level,29) ) { $update_povolen="true"; }
	if( check_level($level,33) ) { $mazani_povoleno="true"; }
	if( check_level($level,34) ) { $garant_akce="true"; }
	
	// promeny pro mazani, zmenu vlastniku
	if( check_level($level,45) ) { $vlastnici_erase_povolen="true"; }
	if( check_level($level,30) ) { $vlastnici_update_povolen="true"; }
	
	// odendani objektu od vlastnika
	if( check_level($level,49) ) { $odendani_povoleno="true"; }
	    
	$vlastnik->vypis_tab(1);
	
	if($co==1)
	{
	     
	        $sql="%".$sql."%";
		$select1 = " WHERE (firma is not NULL) AND ( archiv = 0 or archiv is null ) AND ";
		$select1 .= " ( nick LIKE '$sql' OR jmeno LIKE '$sql' OR prijmeni LIKE '$sql' ";
		$select1 .= " OR ulice LIKE '$sql' OR mesto LIKE '$sql' OR poznamka LIKE '$sql' ";
		
		$select2=" OR psc LIKE '$sql' OR icq LIKE '$sql' OR mail LIKE '$sql' OR telefon LIKE '$sql' ";
		$select2 .= " OR vs LIKE '$sql' ) ";
			 
		if ( $_GET["select"] == 2){ $select3=" AND fakturacni > 0 "; }
		if ( $_GET["select"] == 3){ $select3=" AND fakturacni is NULL "; }
		if ( $_GET["select"] == 4){ $select3=" AND k_platbe = 0 "; }
		if ( $_GET["select"] == 5){ $select3=" AND k_platbe > 0 "; }
		
		if ( $_GET["select"] == 2){ $select3=" AND fakturacni > 0 "; }
		if ( $_GET["select"] == 3){ $select3=" AND fakturacni is NULL "; }
		if ( $_GET["select"] == 4){ $select3=" AND k_platbe = 0 "; }
		if ( $_GET["select"] == 5){ $select3=" AND k_platbe > 0 "; }
	
		if ( $_GET["razeni"] == 1){ $select4=" order by id_cloveka "; }
		if ( $_GET["razeni"] == 3){ $select4=" order by jmeno "; }
		if ( $_GET["razeni"] == 4){ $select4=" order by prijmeni "; }
		if ( $_GET["razeni"] == 5){ $select4=" order by ulice "; }
		if ( $_GET["razeni"] == 6){ $select4=" order by mesto "; }
		if ( $_GET["razeni"] == 14){ $select4=" order by vs "; }
		if ( $_GET["razeni"] == 15){ $select4=" order by k_platbe "; }
						 
		if ( $_GET["razeni2"] == 1){ $select5=" ASC "; }
		if ( $_GET["razeni2"] == 2){ $select5=" DESC "; }
	
		if ( $_GET["fakt_skupina"] > 0){ $select6=" AND fakturacni_skupina_id = ".intval($_GET["fakt_skupina"])." "; }
			     							 
		if ( (strlen($select4) > 1 ) ){ $select4=$select4.$select5; }
									     
		$dotaz_source = " SELECT *, to_char(billing_suspend_start,'FMDD. FMMM. YYYY') as billing_suspend_start_f,
					    to_char(billing_suspend_stop,'FMDD. FMMM. YYYY') as billing_suspend_stop_f 
				     FROM vlastnici ".$select1.$select2.$select3.$select6.$select4;
	  }
	  elseif($co==3){
	  		 
	     $dotaz_source= "SELECT *, to_char(billing_suspend_start,'FMDD. FMMM. YYYY') as billing_suspend_start_f, ".
	    		    " to_char(billing_suspend_stop,'FMDD. FMMM. YYYY') as billing_suspend_stop_f ".
	    		    " FROM vlastnici WHERE ( archiv = 0 or archiv is null ) AND id_cloveka = '$sql' "; 
	  }
	  else
	  { 
	    
	    echo "<tr><td>zadejte výraz k vyhledání....</td></tr>\n"; 
	    
	    echo "</table>\n";
	    
	    echo "</td></tr>\n\n";
	    
	    echo "</table>\n";
	    
	    echo "\n</body>\n</html>\n";
	    
	    exit; 
	  }
	        
	  $list=$_GET["list"];
		  
	  $poradek="find=".$find."&find_id=".$find_id."&najdi=".$_GET["najdi"]."&select=".$_GET["select"]."&razeni=".
	    		    $_GET["razeni"]."&razeni2=".$_GET["razeni2"]."&fakt_skupina=".$_GET["fakt_skupina"];
		      
	  //vytvoreni objektu
	  $listovani = new c_listing_vlastnici2("./vlastnici2.php?".$poradek."&menu=1", 30, $list, "<center><div class=\"text-listing2\">\n", "</div></center>\n", $dotaz_source);
		      
	  if(($list == "")||($list == "1")){    //pokud není list zadán nebo je první
	    $bude_chybet = 0;                  //bude ve výběru sql dotazem chybet 0 záznamů
	  }
	  else{
	    $bude_chybet = (($list-1) * $listovani->interval);    //jinak jich bude chybet podle závislosti na listu a intervalu
	  }
							     
	  $interval=$listovani->interval;

	  $dotaz_final=$dotaz_source." LIMIT " . intval($interval) . " OFFSET " . intval($bude_chybet) . " ";
					   
//	  $listovani->listInterval();
	  $listovani->listPart();
    
	  $vlastnik->vypis($sql,$co,$dotaz_final);
									 
	  $vlastnik->vypis_tab(2);
	
//    	  $listovani->listInterval();
          $listovani->listPart();
	
	?>
	
	
  <!-- konec hlavni tabulky -->
  </td>
  </tr>
  
 </table>

</body> 
</html> 

