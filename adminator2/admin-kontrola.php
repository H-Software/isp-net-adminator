<?php

include ("include/config.php"); 
include ("include/check_login.php");

include ("include/check_level.php");

if ( !( check_level($level,39) ) )
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
  <td colspan="2" height="50" bgcolor="silver">
   <? include("admin-subcat2-inc.php"); ?>      
  </td>
 </tr>
 
  <tr>
  <td colspan="2">
  
  <?
  
  echo "<table width=\"100%\"> ";
  
   $dotaz=pg_exec($db_ok2,"SELECT * FROM fakturacni ");
   $dotaz_radku=pg_num_rows($dotaz);
       
   if ( $dotaz_radku==0 )
   {
   echo "<tr><td> Fakturacni udaje nenalezeny. </td></tr>";
   }
   else
   {
    
   echo "<tr> <td >id: </td> <td>jméno: </td> <td>adresa: </td> ";
   echo "<td>ico: </td> <td>záznam v objektech: </td> </tr>";
	       
   echo "<tr><td colspan=\"5\"><br></td></tr>";
   
  while( $data=pg_fetch_array($dotaz) ):
   // echo "<tr><td colspan=\"14\"> <br> </td> </tr>";
  
   echo "<tr> <td colspan=\"1\"> ".$data["id"]."</td> ";
   
   echo "<td > ".$data["ftitle"]."</td> ";
   echo "<td > ".$data["fulice"]." </td> ";

   echo "<td> ".$data["ico"]." </td>";
   
   echo "<td> ";

    $id=$data["id"];
    // sem zjistovani objektu
    $dotaz2=pg_exec($db_ok2,"SELECT * FROM vlastnici where fakturacni='$id' ");
    $dotaz_radku2=pg_num_rows($dotaz2);
	 
   if ( $dotaz_radku2 > 1){ $objektu="<span style=\"color: orange; \">více  ( ".$dotaz_radku2." )</span>"; }
   elseif ( $dotaz_radku2 == 1) { $objektu="<span style=\"color: green; \">jeden </span>"; }
   else { $objektu="<span style=\"color: red; \">žádný </span>"; }
   
   echo $objektu." </td>";
   
    echo "</tr>";    
   
   endwhile;



  } // konec else
		
  
  ?>
 
 </td>
  </tr>
  
 </table>

</body> 
</html> 

