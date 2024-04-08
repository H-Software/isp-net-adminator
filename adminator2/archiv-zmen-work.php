<?php
require("include/main.function.shared.php");
require_once ("include/config.php"); 
require_once ("include/check_login.php");
require_once ("include/check_level.php");

if ( !( check_level($level,30) ) )
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

<title>Adminator 2 :: Změny :: Archiv změn Work</title> 

</head> 

<body> 

<?php require("head.php"); ?> 

<?php require("category.php"); ?> 

<tr>
  <td colspan="2"><?php require("archiv-zmen-subcat.php"); ?></td>
</tr>
  
<tr>
  <td colspan="2">
  
 <?php
 
    $pocet=$_GET["pocet"];
 
    echo "<div style=\"padding-left: 5px; padding-top: 10px; \">";
    
    echo "<div style=\" padding-bottom: 10px; padding-right: 40px; font-size: 18px; font-weight: bold; float: left; \" >";
    echo " Archiv změn Work (restartování)</div>";
 
    echo "<div style=\" \" ><form method=\"GET\" action=\"\" >";
    
    echo "<span style=\"margin-right: 20px; \" ><label>Vyberte počet záznamů: </label></span>
	
	  <select name=\"pocet\" size=\"1\" >
	    <option value=\"1\" "; if ($pocet == "1" or !isset($pocet) ){ echo " selected "; } echo " >1</option>
	    <option value=\"3\" "; if( $pocet == "3" ){ echo " selected "; } echo " >3</option>
	    <option value=\"5\""; if( $pocet == "5" ){ echo " selected "; } echo " >5</option>
	  </select>";

    echo "<span style=\"margin-left: 10px; \"><input type=\"submit\" name=\"odeslano\" value=\"OK\" ></span>
    
    <!-- <span style=\"margin-left: 40px; \"><a href=\"include\export-archiv-zmen.php\">export dat zde</a></span> -->
    
    </form> ";
 
    echo "</div>"; //konec hlavni divu
    
    $pocet_check=ereg('^([[:digit:]]+)$',$pocet);
    
    $zaklad_sql = "select *,DATE_FORMAT(provedeno_kdy, '%d.%m.%Y %H:%i:%s') as provedeno_kdy2 from archiv_zmen_work ";
    
    if( ($pocet_check) ){   
      if ( ( strlen($pocet) > 0 ) )
      { $sql=$zaklad_sql." order by id DESC LIMIT $pocet "; }
      else
	    { $sql=$zaklad_sql." order by id DESC "; }
    }
    else
    { $sql=$zaklad_sql." order by id DESC LIMIT 1 "; }

    try {
      $vysl = $conn_mysql->query($sql);
    } catch (Exception $e) {
      die ("<h2 style=\"color: red; \">Error: Database query failed! Caught exception: " . $e->getMessage() . "\n" . "</h2></body></html>\n");
    }

    $radku = $vysl->num_rows;
    
    // echo '<br><a href="include\export-archiv-zmen.php">export dat zde</a><br><br>';     
    
    if ( $radku==0 ){ echo "Žádné změny v archivu "; }
    else  
    {
      echo "<table width=\"100%\" border=\"0\" cellpadding=\"5\" class=\"az-main-table\" >";
	    
      echo "<tr >";    
          echo "<td class=\"az-border2\" ><b>id:</b></td>";
          echo "<td class=\"az-border2\" ><b>akce:</b></td>";
      // echo "<td><b>pozn:</b></td>";
          echo "<td class=\"az-border2\" ><b>Provedeno kdy:</b></td>";
          echo "<td class=\"az-border2\" ><b>Provedeno kým:</b></td>";
          echo "<td class=\"az-border2\" ><b>Provedeno úspěšně:</b></td>";
      echo "</tr>";
	    
      while ($data=$vysl->fetch_array()):
	    
        echo "<tr>";    
              echo "<td class=\"az-border1\" style=\"vertical-align: top;\" >".$data["id"]."</td>";
        echo "<td class=\"az-border1\" ><span class=\"az-text\" >";
	   
        $id_cloveka_res = "";  
        $akce = $data["akce"];
	     
	      echo "<pre>".$akce."</pre></span></td>";
	
        echo "<td class=\"az-border1\" style=\"vertical-align: top;\"><span class=\"az-provedeno-kdy\" >";
          if ( ( strlen($data["provedeno_kdy2"]) < 1 ) ){ echo "&nbsp;"; }
          else{ echo $data["provedeno_kdy2"]; }
        echo "</span></td>";
        
        echo "<td class=\"az-border1\" style=\"vertical-align: top;\"><span class=\"az-provedeno-kym\" >";
          if ( ( strlen($data["provedeno_kym"]) < 1 ) ){ echo "&nbsp;"; }
          else{ echo $data["provedeno_kym"]; }
        echo "</span></td>";		   
    
        echo "<td class=\"az-border1\" style=\"vertical-align: top;\">";
          if ( $data["vysledek"] == 1 ){ echo "<span class=\"az-vysl-ano\">Ano</span>"; }
          else{ echo "<span class=\"az-vysl-ne\">&nbsp;</span>"; }
        echo "</td>";
    
        echo "</tr>";
	    
      endwhile;
	    
	    echo "</table>";
    
    } //konec else
    
 ?> 
 
  </td>
  </tr>
  
 </table>

</body> 
</html> 
