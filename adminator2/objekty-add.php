<?php

require("include/main.function.shared.php");
require("include/config.php"); 
require("include/check_login.php");
require("include/check_level.php");
// require("include/class.php"); 
// require("include/c_listing-objekty.php");

if( !( check_level($level,2) ) ) 
{
  header("Location: nolevelpage.php");
  echo "<br>Neopravneny pristup /chyba pristupu. STOP <br>";  
  exit;
}

echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"> 
      <html> 
      <head> ';
      
require ("include/charset.php"); 

?>

<title>Adminator2 - Přidání/úprava objektu</title> 

</head>

<body>

<?php require ("head.php"); ?> 
<?php require ("category.php"); ?> 

 <tr>
  <td colspan="2" height="20" bgcolor="silver">
    <?php require ("objekty-subcat-inc.php"); ?>
  </td>
 </tr>
	 
<tr>
  <td colspan="2">

<?php

// nastavovací promenne

$update_id=$_POST["update_id"];

global $odeslano;
$odeslano=$_POST["odeslano"];

$send = $_POST["send"];

$mod_objektu = $_POST["mod_objektu"];

global $nod_find;
$nod_find = $_POST["nod_find"];

if( ( strlen($nod_find) < 1 ) ){ $nod_find="%"; }
else
{
  if( !(preg_match("/^%.*%$/",$nod_find)) )
  { $nod_find="%".$nod_find."%"; }
}

if($mod_objektu == 2)
{
  require("objekty-add-inc-fiber.php");
}
else
{
  require("objekty-add-inc-wifi.php");
}

?> 

 </td>
  </tr>
  
 </table>

</body> 
</html> 

