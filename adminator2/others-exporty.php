<?php

require_once ("include/config.php"); 
require_once ("include/check_login.php");

require_once ("include/check_level.php");

if( !( check_level($level,122) ) )
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

require ("include/charset.php");

?>

<title>Adminator 2 - Exporty</title> 

</head> 

<body> 

<?php require ("head.php"); ?> 

<?php require ("category.php"); ?> 

 <tr>
  <td colspan="2" bgcolor="silver" height=""><?php require("others-subcat-inc.php"); ?></td>
 </tr>
  
  <tr>
  <td colspan="2">
  <!-- zacatek vlastniho obsahu -->
  
  <?php
  
   echo "<div style=\"padding-top: 10px; \">
           <span style=\"font-weight: bold; font-size: 18px; border-bottom: 1px solid grey; \" >Exporty</span>
	   
	   <span style=\"border-bottom: 1px solid grey; \"> - exporty emailů</span>
	
	 </div>";

    echo "<div style=\"padding-top: 10px; padding-left: 5px; padding-bottom: 20px; \">";

    echo "<div style=\"padding-top: 10px; \" >
	    <span style=\" color: grey; \" >Navigace:</span>
	
	    <span style=\"padding-left: 20px; \" ><a href=\"".$_SERVER["PHP_SELF"]."?item=1\" >export emailů</a></span>
	
	    <span style=\"padding-left: 20px; \" ><a href=\"".$_SERVER["PHP_SELF"]."?item=2\" >export - telefonní seznam</a></span>
	
	</div>";
    
    $item = $_GET["item"];
    
    $vlastnici = $_GET["vlastnici"];
    
    $vlastnici2 = $_GET["vlastnici2"];
    $vlastnici_archiv  = $_GET["vlastnici_archiv"];
    
    $select_fakturacni = $_GET["select_fakturacni"];
    
    $select_fakturacni_skupina = intval($_GET["select_fakturacni_skupina"]);
    
    if( $item == 1)
    {
     echo "<form action=\"\" method=\"GET\" >
	    <input type=\"hidden\" name=\"item\" value=\"".intval($item)."\" > 
	    
	    <div style=\"padding-top: 20px; \" >
		<span style=\"color: grey; \">filtr: </span>
	
		<span style=\"padding-left: 20px; \">výběr typu vlastníka: </span>
	
		<span style=\"padding-left: 20px; \">Vlastníci: </span>
	
		<span style=\"padding-left: 5px; \">
		    <input type=\"checkbox\" name=\"vlastnici\" ";
		if( $vlastnici == "on" ){ echo " checked=\"checked\" "; }    
		echo " >
		</span>
	
		<span style=\"padding-left: 10px; color: grey; \"> | </span>
		<span style=\"padding-left: 10px; \">Vlastníci2: </span>
	
		<span style=\"padding-left: 5px; \">
		    <input type=\"checkbox\" name=\"vlastnici2\" ";
		if( $vlastnici2 == "on" ){ echo " checked=\"checked\" "; }
		echo " >
		</span>
	        
		<span style=\"padding-left: 10px; color: grey; \"> | </span>
		<span style=\"padding-left: 10px; \">Vlastníci archiv: </span>
	
		<span style=\"padding-left: 5px; \">
		    <input type=\"checkbox\" name=\"vlastnici_archiv\" ";
		if( $vlastnici_archiv == "on" ){ echo " checked=\"checked\" "; }
		echo " >
		</span>
	
		<span style=\"padding-left: 50px; \">
		    <input type=\"submit\" name=\"odeslano\" value=\"OK\" >
		</span>
	
	    
	    </div>";
     
     $sql_export = "select mail from vlastnici where ( ( mail is not null) and ( mail not like 'NULL') ";
     
     if( ( ($vlastnici == "on") and ($vlastnici2 == "on") and ($vlastnici_archiv == "on") ) )
     { }
     elseif( ( ($vlastnici == "on") and ($vlastnici2 == "on") ) )
     { $sql_export .= " and ( (archiv = 0) or ( archiv is null ) ) "; }
     elseif( ( ($vlastnici == "on") and ($vlastnici_archiv == "on") ) )
     { $sql_export .= " and ( ( firma is NULL ) or ( archiv = 1 ) ) "; }
     elseif( ( ($vlastnici2 == "on") and ($vlastnici_archiv == "on") ) )
     { $sql_export .= " and ( ( firma = 1 ) or ( archiv = 1 ) ) "; }
     elseif( ( $vlastnici == "on" ) )
     { $sql_export .= " and ( firma is NULL ) and ( (archiv = 0) or ( archiv is null ) ) "; }
     elseif( ( $vlastnici2 == "on" ) )
     { $sql_export .= " and ( firma = 1 ) and ( (archiv = 0) or ( archiv is null ) ) "; }
     elseif( $vlastnici_archiv == "on" )
     { $sql_export .= " and ( archiv = 1 ) "; }
     else
     { 
       $sql_export .= " and ( firma <> 1 ) and ( firma is not null )  ";
       $sql_export .= " and ( archiv <> 1 ) and ( archiv is not null) "; 
       // $sql_export .= " and ( ( archiv <> 1 ) or ( archiv is not null) ) "; 
     
     }
     
     $sql_export .= " ) ";
     
     $dotaz_export=pg_query($sql_export);
     $dotaz_export_radku=pg_num_rows($dotaz_export);
    
     echo "<div style=\"padding-top: 10px; color: grey; \">";
     echo "debug: sql: ".$sql_export.", radku: ".$dotaz_export_radku."</div>";
     
     define ("EXPORT", "export/export-maily.csv");
     $soubor=fopen(EXPORT, "w");
    
     if($soubor === false) {
        echo "<div style=\"color: red;\">CHYBA! Soubor pro export nelze otevřít.</div>";
     }
         
     while( $data = pg_fetch_array($dotaz_export) )
     { 
      $email = $data["mail"]."; \n";
      fwrite($soubor,  $email); 
     }

     fclose($soubor);
    
     echo "<div style=\"padding-top: 20px; padding-left: 15px;\" >";
     echo "<a href=\"export/export-maily.csv\" >export emailů zde</a></div>";
    
     echo "</form>";
     
    } // konec if item == 1
    elseif($item == 2) {
    
     echo "<form action=\"\" method=\"GET\" >
	    <input type=\"hidden\" name=\"item\" value=\"".intval($item)."\" > 
	    
	    <div style=\"padding-top: 20px; \" >
		<span style=\"color: grey; \">filtr: </span>
	
		<span style=\"padding-left: 20px; \">výběr typu vlastníka: </span>
	
		<span style=\"padding-left: 20px; \">Vlastníci: </span>
	
		<span style=\"padding-left: 5px; \">
		    <input type=\"checkbox\" name=\"vlastnici\" ";
		if( $vlastnici == "on" ){ echo " checked=\"checked\" "; }    
		echo " >
		</span>
	
		<span style=\"padding-left: 10px; color: grey; \"> | </span>
		<span style=\"padding-left: 10px; \">Vlastníci2: </span>
	
		<span style=\"padding-left: 5px; \">
		    <input type=\"checkbox\" name=\"vlastnici2\" ";
		if( $vlastnici2 == "on" ){ echo " checked=\"checked\" "; }
		echo " >
		</span>
	        
		<span style=\"padding-left: 10px; color: grey; \"> | </span>
		<span style=\"padding-left: 10px; \">Vlastníci archiv: </span>
	
		<span style=\"padding-left: 5px; \">
		    <input type=\"checkbox\" name=\"vlastnici_archiv\" ";
		if( $vlastnici_archiv == "on" ){ echo " checked=\"checked\" "; }
		echo " >
		</span>
	
		<span style=\"padding-left: 50px; \">
		    <input type=\"submit\" name=\"odeslano\" value=\"OK\" >
		</span>";
	
		//druha radka
		echo "<div style=\"height: 10px;\">&nbsp;</div>";
		
		echo "<div style=\"float: left; width: 46px;\">&nbsp;</div>
		      
		      <div style=\"float: left; \">fakturační: </div>";
		      
		echo "<span style=\"float: left; padding-left: 85px; \">
			<select size=\"1\" name=\"select_fakturacni\">
			    <option value=\"0\""; if(!isset($select_fakturacni)){ echo " selected "; } echo ">nevybráno</option>
			    <option value=\"1\""; if($select_fakturacni == 1){ echo " selected "; } echo ">jen fakturační</option>
			    <option value=\"2\""; if($select_fakturacni == 2){ echo " selected "; } echo ">jen NE-fakturační</option>
			</select>
		      </span>";
		
		echo "<div style=\"clear: both; height: 10px;\">&nbsp;</div>";
		
		//treti radka
		echo "<div style=\"float: left; width: 46px;\">&nbsp;</div>
		      
		      <div style=\"float: left; \">Fakturační skupina: </div>";
		
		echo "<span style=\"float: left; padding-left: 30px; \">
			<select size=\"1\" name=\"select_fakturacni_skupina\">
			    <option value=\"0\""; if(!isset($select_fakturacni_skupina)){ echo " selected "; } echo ">nevybráno</option>";

		    //výpis fakt. skupin
		    $rs_fs = mysql_query("SELECT id, nazev, typ FROM fakturacni_skupiny ORDER BY nazev");
		    
		    while($data_fs = mysql_fetch_array($rs_fs)){
			
			if($data_fs["typ"] == 1){
			    $typ = "DU";
			}
			elseif($data_fs["typ"] == 2){
			    $typ = "FU";
			}
			else{
			    $typ = "??";
			}
			
			echo "<option value=\"".intval($data_fs["id"])."\" ";
			if( $select_fakturacni_skupina == $data_fs["id"]) echo " selected ";
			echo ">".htmlspecialchars($data_fs["nazev"]).
			     "(".htmlspecialchars($typ).")</option>";
		    
		    } //konec while
		    
		echo "	</select>
		      </span>";
		
		echo "<div style=\"height: 10px;\">&nbsp;</div>";
		
	echo "</div>";
    
        echo "</form>";
    
    	$sql_export = "SELECT jmeno, prijmeni, ulice, telefon FROM vlastnici WHERE ( (telefon IS NOT NULL) ";
     
        if( ( ($vlastnici == "on") and ($vlastnici2 == "on") and ($vlastnici_archiv == "on") ) )
        { }
        elseif( ( ($vlastnici == "on") and ($vlastnici2 == "on") ) )
        { $sql_export .= " and ( (archiv = 0) or ( archiv is null ) ) "; }
        elseif( ( ($vlastnici == "on") and ($vlastnici_archiv == "on") ) )
        { $sql_export .= " and ( ( firma is NULL ) or ( archiv = 1 ) ) "; }
        elseif( ( ($vlastnici2 == "on") and ($vlastnici_archiv == "on") ) )
        { $sql_export .= " and ( ( firma = 1 ) or ( archiv = 1 ) ) "; }
        elseif( ( $vlastnici == "on" ) )
        { $sql_export .= " and ( firma is NULL ) and ( (archiv = 0) or ( archiv is null ) ) "; }
        elseif( ( $vlastnici2 == "on" ) )
        { $sql_export .= " and ( firma = 1 ) and ( (archiv = 0) or ( archiv is null ) ) "; }
        elseif( $vlastnici_archiv == "on" )
        { $sql_export .= " and ( archiv = 1 ) "; }
        else
        {	 
    	    $sql_export .= " and ( firma <> 1 ) and ( firma is not null )  ";
    	    $sql_export .= " and ( archiv <> 1 ) and ( archiv is not null) "; 
    	    // $sql_export .= " and ( ( archiv <> 1 ) or ( archiv is not null) ) "; 
     
        }
     
        //druha radka - fakturacni/nefakturacni
        
        if( $select_fakturacni == 1)
        { $sql_export .= " and (fakturacni > 0 ) "; }
        elseif($select_fakturacni == 2)
        { $sql_export .= " and (fakturacni is NULL) "; }
        
        //treti radka - fakturacni skupina
        if( $select_fakturacni_skupina > 0)
        { $sql_export .= " and (fakturacni_skupina_id = ".intval($select_fakturacni_skupina)." ) "; }
        
        $sql_export .= " ) ";
     
        $dotaz_export=pg_query($sql_export);
        $dotaz_export_radku=pg_num_rows($dotaz_export);
    
        echo "<div style=\"padding-top: 10px; color: grey; \">";
        echo "debug: sql: ".$sql_export.", radku: ".$dotaz_export_radku."</div>";
        
        define ("EXPORT", "export/export-telefonni-seznam.csv");
        $soubor=fopen(EXPORT, "w");
    
        if($soubor === false) {
    	    echo "<div style=\"color: red;\">CHYBA! Soubor pro export nelze otevřít.</div>";
        }
         
        while( $data = pg_fetch_array($dotaz_export) )
        { 
    	    if((preg_match("/^(\d){9,9}$/", $data["telefon"]) == 1))
    	    //if( strlen($data["telefon"]) > 1 )
    	    {
    		
    		$out_cp = "cp1250";
    		
    		$jmeno = $data["jmeno"];
    		$prijmeni = $data["prijmeni"];
    		$ulice = $data["ulice"];
    		
    		//$jmeno = iconv("UTF-8", $out_cp, $jmeno);
    		//$prijmeni = iconv("UTF-8", $out_cp, $prijmeni);
    		//$ulice = iconv("UTF-8", $out_cp, $ulice);
    		
    		$radek = "".$jmeno.";".$prijmeni.";".$ulice.";".$data["telefon"]."; \n";
    		fwrite($soubor,  $radek); 
    	    }
        }

        fclose($soubor);
    
        echo "<div style=\"padding-top: 20px; padding-left: 15px;\" >";
        echo "<a href=\"export/export-telefonni-seznam.csv\" >export - telefonní seznam zde</a></div>";
    
    } //konec if item == 2
    else
    {
     echo "<div style=\"padding-top: 20px; padding-left: 5px; padding-bottom: 20px; font-size: 18px; \">
         Prosím vyberte si nějakou možnost ...</div>";    
    }
  
   echo "</div>";
  ?> 
    <!-- konec vlastniho obsahu -->
   </td>
  </tr>
  
 </table>

</body> 
</html> 

