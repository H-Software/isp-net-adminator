<?php

require("include/main.function.shared.php");
require("include/config.php"); 
require("include/check_login.php");
require("include/check_level.php");

if ( !( check_level($level,33) ) )
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

<title>Adminator 2</title> 

</head> 

<body> 

<? include ("head.php"); ?> 

<? include ("category.php"); ?> 


 <tr>
 <td colspan="2" height="50" align="center" ><span style="font-size: 16pt; ">Mazání objektu</span> </td>
  </tr>

  
  <tr>
  <td colspan="2" >
  <?
  
  $erase_id=$_POST["erase_id"];
  $odeslano=$_POST["odeslano"];
  
    $id_check=ereg('^([[:digit:]]+)$',$erase_id);
    
    if ( !($id_check) )
    {
    echo "<br> CHYBA! Vstupni data maji neodpovidajici format. <br><br>";
    exit; 
    }
	
  echo "<span style=\"color: grey; \">debug: update_id: ".$erase_id." </span>"; 
  
  
  //checkem jestli se macklo na tlacitko "OK" :)
  if ( ereg("^OK$",$odeslano) ) 
  {    
  // zjistime puvodni data pro archiv zmen
    $vysl4=pg_exec($db_ok2,"select * from objekty WHERE id_komplu='$erase_id' ");
     if ( ( pg_num_rows($vysl4) <> 1 ) ) {echo "<p>Chyba! Vstupni data neodpovidaji masce. Nelze pokracovat. </p>"; $error="true"; }
   else  { while ($data4=pg_fetch_array($vysl4) ):
   
      $pole2 .=" [dns_jmeno]=> ".$data4["dns_jmeno"].", [ip]=> ".$data4["ip"].", [dov_net]=> ".$data4["dov_net"]; //.", [mac]=> ".$data4["mac"];
    // $pole2 .=", [rra]=> ".$data4["rra"].", [vezeni]=> ".$data4["vezeni"];
    $pole2 .= ", [sc]=> ".$data4["sc"].", [typ]=> ".$data4["typ"].", [id_tarifu] => ".$data4["id_tarifu"];
   $pole2 .=", [poznamka]=> ".$data4["poznamka"].", [verejna]=> ".$data4["verejna"];
  $pole2 .=", [rb_mac] => ".$data4["rb_mac"].",[sikana-status]=> ".$data4["sikana_status"].",[sikana-cas]=> ".$data4["sikana_cas"];
  $pole2 .=", [sikana_text]=> ".$data4["sikana_text"].", [upravil]=> ".$data4["upravil"]." ";
						     
  endwhile;   
}
												       
    $obj_erase_eq = array( "id_komplu" => $erase_id );
    			   
    if( !( $true) ) { $res=pg_delete($db_ok2,'objekty', $obj_erase_eq); }
    
    if ($res) { echo "<br><H3><div style=\"color: green; \" >Data z databáze smazana. </div></H3>\n"; }
    else { echo "<div style=\"color: red; \">Chyba! Data v databázi nelze změnit. </div><br>\n".pg_last_error($db_ok2); }
  
     // pridame to do archivu zmen
    $pole = "<b>akce: smazani objektu;</b><br>";
    foreach ($obj_erase_eq as $key => $val) { $pole .= " [".$key."] => ".$val."\n"; }
    $pole .= $pole2;
    
    //.", akci provedl: ".\Cartalyst\Sentinel\Native\Facades\Sentinel::getUser()->email.", vysledek akce dle postgre: ".$res.", datum akce: ".$datum;
		   
    if( $res == 1 ){ $vysledek_write=1; }
    
    $add=mysql_query("INSERT INTO archiv_zmen (akce,provedeno_kym,vysledek) VALUES ('$pole','" . \Cartalyst\Sentinel\Native\Facades\Sentinel::getUser()->email . "','$vysledek_write') "); 
  
  }
  else 
  { 
  // optame se jestli opravdu smazat
  // $fail="true"; $error.="<div class=\"objekty-add-no-click-ok\"><h4>Data neuloženy, nebylo použito tlačítko \"OK\", 
  // pro uložení klepněte na tlačítko \"OK\" v dolní části obrazovky!!!</h4></div>"; 
  echo "<form action=\"".$_SERVER["PHP_SELF"]."\" method=\"POST\" >";
  
  echo "<table border=\"0\" width=\"50%\">";
  
  echo "<tr><td colspan=\"4\"> Opravdu smazat následující záznam ? <br><br></td></tr>";
  
  // echo "<tr>";
  
  include("include/config.pg.php");
  
  $dotaz = pg_exec($db_ok2, "SELECT * FROM objekty WHERE id_komplu='$erase_id' ");

  $radku=pg_num_rows($dotaz);

    if ($radku ==0 ) { echo "CHYBA! Neodpovídající pocet zaznamu. "; }
    else
    {
    
     while (  $data=pg_fetch_array($dotaz) ) 
     {
     
     echo "<tr> <td> id_komplu: </td> <td>".$data["id_komplu"]." </td> </tr>";
     echo "<tr> <td> dns : </td> <td>".$data["dns_jmeno"]." </td> </tr>";
     
     echo "<tr> <td> ip adresa: </td> <td> ".$data["ip"]." </td> </tr>";
     
     echo "<tr> <td> mac adresa: </td> <td> ".$data["mac"]." </td> </tr>";
     // $data[""];
     
     }
    
    echo "<tr><td colspan=\"2\"><br></td></tr>";
    
    echo "<tr> <td colspan=\"2\" align=\"center\">";
    
    echo "<input type=\"hidden\" name=\"erase_id\" value=\"".$erase_id."\" >";
    echo " <input name=\"odeslano\" type=\"submit\" value=\"OK\" >
    </td></tr>";
    
    }
    					      
  echo "</tr>";
  
  echo "</table>"; 
  
  echo "</form>";
  
  }
  
   
  
  ?>
  
  </td>
  </tr>
  
 </table>

</body> 
</html> 

