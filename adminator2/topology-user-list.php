<?php

require("include/main.function.shared.php");
require_once("include/config.php"); 
require_once("include/check_login.php");
require_once("include/check_level.php");

if( !( check_level($level,6) ) )
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

<title>Adminator2 - Topology - user list</title> 

</head>

<body>

<?php require ("head.php"); ?> 

<?php require ("category.php"); ?> 

 <tr>
  <td colspan="2" bgcolor="silver" >
   <?php require("topology-cat2.php"); ?>
  </td>
 </tr>
	     
  <tr>
  <td colspan="2">
  
  <?php
    
    function show_end_tags(){
    
            echo '
             
             </td>
	    </tr>
	    
	    </table>
	  </body>
	</html>';

    }     
    
    $vyber=intval($_GET["vyber"]);
    $vysilac=intval($_GET["vysilac"]);

    //uvodni panel s vyberem		   
    echo "
		  
	  <div style=\"padding-top: 10px; padding-left: 5px; height: 30px; border-bottom: 1px solid black;\" >
	
	  <form action=\"\" method=\"get\" name=\"form1\">
		  
	    <div style=\"float: left; width: 200px; font-weight: bold;\">zvolte režim výpisu: </div>
		    
	    <div style=\"float: left; padding-right: 20px;\">vysílače: </div>
		    
	    <div style=\"float: left; padding-right: 20px; width: 300px; \">\n";
	    
	    if( $vyber > 0){
		echo "<span style=\"color: gray;\">není dostupné</span>\n";
		echo "<input type=\"hidden\" name=\"vysilac\" value=\"0\">";
	    }
	    else
	    {
		echo "<select name=\"vysilac\" size=\"1\" style=\"width: 280px; \">
			    
		    <option value=\"0\" class=\"top-neni-vybr\" > není vybráno </option>\n";
			    
	    	    $vysledek=$conn_mysql->query("select id, jmeno from nod_list ORDER BY jmeno ASC" );
		
		    while($zaznam2 = $vysledek->fetch_array() )
		    {
			$selected = ( $vysilac == $zaznam2["id"] ? " selected " : "");
			
			echo "\t\t\t<option value=\"".$zaznam2["id"]."\" ".$selected.">".
			    $zaznam2["jmeno"]." (".$zaznam2["id"].")</option>\n";
		    }
																    
		echo "\t\t</select>\n";
		
	      }	
	          
	      echo "</div>\n";
		    
	    echo "<div style=\"float: left;\"><input type=\"submit\" name=\"odeslat\" value=\"OK\" ></div>\n";
	
	    echo "<div slyle=\"clear: both;\"></div>\n";

	echo "</form>\n";
	    
	echo "</div>\n\n\n";
	    	
		
    //zde vlastni vypis objektu

    if( ($vyber > 0) and ( $vysilac > 0) ){
    
	echo "<div style=\"padding-top: 20px; padding-left: 5px; padding-bottom: 20px; font-weight: bold; color: red; \">".
	    "Chyba! nelze vybrat oba prvky!</div>\n";
    
	show_end_tags();
	exit;
    }
    
    
    if($vyber == 2){
	$sql_where = " AND (id_nodu = '0') ";
    }
    elseif($vyber == 3){
	$sql_where = "";
    }	
    elseif( $vysilac > 0){
	$sql_where = " AND (id_nodu = '".intval($vysilac)."') ";
    }
    else{
	//neco nesmyslnyho aby se nevypsalo nic
	$sql_where = " AND (id_komplu = '0') ";
    }
    $sql_zaklad = "SELECT * FROM objekty WHERE ( (id_komplu > 0) ".$sql_where." ) ORDER BY ip ASC";
    	    
    $rs = pg_query($db_ok2, $sql_zaklad);
    $rs_rows = pg_num_rows($rs);
    
    if(!$rs){

	echo "<div style=\"padding-top: 20px; padding-left: 5px; padding-bottom: 10px; font-weight: bold; color: red; \">".
	    "Chyba! PG SQL! ".pg_last_error($db_ok2)."</div>";

	show_end_tags();
	exit;	
    
    }
    
    echo "<div style=\"padding-top: 20px; padding-left: 5px; padding-bottom: 10px; \">".
	    "počet objektů: ".$rs_rows."</div>";
    
    echo '<table border="1" width="100%" >';
					      
    echo "\n<tr>
		<td><b>dns jméno:</b></td>
	        <td><b>IP adresa: </b></td>
		<td><b>přípojný bod</b></td>			      
		<td><b>odkaz</b></td>			      
	  </tr>\n";
		
    echo "\n";
							      
    while($zaznam = pg_fetch_array($rs))
    {
	$id=$zaznam["id_nodu"];
		
	echo "<tr>\n";
	
	echo "\t<td>".$zaznam["dns_jmeno"]."</td>\n";
	echo "\t<td>".$zaznam["ip"]."</td>\n";
		 
	$vysledek_ms=$conn_mysql->query("SELECT jmeno FROM nod_list WHERE id = '".intval($id)."' ");
	$radku=$vysledek_ms->num_rows;
											
	if($radku==0){
	    echo "\t<td>nepřiřazeno/jméno nodu nenalezeno) </td>";
	}
	else{
	
	    while ($zaznam_ms=$vysledek_ms->fetch_array()):
			$nazev_nodu_new=$zaznam_ms["jmeno"];
	    endwhile;
	
	    echo "\t<td>".htmlspecialchars($nazev_nodu_new)."</td>\n"; 
	}
	
	echo "\t<td>detail: <a href=\"objekty.php?ip_find=".$zaznam["ip"]."\" >zde</a>, ";
	echo "v novém okně <a href=\"objekty.php?ip_find=".$zaznam["ip"]."\" target=\"_blank\" >zde</a></td>\n";
	             
	             
	echo "</tr>\n\n";
    }

    echo "</table>\n";

?>
		
  </td>
  </tr>
  
 </table>

</body>
</html>
