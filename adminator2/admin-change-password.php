<?php

require_once ("include/config.php"); 
require_once ("include/check_login.php");

require_once ("include/check_level.php");

if ( !( check_level($level,22) ) )
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

require ("include/charset.php"); 

?>

<title>Adminator 2</title> 

</head> 

<body> 

<?php require ("head.php"); ?> 

<?php require ("category.php"); ?> 

 <tr>
  <td colspan="2" height="20" bgcolor="silver">
      <?php require("admin-subcat2-inc.php"); ?>
  </td>
 </tr>

 <tr>
  <td colspan="2" >
  
    <br> <div style="font-size: 20px">Změna hesla </div><br><br>
  
  <?php
  if ( ! ( $_POST["new_pass"])  )
  
  {
  //zobrazime form
    echo '<form method="POST" action="">';
    echo '<table width="50%">';
    
    
    echo '<tr><td> <label>Stávající heslo:</label></td> <td><input type="password" name="old_pass" size="20"> </td></tr>';
    echo '<td><td colspan="2"><br></td></tr>';
	  
    echo '<tr><td> <label>Nové heslo:</label></td> <td><input type="password" name="new_pass" size="20"> </td></tr>';
    echo '<td><td colspan="2"><br></td></tr>';
    
    echo '<tr><td> <label>Nové heslo znovu:</label></td> <td><input type="password" name="new_pass2" size="20"> </td></tr>';
    echo '<td><td colspan="2"><br></td></tr>';
	  
    
    echo '
    
	  <tr><td></td><td><input type="submit" value="OK" name="B1">
	  </td></tr>
	     
    
	';
    
    echo "</table></form>";
   }
   else
   {
   // budeme ukladat
   $nick=$_SESSION["db_nick"];

     $old_pass=$_POST["old_pass"];
     $new_pass=$_POST["new_pass"];
     $new_pass2=$_POST["new_pass2"];
	
    $old_pass=md5($old_pass);

    // checkneme stary heslo
    $vysl_old_pass=mysql_query("select * from users_old where password='".$old_pass."' " );
    $radku_old_pass=mysql_num_rows($vysl_old_pass);
	
    if ( $radku_old_pass==0 )     
    {
    
    echo "Nesouhlasí staré heslo.";
    
    }    
    else
    {
    // stare heslo spravne, ted checknem nove heslo
    
    if ( !( $new_pass==$new_pass2) )
    {
    echo "<p>Nové heslo nesouhlasí.</p>";
    
    }
    else
    {
    
     echo "Změna heslo pro uživatele ".$nick."...<br>";

    $new_pass=md5($new_pass);
    	  
    // uz to upravime
    $add=mysql_query("UPDATE users_old SET password ='$new_pass' WHERE login = '".mysql_real_escape_string($nick)."' ");

    if($add) 
    { echo "<br><H3><div style=\"color: green; \" >Heslo v databázi úspěšně změněno.</div></H3>\n"; }
    else
    { 
	echo "<br><H3><div style=\"color: red; \">Chyba! Heslo v databázi nelze změnit. </div></h3>\n"; 
	echo "chyba: ".mysql_errno($MC) . ": " . mysql_error($MC) . "\n";
    }

    echo "<br>";	   				   
    /*
    if ($add) echo "<br><br>MySql potvrdilo, takze: <br><H2>Data v databazi upravena.</H2><br><br>";
   else echo "Mysql : data v databázi <b>neupravena</b>.";
   */
   
   //konec else jestli souhlasi hesla     
   }
    
   // konec else je li pocet radku nula
    }
    					          
   // konec else jestli uz je odeslala form data
   }
    ?>

  </td>
  </tr>
  
 </table>

</body> 
</html> 
