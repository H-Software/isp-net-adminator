<?php

include ("include/config.php"); 
include ("include/check_login.php");

include ("include/check_level.php");

if ( !( check_level($level,43) ) )
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

<title>Adminator 2</title> 

</head> 

<body> 

<? include ("head.php"); ?> 

<? include ("category.php"); ?> 

 
 <tr>
  <td colspan="2" height=""><span style="background-color: #DDDDDD; font-size: 18px;">  Administrace  - statistika </span></td>
 </tr>
 
 <tr>
 <td colspan="2"></td>
  </tr>
  
  <tr>
  <td colspan="2">
  
  
  <table width=100%>
  <tr>
  <td width="20%" valign="top" ><? include ("admin-cat.php"); ?></td>
  
  <td valign="top" > 
  
  <!-- sem zbytek -->
  
    <?
    $rok=$_POST["rok"];
    
    if ( (strlen($rok) < 1) ){ $rok="2008"; }

    echo "<br><div style=\"font-size: 18px; font-weight: bold; \" >Statistika přidaných objektů: </div><br>";

         echo "<table border=\"0\" width=\"50%\" >";
         echo "<tr>";

                 echo "<td valign=\"center\"><div style=\"font-size: 16px;  \">Výpis objektů za rok: <b>$rok</b> </div></td>";

                 echo "<td>Zvolte rok: </td>";

                 echo "<td>

                 <form action=\"".$_SERVER["PHP_SELF"]."\" method=\"POST\" >
                    <select name=\"rok\" size=\"1\" >

                        <option value=\"2005\" "; if ($rok == "2005")echo "selected"; echo " >2005</option>
                        <option value=\"2006\" "; if ($rok == "2006")echo "selected"; echo " >2006</option>
                        <option value=\"2007\" "; if ($rok == "2007")echo "selected"; echo " >2007</option>
                        <option value=\"2008\" "; if ($rok == "2008")echo "selected"; echo " >2008</option>
                        <option value=\"2009\" "; if ($rok == "2009")echo "selected"; echo " >2009</option>

                     </select>
		     
                     </td>

                     <td>
                         <input type=\"submit\" value=\"OK\" name=\"OK\" >
                 </form>

                </td>";
        echo "</tr>";

        echo "<tr><td colspan=\"4\" ><hr width=\"100%\" align=\"left\" ></td></tr>";

        echo "</table>";

    echo "<br>";
    
    echo "<table border=\"1\" width=\"50%\" > ";

    echo "<tr>
	<td> Období: </td> 
	<td> Počet objektů: </td>
	<td> platby: </td>
	<tr>";
	
    echo "<tr><td colspan=\"3\" > <br></td></tr>";
    
    include("include/config.pg.php");
    
    // $mesic=0;
    
    for ($mesic=1; $mesic<=12; $mesic++)
    {
    
     $dotaz="SELECT * FROM objekty WHERE ( ( typ = 1 ) AND ( extract(year from pridano) = ".$rok." ) AND ( extract(month from pridano) = ".$mesic." ) ) ";
    
     $prvni=pg_exec($db_ok2,$dotaz);
     $prvni_radku=pg_num_rows($prvni);
		 
    if ( $prvni_radku == 0){ }
    else
    {
    
    while( $data1=pg_fetch_array($prvni) ):

    $pocet1 = $pocet1 + 1;
    
    if ( $data["id_tridy"] > 0 ) { }
    elseif ( ( $data1["sc"] == 1 ) ) { $platba1 = $platba1 + 250; }
    else{ $platba1 = $platba1 + 420; }
    
    endwhile;
    
    } // konec else
    
    echo "<tr> <td>";
    
    // if ( $i == 1)
    // {	echo "do období 4 /2005"; }
    // else
    {  echo $mesic." / ".$rok;  }
    
    if ( $prvni_radku == 0 )
    { echo "</td><td colspan=\"2\">Žádné objekty v tomto období </td></tr>"; }
    else
    {
	echo "</td>"; 
	echo "<td> ".$pocet1." </td>";
	echo "<td> ".$platba1." </td>";
	echo "</tr>";
    }
    
    // echo "</table>";
    
    $pocet1=0;
    $platba1=0;
    
    }
    
    echo "</table>";
    
    ?>
   
  
  <!-- konec interni tabulky -->
  </tr>
  </table>
  
  
  </td>
  </tr>
  
 </table>

</body> 
</html> 

