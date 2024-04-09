<?php

require 'smarty/Smarty.class.php';
require("include/main.function.shared.php");
require("include/config.php");
require_once ("include/check_login.php");
require_once ("include/check_level.php");

$smarty = new Smarty;
$smarty->compile_check = true;
//$smarty->debugging = true;

echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
      <html>
      <head> ';

require_once ("include/charset.php");

?>

<title>Adminátor2 - home</title> 

<link href="board-css.css" rel="stylesheet" type="text/css">

</head>

<body>

<?php require ("head.php"); ?>

<?php require ("category.php"); ?>

<tr>
    <td align="left" colspan="2">
<br>

<?php

//vlozeni prihlasovaci historie
list($r, $render) = list_logged_users_history($conn_mysql, $smarty, "fetch");
echo $render;

 // vlozeni vypisu zavad/oprav
 if ( check_level($level,101) )
 {
   require("opravy-for-home.php"); 
 }
 
 echo "<br><br>";

 //informace z modulu neuhrazené faktury
 require("./faktury/fn-for-home.php");

 //generovani zprav z nastenky
 if( check_level($level,87) )
 {
   // moved to A3
   // require("board-header-for-home.php"); //vložíme hlavičku
   // require("board-main-for-home.php"); // hlavni data, poupravena
 }

?>

    <!-- konec vlastniho obsahu -->	
    </td>
    </tr>
 </table>
 
 </body>
 </html>
 
 