<?

require("include/main.function.shared.php");

init_ses();

require ("include/config.php"); 
require ("include/check_level.php");
// require ("include/check_login.php");

$sid = $_SESSION["db_login_md5"];
$level = $_SESSION["db_level"];
$nick =$_SESSION["db_nick"];

$date = Date("U");
$ad = Date("U") - 300;

if ( !( check_level($level,27) ) )
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

<title>Adminator 2 - vypis IP</title> 

</head> 

<body> 

<? 
// include ("head.php"); 
?> 

 <tr>
 <td colspan="2" height="50"></td>
  </tr>
 
 <tr>
 <td colspan="2"></td>
  </tr>
  
  <tr>
  <td colspan="2">
  
  <br>
  <H3>IP adresu nelze vygenerovat, použité ip:</H3>
  
  <form><INPUT TYPE="button" VALUE="Zavřít toto okno" onClick="window.close();"></form>
  <?
   
  $gen_ip_find=$_GET["id_rozsah"];
  
  if(empty($gen_ip_find)){
    echo "<div>Error! Chybne vstupni udaje (chybi id_rozsah)</div>";
    exit;
  }

  list($a,$b,$c,$d) = preg_split("/[.]/",$gen_ip_find);
  
  if ($c == 0 ){ $gen_ip_find=$gen_ip_find."/16"; }
  else
  { $gen_ip_find=$gen_ip_find."/24"; }
  
  $sql = "SELECT * FROM objekty WHERE ip <<= '" . $gen_ip_find . "' order by ip asc";
  // echo "<div>DEBUG SQL: " . $sql . "</div>";

  $msq_check_ip = pg_exec($db_ok2, $sql);
  $msq_check_ip_radku=pg_num_rows($msq_check_ip);
	  
    if ( $msq_check_ip_radku == 0 ) { $d=10; $gen_ip=$a.".".$b.".".$c.".".$d; }
    else {
	
    echo "<table width=\"100%\" ><tr>";
    while (  $data_check_ip=pg_fetch_array($msq_check_ip) )
      {       
      
      if ($sloupec == 3 ) { echo "<tr>"; }
      
      $ip=$data_check_ip["ip"];
      
      echo "<td>".$ip."</td>"; 
      
      $sloupec++;
      
      if ( $sloupec == 3 ){ echo "</tr>"; $sloupec=0; }
      
      }

    echo "</table>";
    }	  
  
  ?>
 
  </td>
  </tr>
  
 </table>
</body> 
</html>
 