<?php

include ("include/config.php"); 
include ("include/check_login.php");

include ("include/check_level.php");

if ( !( check_level($level,46) ) )
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

<title>Adminator2 - Topology</title> 

</head> 

<body> 

<? include ("head.php"); ?> 

<? include ("category.php"); ?> 

 
 <tr>
  <td colspan="2">
  
    <table border="1" width="100%" id="table1">
	  <tr>
              <td colspan="2">
	      
	      <?  include ("topology-cat2.php"); ?>
	     </td>
	  </tr>
		  
	  
	  <tr>
			   
        <td valign="top">
					     
	<br>Odřazení:<br>
	   
 <?
 
  
 $dns=$_POST["dns_smazat"];
 $dns_delka=strlen($dns);
 
  if  ( !( $dns_delka > 0 )  )
  { echo "Nevyhovujici zadani "; Exit; }
  
  // $dns=$input{'dns_smazat'};
  
  echo "<br>Zadaná data: <br><br>\n";
 
  echo "clovek u kterého se má smazat přiřazení: $dns <br>";
  

    $data = array( "dns_jmeno"=>$dns);
    $update = array( "id_nodu"=>"0" );		      

      // This is safe, since $_POST is converted automatically
    $res = pg_update($db_ok2, 'objekty', $update, $data);
		
	//   system("/srv/www/htdocs.ssl/adminator2/scripts/merge-del.pl ".$dns); 

    if ($res)
    { echo  "<p><H3>PgSql potvrdilo</H3>, takže data upraveny!</p>"; }
    
    else { echo "<p>Houstone mame problem, tento zapis do db nevysel </p>"; }
    

	   ?>
	   
	   </td></tr></table>

    <!-- konec hlavni tabulky -->	
  </td>
  </tr>
  
 </table>

</body> 
</html> 

