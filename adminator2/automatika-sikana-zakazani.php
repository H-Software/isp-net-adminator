<?php

require("include/main.function.shared.php");
require("include/config.php"); 
require("include/check_login.php");
require("include/check_level.php");

if ( !( check_level($level,36) ) )
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
  <td colspan="2">
  <!-- zde zacina vnitrni tabulka -->
  
  <table border="1" width="100%">
  
  <tr>
    <td colspan="2">  <div style="color: red; font-size: 20px; ">Automatika</div> </td>
    <td></td>
  </tr>
  
  <tr><td colspan="2" ><br></td></tr>
  
  <tr><td width="20%" valign="top" ><?include ("automatika-cat.php"); ?></td>
  
  <td>
  <?
  echo "<span style=\"background-color: silver; \">Automatika - šikana - zakázaní</span>";
  echo "<br><br>";
  
  echo "<span style=\"color: grey; \">stav: </span><br> \n";
  
  $send=$_POST["send"];
  
  if ( !($send) )
  {
  $vysledek=$conn_mysql->query("select * from automatika where vec LIKE 'sikana_net_n' " );
  $radku = $vysledek->num_rows();
	    
    if ($radku==0) { echo "Chyba! Stav se nepodarilo zjistit "; }
    else
	{
    
     while ($data = $vysledek->fetch_array()):
     
             $vec=$data["vec"];
	     $zapnuto=$data["zapnuto"];
	     $cas_hodina=$data["cas_hodina"];
	    	     
     endwhile;
    
    // sem vypis statusu
    
    echo "<br>";
    echo "<table border=\"0\" width=\"80%\"> <tr>";
    
    echo "<td><b>Funkce:</b> :".$vec."</td>";
    
    echo "<td> <b>stav: </b>"; 
    
    if ($zapnuto == 2) { echo "zapnuto"; } else { echo "vypnuto"; }
    
    echo "</td>";
    
    echo "<td><b> čas, kdy se má zakazovat (hodina)</b> : ".$cas_hodina."</td>";
    
    
    echo "</tr></table> ";
    
	}
    
    }
    else
    {
    echo " pro zobrazeni aktuálních údajů znovu vyberte sekci ";
    
    
    }
    				    
    // sem upravu
    
    // $send=$_POST["send"];
    $vec_new=$_POST["vec"];
    $zapnuto_new=$_POST["zapnuto_new"];
    $cas=$_POST["cas_new"];
    
    
    if ($send)
    {
    //budeme ukladat
    
    $uprava=$conn_mysql->query("UPDATE automatika SET zapnuto='$zapnuto_new', cas_hodina='$cas' where vec like '$vec_new' Limit 1 ");
    
    
    if ($uprava) { echo "<br><H3><div style=\"color: green; \" >Data úspěšně uloženy do databáze.</div></H3>\n"; }
    else { echo "<div style=\"color: red; \">Chyba! Data do databáze nelze uložit. </div><br>\n".pg_last_error($db_ok2); }
    
    
    }
    else
    {
    
    echo "<br> <span style=\"color: grey; \">uprava: </span> <br> \n";
    
    echo "<form name=\"form2\" method=\"POST\" action=\"".$_SERVER["PHP_SELF"]."\" > \n";
    
    echo "<table border=\"0\" width=\"75%\"> <tr>";
    
    
    echo "<td>vec: sikana_net_n </td> \n";
    
    echo "<td>";
    
    echo "<input type=\"hidden\" name=\"vec\" value=\"sikana_net_n\" >";
    
    echo "<label>zapnuto: </label>";
    
    echo "</td> \n";
    
    echo "<td>";
    
    echo "<label> | Ano </label>";
    echo "<input type=\"radio\" name=\"zapnuto_new\" value=\"2\" ";
    
    if ($zapnuto == 2 ){ echo " checked "; }
    
    echo " >";
    
    echo "<label> | Ne </label>";
    echo "<input type=\"radio\" name=\"zapnuto_new\" value=\"1\" ";
    
    if ($zapnuto==1){ echo " checked "; }
    
    echo " >";
    
    echo "</td> \n";
    
    echo "<td>hodina odpočtu: </td>";
    // sem hodiny
    echo "<td>";
    
    echo "<select size=\"1\" name=\"cas_new\" >";
    
    for($i=1; $i <= 24; $i++ )
    {
    echo "<option value=\"".$i."\" ";
    
    if ($cas_hodina == $i ){ echo " selected "; }
    
    echo " > ".$i." </option> \n";
    
    }
    
    echo "</select>";
    echo "</td> \n";
    
    echo "<td> 
    <input type=\"hidden\" name=\"send\" value=\"true\" >
    <input type=\"submit\" value=\"UPDATE\" name=\"B1\" > </td>";
    
    echo "</tr></table> \n";
    
    echo "</form>";
  
    } 
  ?>
  
  </td>
  
  </tr>
  
  </table>


    
  </td>
  </tr>
  
 </table>

</body> 
</html> 

