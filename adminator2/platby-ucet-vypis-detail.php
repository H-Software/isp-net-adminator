<?php

include ("include/config.php"); 
include ("include/check_login.php");

include ("include/check_level.php");

if ( !( check_level($level,70) ) )
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
    
    $id_platby=$_GET["id_platby"];
    
    include("include/config.pg.php");
        
    if ( !( isset($id_platby) ) )
    {
    
    echo "Vyberte platbu ...<br><br><br> dodělat";
    
    } 
    else
    {
    
	$dotaz=pg_query("SELECT * FROM platby WHERE id = '$id_platby' ");
	$dotaz_radku=pg_num_rows($dotaz);
    
	if ( $dotaz_radku == 0 )
	{ 
	echo "<br> Platbu se nepodařilo najít. <br>";
	} 
	else
	{
	    echo "<div style=\"font-size: 20px; \">Informace o platbě: </div><br><br>";
	    
	    while ($data=pg_fetch_array($dotaz) ):
	
	      echo "<b>id platby</b>: ".$data["id"]."<br>";
	      echo "<b>zaplaceno_za</b>: ".$data["zaplaceno_za"]."<br>";
	      echo "<b>částka: </b>".$data["castka"]."<br>";
	      echo "<b>daň</b>: ".$data["dan"]."<br>";
	      echo "<b>účet</b>: ".$data["ucet"]."<br>";
	      echo "<b>zaplaceno_dne</b>: ".$data["zaplaceno_dne"]."<br>";
	      echo "<b>z výpisu: </b>".$data["zvypisu"]."<br>";
	      echo "<b>hotově: </b>".$data["hotove"]."<br>";
	      echo "<b>firma: </b>".$data["firma"]."<br>";
	      
	      echo "<b>id_cloveka</b>: ".$data["id_cloveka"]."<br>";
	      
	    endwhile;
	}
    
    
    }
  
  ?>
  
  </td>
  </tr>
  
 </table>

</body>
</html>

