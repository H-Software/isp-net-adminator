<?php

require("include/main.function.shared.php");
require("include/config.php"); 
require("include/check_login.php");
require("include/check_level.php");


$level_col = "lvl_admin_login_iptv";

if( !( check_level2($level,$level_col) ) )
{ // neni level
  header("Location: ".$cesta."nolevelpage.php");
  
  echo "<br>Neopravneny pristup /chyba pristupu. STOP <br>";
  exit;
}

echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"> 
      <html> 
      <head>';

echo "<script type=\"text/javascript\" src=\"/include/js/adminator-global.js\"></script>";

require("include/charset.php"); 

?>

<title>Adminator 2 - login do IPTV portálu</title> 

</head> 
<body> 

<?php require ("head.php"); ?> 
<?php require ("category.php"); ?> 
 
 <tr>
  <td colspan="2" height="20" bgcolor="silver" >
   <?php require("admin-subcat2-inc.php"); ?>
   </td>
  </tr>
        
  <tr>
  <td colspan="2">
  <!-- zacatek vlastniho obsahu -->

<?php
//form promenne
    
    echo "<span style=\"padding-left: 25px; \" >\n\n";
       
    	    echo "<form method=\"post\" action=\"http://u150-137.static.iptv.gateway:9080/admin/j_acegi_security_check\" name=\"loginForm\" >\n";
    	    
    	    echo "<input type=\"hidden\" name=\"j_username\" id=\"username\" value=\"simelon-adminator\" >\n";

//    	    echo "<input type=\"hidden\" name=\"j_password\" value=\"adminator2\" >\n";    	    
    	    echo "<input type=\"hidden\" name=\"j_password\" value=\"password\" >\n";
    	    
    	    //echo "<input type=\"hidden\" name=\"submit\" value=\"Login\" >\n";
    	    
	    //echo "<a href=\"javascript: document.iptvportal.submit();\" >".
	    //	"aktivace funkcí IPTV portálu (přihlašení)</a>";
	    
	    echo "<script type=\"text/javascript\">document.loginForm.submit();</script>\n";
	    
	    echo "</form>\n\n";
	    
    echo "</span>
       
      </div>\n";

?>

 <!-- konec vlastniho obsahu -->
  </td>
  </tr>
  
 </table>

</body>
</html>
