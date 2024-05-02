<?php

require("include/main.function.shared.php");
require("include/config.php"); 

require("include/check_login.php");
require("include/check_level.php");

if ( !( check_level($level,102) ) )
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

<title>Adminator 2 - hledání</title> 

</head> 

<body> 

<?php include ("head.php"); ?> 

<?php include ("category.php"); ?> 


 <tr>
  <td colspan="2" height="20" bgcolor="silver" >
   <?php include("vlastnici-cat-inc.php"); ?>
  </td>
 </tr>
      
 <tr>
   <td colspan="2" height="25" >
   <span style="font-weight: bold; font-size: 14px; ">Vlastníci - globální hledání </span></td>
 </tr>
  
  <tr>
  <td colspan="2">
  
   <form method="GET" <?php echo 'action="'.$_SERVER["PHP_SELF"].'">'; ?>
   
      <table width="80%" height="80" border="0" >

     <tr>
         <td colspan="2"> <hr width="30%" align="left" > </td>
    </tr>
	    
   <tr>
    <td colspan="2">

    <input type="radio" name="select" value="1"
    <?php if ( ( !( isset($GET["select"] ) ) or ($_GET["select"] == 1 ) ) ){ echo " checked "; } ?>
    ><label>Všichni</label> |

    <input type="radio" name="select" value="4"
    <?php if ( $_GET["select"] == 4 ){ echo " checked "; } ?>
    ><label> Neplatí(free) </label> |

    <input type="radio" name="select" value="5"
    <?php if ( $_GET["select"] == 5 ){ echo " checked "; } ?>
    ><label> Platí </label> |
    

    <span style="padding-left: 5px; padding-right: 5px; ">Řadit dle:</span>

    <select name="razeni" size="1" >
    
            <option value="1" <? if ( ( $_GET["razeni"] == 1) or !isset($_GET["razeni"]) ) { echo " selected "; } ?> > id klienta  </option>
	    <option value="3" <? if ($_GET["razeni"] == 3) { echo " selected "; } ?> > jména  </option>
	    <option value="4" <? if ($_GET["razeni"] == 4) { echo " selected "; } ?> > Příjmení  </option>
	    <option value="5" <? if ($_GET["razeni"] == 5) { echo " selected "; } ?> > Ulice  </option>
	    <option value="6" <? if ($_GET["razeni"] == 6) { echo " selected "; } ?> > Město  </option>
	    <option value="14" <? if ($_GET["razeni"] == 14) { echo " selected "; } ?> > Var. symbol  </option>
	    <option value="15" <? if ($_GET["razeni"] == 15) { echo " selected "; } ?> > K platbě  </option>
							    
    </select>
    <span style="padding-left: 7px; "></span>
    <select name="razeni2" size="1" >
						
        <option value="1" <? if ($_GET["razeni2"] == 1) { echo " selected "; } ?> > vzestupně  </option>
        <option value="2" <? if ($_GET["razeni2"] == 2) { echo " selected "; } ?>  > sestupně  </option>
									
    </select>
				    
    </td>
   </tr>


     <tr>
         <td colspan="2"> <hr width="30%" align="left" > </td>
    </tr>
	    
	      
          <tr>
	    <td> <input type="submit" value="NAJDI" name="najdi"> </td>
	    <td>  <label>Hledání : </label><input type="text" name="find" 
	    <? 
	    if (empty($_GET["find"]) ){ echo 'value="%"'; } 
	    else{  echo 'value="'.$_GET["find"].'" >'; }
	    ?>
	    </td>
	  </tr>
	
	<?php
		
	$find=$_GET["find"];
	$najdi=$_GET["najdi"];
	
	// $delka_find_id=strlen($find_id);
	
	$sql=$_GET["find"];
	
	if( ( ! isset($najdi) ) )
	{ echo "Zadejte výraz k vyhledání.... <br>"; Exit; }
	
	vlastnikfind::vypis_tab(1);
		
	    $sql="%".$sql."%";
	    $select1 = " WHERE ( firma is  NULL OR firma = 0 ) AND ( archiv = 0 or archiv is null ) AND ";
	    $select1 .= " ( nick LIKE '$sql' OR jmeno LIKE '$sql' OR prijmeni LIKE '$sql' OR ulice LIKE '$sql' ";
	    $select1 .= " OR mesto LIKE '$sql' OR poznamka LIKE '$sql' ";
	    
	    $select2=" OR psc LIKE '$sql' OR icq LIKE '$sql' OR mail LIKE '$sql' OR telefon LIKE '$sql' ";
	    $select2 .= "OR vs LIKE '$sql' ) ";
			 
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
						     
		$dotaz_source = " SELECT * FROM vlastnici ".$select1.$select2.$select3.$select4;
	  
		if ( ( strlen($select5) > 1 ) ){ $dotaz_source = $dotaz_source.$select5; }
		  
	//  $poradek="find=".$find."&find_id=".$find_id."&najdi=".$_GET["najdi"]."&select=".$_GET["select"]."&razeni=".$_GET["razeni"]."&razeni2=".$_GET["razeni2"];
	
	echo "<tr><td colspan=\"10\"><hr></td></tr>";	      
	echo "<tr><td colspan=\"10\" height=\"40px\" ><span style=\"font-size: 20px; font-weight: bold; color: navy; \" >
	Výsledek hledání výrazu: \"".$sql."\" v sekci \"vlastníci\"</span></td></tr>";
				
					      
	  $vlastnik = new vlastnikfind;
	  $vlastnik->vypis($sql,$dotaz_source);
	
	echo "<tr><td colspan=\"10\"><hr></td></tr>";	      
	echo "<tr><td colspan=\"10\" height=\"40px\" ><span style=\"font-size: 20px; font-weight: bold; color: navy; \" >
	Výsledek hledání výrazu: \"".$sql."\" v sekci \"vlastníci2\"</span></td></tr>";
	
	$sql="".$sql."";
	$select1 = " WHERE firma is not NULL AND ( archiv = 0 or archiv is null ) AND ";
	$select1 .= " ( nick LIKE '$sql' OR jmeno LIKE '$sql' OR prijmeni LIKE '$sql' ";
	$select1 .= " OR ulice LIKE '$sql' OR mesto LIKE '$sql' OR poznamka LIKE '$sql' ";
	
	$select2 = " OR psc LIKE '$sql' OR icq LIKE '$sql' OR mail LIKE '$sql' OR telefon LIKE '$sql' ";
	$select2 .= " OR vs LIKE '$sql') ";

	$dotaz_source = " SELECT * FROM vlastnici ".$select1.$select2.$select3.$select4;
	
	if ( ( strlen($select5) > 1 ) ){ $dotaz_source = $dotaz_source.$select5; }
		
	 $vlastnik2 = new vlastnikfind;
	 $vlastnik2->vypis($sql,$dotaz_source);
	  
	echo "<tr><td colspan=\"10\"><hr></td></tr>";	      
	echo "<tr><td colspan=\"10\" height=\"40px\" ><span style=\"font-size: 20px; font-weight: bold; color: navy; \" >
	Výsledek hledání výrazu: \"".$sql."\" v sekci \"Fakturační\"</span></td></tr>";
	
	$sql="".$sql."";
			 
	$dotaz_source = "26058677";
	
	$vlastnik2 = new vlastnikfind;
	$vlastnik2->vypis($sql,$dotaz_source,"2");
	    
	// konec tabulky								 
        vlastnikfind::vypis_tab(2);
	
	?>
	
	
  <!-- konec hlavni tabulky -->
  </td>
  </tr>
  
 </table>

</body> 
</html> 

