  
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
	    <?php
	    if (empty($_GET["find"]) ){ echo 'value="%"'; } 
	    else{  echo 'value="'.$_GET["find"].'" >'; }
	    ?>
	    </td>
	  </tr>
	
	<?php
				
	// body 2		      
	//   $vlastnik = new vlastnikfind;
	//   $vlastnik->vypis($sql,$dotaz_source);
	

	// echo "<tr><td colspan=\"10\"><hr></td></tr>";	      
	// echo "<tr><td colspan=\"10\" height=\"40px\" ><span style=\"font-size: 20px; font-weight: bold; color: navy; \" >
	// Výsledek hledání výrazu: \"".$sql."\" v sekci \"vlastníci2\"</span></td></tr>";

	// b3
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
	
	// body 4
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

