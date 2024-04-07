<?php

require("include/config.php");

require_once ("include/check_login.php");

require_once ("include/check_level.php");

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

 $dotaz_historie=mysql_query("SELECT date, nick, ip FROM login_log ORDER BY date DESC LIMIT 5");
 
 echo "<div style=\"width: 800px; margin-left: auto; margin-right: auto; text-align: left; 
	font-family: Verdana; font-weight: bold; font-size: 14px; padding-bottom: 20px; \">
	Poslední přihlašení uživatelé: </div>";
 
 echo "<div style=\"font-family: Verdana; font-size: 12px; \" >";
 
 while( $data_historie=mysql_fetch_array($dotaz_historie) )
 {
    $datum = strftime("%d.%m.%Y %H:%M:%S", $data_historie["date"] );
    
    echo "<div style=\"width: 800px; margin-left: auto; margin-right: auto; text-align: left; padding-bottom: 2px; \" >";
    echo "uživatel: <span style=\"font-weight: bold; \">".$data_historie["nick"]."</span>, přihlášen dne: ";
    echo "<span style=\"font-style: italic; color: grey; font-weight: bold; \" >".$datum."</span>, z ip adresy: ";
    echo "<span style=\"font-style: italic; color: grey; font-weight: bold; \" >".$data_historie["ip"]." </span></div>";
  
 } // konec while

 echo "</div>";

 // vlozeni vypisu zavad/oprav
 if ( check_level($level,101) )
 {
   include("opravy-for-home.php"); 
 }
 
 echo "<br><br>";

 //informace z modulu neuhrazené faktury
 require("./faktury/fn-for-home.php");

 //generovani zprav z nastenky
 if( check_level($level,87) )
 {
   require("board-header-for-home.php"); //vložíme hlavičku

   require("board-main-for-home.php"); // hlavni data, poupravena
 }

?>

    <!-- konec vlastniho obsahu -->	
    </td>
    </tr>
 </table>
 
 </body>
 </html>
 
 