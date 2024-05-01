<?php

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

</head>
<body>

<?php require ("head.php"); ?>

<?php require ("category.php"); ?>

<tr>
    <td align="left" colspan="2">
<br>

    <!-- konec vlastniho obsahu -->	
    </td>
    </tr>
 </table>
 
 </body>
 </html>
 
 