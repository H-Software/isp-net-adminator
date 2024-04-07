<?php

include ("include/config.php");
include ("include/check_login.php");

include ("include/check_level.php");

if ( !( check_level($level,81) ) )
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

<title>Adminator2 - platby</title> 

</head> 

<body> 

<? include ("head.php"); ?> 

<? include ("category.php"); ?> 

 <tr>
   <td colspan="2" ><? include("platby-subcat-inc2.php"); ?></td>
 </tr>
    
 <tr>
  <td colspan="2">
  
  <?
    
	//prvni odkaz - platby soucet
	
	$rok=$_GET["rok"];
	
	if ( (strlen($rok) < 1) )
	{ $rok = strftime("%Y", time()); }
	    		   
	include ("include/config.pg.php");
	
	echo "<br><div style=\"font-size: 18px; font-weight: bold; \" >Statistika hotovostních plateb: </div><br>";
				
	 echo "<table border=\"0\" width=\"50%\" >";
	 echo "<tr>";
		 
	         echo "<td valign=\"center\"><div style=\"font-size: 16px;  \">Výpis plateb za rok: <b>$rok</b> </div></td>";
			     
	         echo "<td>Zvolte rok: </td>";
			 
	         echo "<td>
						     
	         <form action=\"".$_SERVER["PHP_SELF"]."\" method=\"GET\" >
		    <select name=\"rok\" size=\"1\">
								 
		        <option value=\"2006\" "; if ($rok == "2006")echo "selected"; echo " >2006</option>
			<option value=\"2007\" "; if ($rok == "2007")echo "selected"; echo " >2007</option>
			<option value=\"2008\" "; if ($rok == "2008")echo "selected"; echo " >2008</option>
			<option value=\"2009\" "; if ($rok == "2009")echo "selected"; echo " >2009</option>

			<option value=\"2010\" "; if ($rok == "2010")echo "selected"; echo " >2010</option>
			<option value=\"2011\" "; if ($rok == "2011")echo "selected"; echo " >2011</option>
			<option value=\"2012\" "; if ($rok == "2012")echo "selected"; echo " >2012</option>
																	 
		     </select>
		
		    <input type=\"hidden\" name=\"id_vlastnika\" value=\"".$id_vlastnika."\" >
		
		     </td>
		
		     <td>
		         <input type=\"submit\" value=\"OK\" name=\"OK\" >
		 </form>
		
		</td>";
	echo "</tr>";
	
	echo "<tr><td colspan=\"4\" ><hr width=\"100%\" align=\"left\" ></td></tr>";
																																	 
	echo "</table>";
	
	echo "<br>";
	
	echo "<table border=\"1\" width=\"100%\" >";
	
	echo "<tr>";
	echo "<td><b> období </b></td>";
	echo "<td><b>celkově</b></td>";
	
	echo "<td><b>platby na F.O.: </b></td>";
	echo "<td><b>platby na s.r.o.: </b></td>";
	echo "</tr>";
	
	for($i=1;$i <= 12;$i++)
	{
			
	   echo "<tr>";
				 
	   if ( $i < 10){ $pozadovane_obdobi = $rok."-"."0".$i; }
	   else { $pozadovane_obdobi = $rok."-".$i; }
						   
	   $mesic_long = "0".$i;
	
	    echo "<td>$pozadovane_obdobi</td>";
	    
	    // vypis obojiho						    
	    $dotaz=pg_query("SELECT * FROM platby WHERE ( hotove='1' and zaplaceno_za LIKE '$pozadovane_obdobi' )");
	    $sum_placeni =  0;
	    while ($data=pg_fetch_array($dotaz)){ $sum_placeni = $sum_placeni + $data["castka"];  }
	
	    $sum_placeni_celkem = $sum_placeni_celkem + $sum_placeni;
	     
	    echo "<td> ".$sum_placeni."</td>";
	   
	    // vypis plateb na FO
	    $dotaz2=pg_query(" SELECT * FROM platby WHERE ( ( hotove='1') and ( firma is NULL ) and ( zaplaceno_za LIKE '$pozadovane_obdobi') ) "); 
	    $sum_placeni2 = 0;
	    while ($data2=pg_fetch_array($dotaz2)){ $sum_placeni2 = $sum_placeni2 + $data2["castka"]; }
		
	    $sum_placeni2_celkem = $sum_placeni2_celkem + $sum_placeni2;
	    
	    echo "<td>".$sum_placeni2."</td>";
	    
	    //vypis plateb na sro.
	   $dotaz3=pg_query(" SELECT * FROM platby WHERE ( (hotove='1') and (firma is not null) and ( zaplaceno_za LIKE '$pozadovane_obdobi') ) "); 
	   $sum_placeni3 = 0;
	   while ($data3=pg_fetch_array($dotaz3)){ $sum_placeni3 = $sum_placeni3 + $data3["castka"]; }
	
	   $sum_placeni3_celkem = $sum_placeni3_celkem + $sum_placeni3;
	    
	   echo "<td>".$sum_placeni3."</td>";
	
	  echo "</tr>";
	
	}
	
	  echo "<tr><td colspan=\"4\" height=\"30\" ><hr></td></tr>";
		
	  echo "<td><b> součet: </b></td><td><b> $sum_placeni_celkem </b></td><td><b> $sum_placeni2_celkem </b></td><td><b> $sum_placeni3_celkem </b></td>";
	
	echo "</table>";
 
  ?>
  
  </td>
  </tr>
  
 </table>
 
</body>
</html>