<?php

include ("include/config.php"); 
include ("include/check_login.php");

include ("include/check_level.php");

if ( !( check_level($level,41) ) )
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
   // sem zbytek
    
  //prvni odkaz - platby soucet
	   	
    echo "<br><div style=\"font-size: 18px; font-weight: bold; \" >Součty plateb: </div><br>";
			
    $dotaz=pg_exec($db_ok2," SELECT * FROM vlastnici WHERE ( archiv = 0 or archiv is null ) ");
    while (  $data=pg_fetch_array($dotaz) )
    {      $sum_placeni = $sum_placeni + $data["k_platbe"]; }
	
    echo "<hr align=\"left\" width=\"25%\" >";
		
    echo "součet plateb celkem: ".$sum_placeni."<br><br>";
	  
    echo "<hr align=\"left\" width=\"25%\" >";
	 
    $dotaz=pg_exec($db_ok2," SELECT * FROM vlastnici where ( (firma is null) and ( archiv = 0 or archiv is null ) ) ");
    while (  $data=pg_fetch_array($dotaz) )
    {      $sum_placeni2 = $sum_placeni2 + $data["k_platbe"]; }
	
    echo "součet plateb lidí na F.O.: ".$sum_placeni2."<br><br>";
	
    echo "<hr align=\"left\" width=\"25%\" >";
	 
    $dotaz=pg_exec($db_ok2," SELECT * FROM vlastnici where ( (firma is not null) and ( ( archiv = 0 or archiv is null ) ) ) ");
    while (  $data=pg_fetch_array($dotaz) )
    { $sum_placeni3 = $sum_placeni3 + $data["k_platbe"]; }
	
    echo "součet plateb lidí na S.r.o. : ".$sum_placeni3."<br><br>";
    
  ?>
  
  </td>
  </tr>
  
 </table>

</body>
</html>

