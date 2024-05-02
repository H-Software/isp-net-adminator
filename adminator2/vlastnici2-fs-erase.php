<?php

require("include/main.function.shared.php");
require("include/config.php"); 
// 
require("include/check_login.php");
require("include/check_level.php");

if ( !( check_level($level,141) ) )
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

<title>Adminator 2 :: FS :: smazání</title> 

</head> 

<body> 

<? include ("head.php"); ?> 

<? include ("category.php"); ?> 

 <tr>
   <td colspan="2" height="20" bgcolor="silver" >
    <? include("vlastnici-cat-inc.php"); ?>
   </td>
 </tr>

 <tr>
  <td colspan="2" >
  <?
  
  $erase_id=$_GET["erase_id"];
  if( (strlen($erase_id) < 1 ) )
  { $erase_id = $_POST["erase_id"]; }
  
    
  $odeslano=$_POST["odeslano"];
  
  if( !(preg_match('/^([[:digit:]]+)$/',$erase_id)) )
  {
    echo "<div style=\"color: red; font-size: 18px; \"> 
	    CHYBA! Vstupni data maji neodpovidajici format. debug: erase_id: ".$erase_id." </div><br>";
    exit; 
  }
	
  //echo "<span style=\"color: grey; \">debug: erase_id: ".$erase_id." </span>"; 
  
  $res_vl = pg_query("SELECT * FROM vlastnici WHERE fakturacni_skupina_id = '$erase_id' ");
  $res_vs_radku = pg_num_rows($res_vl);
  
  
  if ( $res_vs_radku > 0 ) // jestli jsou u FS vlastnici
  {
    echo "<br><br><span style=\"color: red; font-size: 18px; \">
	    Fakturační skupinu nelze smazat, jsou k ní přiřazeny klienti.</span><br><br>";
    exit;
  }
  
  //checkem jestli se macklo na tlacitko "OK" :)
  if ( preg_match("/^OK$/",$odeslano) ) 
  { 
  
    // zjistime puvodni data pro archiv zmen
    $vysl_az  = $conn_mysql->query("SELECT * FROM fakturacni_skupiny WHERE id = '$erase_id'");
    
    if( ( mysql_num_rows($vysl_az) <> 1 ) ) 
    { $pole2 .= "<p>Chyba! puvodni data nelze nacist. </p>"; $error="true"; }
    else
    { 
	while ($data_az = mysql_fetch_array($vysl_az) ):
    	  $pole2 .=" [id_fs]=> ".$data_az["id"].", [nazev]=> ".$data_az["nazev"].", [typ]=> ".$data_az["typ"];
    	  $pole2 .=", [fakturacni_text]=> ".$data_az["fakturacni_text"].", [typ_sluzby]=> ".$data_az["typ_sluzby"];					     
	endwhile;   
    }
    	   
    $res = $conn_mysql->query("DELETE FROM fakturacni_skupiny WHERE id = '$erase_id' LIMIT 1 ");
    
    if ($res) { echo "<br><H3><div style=\"color: green; \" >Fakturační skupina úspěšně smazána. </div></H3>\n"; }
    else { echo "<div style=\"color: red; \">Chyba! Fakturační skupinu z databáze nelze smazat. </div><br>\n"; }
  
    // pridame to do archivu zmen
    $pole = "<b>akce: smazani fakt. skupiny; </b><br>".$pole2;
    // $pole .= $pole2.", akci provedl: ".\Cartalyst\Sentinel\Native\Facades\Sentinel::getUser()->email.", vysledek akce dle postgre: ".$res.", datum akce: ".$datum;

    if ( $res == 1){ $vysledek_write=1; }   		   
    $add=$conn_mysql->query("INSERT INTO archiv_zmen (akce,provedeno_kym,vysledek) VALUES ('$pole','" . \Cartalyst\Sentinel\Native\Facades\Sentinel::getUser()->email . "','$vysledek_write' )");

  }
  else 
  { 
    $dotaz = $conn_mysql->query("SELECT * FROM fakturacni_skupiny WHERE id = '$erase_id' ");
    $radku = $dotaz->num_rows;
    
    if ($radku <> 1 ) 
    { 
       echo "CHYBA! Neodpovídající počet záznamů. "; 
       exit;
    }
   
    //optame se jestli opravdu smazat
  
   echo "<form action=\"".$_SERVER["PHP_SELF"]."\" method=\"POST\" >";
    
   echo "<table border=\"0\" width=\"50%\">";
  
   echo "<tr><td colspan=\"4\"> Opravdu smazat následující Fakturační skupinu? <br><br></td></tr>";
  
    while ($data= $dotaz->fetch_array() ) 
    {
     
     echo "<tr> <td><b>id fakt. skupiny: </b></td> <td>".$data["id"]." </td> </tr>";
     echo "<tr> <td><b>Název: </b></td><td>".$data["nazev"]." </td> </tr>";     
     echo "<tr> <td><b>Fakturační text: </b></td> <td> ".$data["fakturacni_text"]." </td> </tr>";
     //echo "<tr> <td> adresa: </td> <td> ".$data["ulice"]." , ".$data["mesto"]." </td> </tr>";
     
    } // konec while
    
    echo "<tr><td colspan=\"2\"><br></td></tr>";
    echo "<tr> <td colspan=\"2\" align=\"center\">";
    
    echo "<input type=\"hidden\" name=\"erase_id\" value=\"".$erase_id."\" >";
    echo " <input name=\"odeslano\" type=\"submit\" value=\"OK\" >
    </td></tr>";
    			      
  echo "</tr>";
  echo "</table>";
  echo "</form>";
  
  } // konec else jestli opravdu smazat

?>
  
  </td>
  </tr>
  
 </table>

</body> 
</html> 

