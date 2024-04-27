<?php

require("include/main.function.shared.php");

include ("include/config.php"); 
include ("include/check_login.php");

include ("include/check_level.php");

if ( !( check_level($level,104) ) )
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

<title>Adminator 2 - statistika vlastníků </title> 

</head> 

<body> 

<? include ("head.php"); ?> 

<? include ("category.php"); ?> 

  <tr>
   <td colspan="2" bgcolor="silver" height=""><? include("stats-cat-inc.php"); ?></td>
 </tr>
     
  <tr>
  <td colspan="2">
 
  
  <!-- sem zbytek -->
  
    <?
    $rok=$_POST["rok"];
            
    if ( (strlen($rok) < 1) )	    
    { $rok = strftime("%Y", time()); }
  
    echo "<br><div style=\"font-size: 18px; font-weight: bold; \" >Statistika přidaných vlastníků: </div><br>";

         echo "<table border=\"0\" width=\"50%\" >";
         echo "<tr>";

                 echo "<td valign=\"center\"><div style=\"font-size: 16px;  \">Výpis vlastníků za rok: <b>$rok</b> </div></td>";

                 echo "<td>Zvolte rok: </td>";

                 echo "<td>

                 <form method=\"POST\" >
                    <select name=\"rok\" size=\"1\" >

                       <option value=\"2006\" "; if ($rok == "2006")echo "selected"; echo " >2006</option>
                       <option value=\"2007\" "; if ($rok == "2007")echo "selected"; echo " >2007</option>
                       <option value=\"2008\" "; if ($rok == "2008")echo "selected"; echo " >2008</option>
                       <option value=\"2009\" "; if ($rok == "2009")echo "selected"; echo " >2009</option>
                       <option value=\"2010\" "; if ($rok == "2010")echo "selected"; echo " >2010</option>
                       <option value=\"2011\" "; if ($rok == "2011")echo "selected"; echo " >2011</option>
			
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
	<td> Počet vlastníků: </td>
	<td> platby: </td>
	<tr>";
	
    echo "<tr><td colspan=\"3\" > <br></td></tr>";
    
    include("include/config.pg.php");
    
    // $mesic=0;
    
    for ($mesic=1; $mesic<=12; $mesic++)
    {
    
     $dotaz="SELECT * FROM vlastnici WHERE ( ( extract(year from pridano) = ".$rok." ) AND ( extract(month from pridano) = ".$mesic." ) 
    		AND ( ( archiv = 0 or archiv is null ) ) ) ";
    
     $prvni=pg_query($dotaz);

     if($prvni === false){
      echo pg_last_error($db_ok2);
      exit;
     }
		 else{
      $prvni_radku=pg_num_rows($prvni);
     }
		 
    if ( $prvni_radku == 0){ }
    else
    {
     while( $data1=pg_fetch_array($prvni) )
     {
     $pocet1 = $pocet1 + 1;
     $platba1 = $platba1 + $data1["k_platbe"]; 
     }
    } // konec else
    
    echo "<tr> <td>";
    
    echo $mesic." / ".$rok;
    
    if ( $prvni_radku == 0 )
    { echo "</td><td colspan=\"2\">Žádní vlastníci v tomto období </td></tr>"; }
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
    
    echo "<div style=\"padding-top: 20px; padding-bottom: 20px; font-weight: bold; \">
    Poznámka: Vlastníci přidáni v první polovině roku 2008 a dříve, mají datumy přídání orientační (dle datumu přidání objektu, či dle VS ).
    </div>
    ";
    
    
    ?>
    
  <!-- konec interni tabulky -->
    
  </td>
  </tr>
  
 </table>

</body> 
</html> 

