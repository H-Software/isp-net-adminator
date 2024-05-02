<?php

require ("include/main.function.shared.php");
require ("include/config.php"); 
require ("include/check_login.php");

require ("include/check_level.php");

if ( !( check_level($level,45) ) )
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

<title>Adminator 2 - vlastníci </title> 

</head> 

<body> 

<? include ("head.php"); ?> 

<? include ("category.php"); ?> 


 <tr>
 <td colspan="2" height="50" align="center" ><span style="font-size: 16pt; ">Mazání vlastníků</span> </td>
  </tr>

  
  <tr>
  <td colspan="2" >
  <?
  
  $erase_id=$_POST["erase_id"];
  $odeslano=$_POST["odeslano"];
  
    $id_check=preg_match('/^([[:digit:]]+)$/',$erase_id);
    
    if ( !($id_check) )
    {
      echo "<br> CHYBA! Vstupni data maji neodpovidajici format. debug: erase_id: ".$erase_id." <br><br>";
      exit; 
    }
	
  echo "<span style=\"color: grey; \">debug: update_id: ".$erase_id." </span>"; 
  
  $res_objekty=pg_query("SELECT * FROM objekty WHERE id_cloveka='$erase_id' ");
  $res_objekty_radku=pg_num_rows($res_objekty);
  
  $res_fakturacni=pg_query("SELECT * FROM vlastnici WHERE id_cloveka='$erase_id' ");
  
  while($data_fakturacni=pg_fetch_array($res_fakturacni) )
  { $res_fakturacni_id = $data_fakturacni["fakturacni"]; }
  
  if ( $res_objekty_radku > 0 ) // jestli jsou u vlastnika objekty
  {
  
  echo "<br><br><span style=\"color: red; font-size: 18px; \"> Vlastníka nelze smazat, jsou k němu přiřazeny objekty. </span><br><br>";
  
  }
  elseif ( $res_fakturacni_id > 0 ) // jestli je u vlastnika fakturacni adresa
  {
  
  echo "<br><br><span style=\"color: red; font-size: 18px; \"> Vlastníka nelze smazat, je k němu přiřazena fakturační adresa. </span><br><br>";
  
  }
  else
  {
  
  //checkem jestli se macklo na tlacitko "OK" :)
  if ( preg_match("/^OK$/",$odeslano) ) 
  { 
  
  // budeme mazat
  // echo "smazano! ";
  
  // muster:  $res = pg_delete($db, 'post_log', $_POST);
   
  // zjistime puvodni data pro archiv zmen
    $vysl4=pg_exec($db_ok2,"select * from vlastnici WHERE id_cloveka='$erase_id' ");
     if ( ( pg_num_rows($vysl4) <> 1 ) ) {echo "<p>Chyba! Vstupni data neodpovidaji masce. Nelze pokracovat. </p>"; $error="true"; }
   else  { 
   
	while ($data4=pg_fetch_array($vysl4) ):
   
      $pole2 .=" [id_cloveka]=> ".$data4["id_cloveka"].", [jmeno]=> ".$data4["jmeno"].", [prijmeni]=> ".$data4["prijmeni"];
     $pole2 .=", [ulice]=> ".$data4["ulice"].", [mesto]=> ".$data4["mesto"].", [psc]=> ".$data4["psc"];
    $pole2 .=", [telefon]=> ".$data4["telefon"].", [icq]=> ".$data4["icq"].", [mail]=> ".$data4["mail"];
   $pole2 .=", [poznamka]=> ".$data4["poznamka"].", [fakturacni]=> ".$data4["fakturacni"].", [vs]=> ".$data4["vs"];
  $pole2 .=", [k_platbe] => ".$data4["k_platbe"].",[firma]=> ".$data4["firma"];
  
  // .",[sikana-cas]=> ".$data4["sikana_cas"];
      
 // $pole2 .=", [sikana_text]=> ".$data4["sikana_text"].", [upravil]=> ".$data4["upravil"]." ";
						     
	endwhile;   
	}
								
    $datum = strftime("%d/%m/%Y %H:%M:%S", time());
    																       
    $obj_erase_eq = array( "id_cloveka" => $erase_id );
    			   
    if ( !( $true) ) { $res=pg_delete($db_ok2,'vlastnici', $obj_erase_eq); }
    
    if ($res) { echo "<br><H3><div style=\"color: green; \" >Data z databáze smazána. </div></H3>\n"; }
    else { echo "<div style=\"color: red; \">Chyba! Data z databáze nelze smazat. </div><br>\n".pg_last_error($db_ok2); }
  
     // pridame to do archivu zmen
    $pole = "<b>akce: smazani vlastnika ;</b><br>";
    foreach ($obj_erase_eq as $key => $val) { $pole=$pole." [".$key."] => ".$val."\n"; }
    // $pole .= $pole2.", akci provedl: ".\Cartalyst\Sentinel\Native\Facades\Sentinel::getUser()->email.", vysledek akce dle postgre: ".$res.", datum akce: ".$datum;

    if ( $res == 1){ $vysledek_write="1"; }
       		   
    $add=$conn_mysql->query("INSERT INTO archiv_zmen (akce,provedeno_kym,vysledek) VALUES ('$pole','" . \Cartalyst\Sentinel\Native\Facades\Sentinel::getUser()->email . "','$vysledek_write' )");
		       
  
  }
  else 
  { 
  // optame se jestli opravdu smazat
  
  echo "<form action=\"".$_SERVER["PHP_SELF"]."\" method=\"POST\" >";
    
  echo "<table border=\"0\" width=\"50%\">";
  
  echo "<tr><td colspan=\"4\"> Opravdu smazat následující záznam ? <br><br></td></tr>";
  
  // echo "<tr>";
  
  include("include/config.pg.php");
  
  $dotaz = pg_exec($db_ok2, "SELECT * FROM vlastnici WHERE id_cloveka='$erase_id' ");

  $radku=pg_num_rows($dotaz);

    if ($radku <> 1 ) { echo "CHYBA! Neodpovídající počet záznamů. "; }
    else
    {
    
     while (  $data=pg_fetch_array($dotaz) ) 
     {
     
     echo "<tr> <td> id_cloveka: </td> <td>".$data["id_cloveka"]." </td> </tr>";
     echo "<tr> <td> jméno: </td> <td>".$data["jmeno"]." </td> </tr>";
     
     echo "<tr> <td> příjmení: </td> <td> ".$data["prijmeni"]." </td> </tr>";
     
     echo "<tr> <td> adresa: </td> <td> ".$data["ulice"]." , ".$data["mesto"]." </td> </tr>";
     // $data[""];
     
     } // konec while
    
    echo "<tr><td colspan=\"2\"><br></td></tr>";
    
    echo "<tr> <td colspan=\"2\" align=\"center\">";
    
    echo "<input type=\"hidden\" name=\"erase_id\" value=\"".$erase_id."\" >";
    echo " <input name=\"odeslano\" type=\"submit\" value=\"OK\" >
    </td></tr>";
    
    } // konec else $radku <> 1
    					      
  echo "</tr>";
  
  echo "</table>"; 
  
  echo "</form>";
  
  } // konec else jestli opravdu smazat
  
  } // konec else res_objekty_radku > 0
   
  
  ?>
  
  </td>
  </tr>
  
 </table>

</body> 
</html> 

