<?php

include ("include/config.php"); 
include ("include/check_login.php");

include ("include/check_level.php");

if ( !( check_level($level,62) ) )
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

<title>Adminator 2 - monitoring</title> 

</head> 

<body> 

<?php include ("head.php"); ?> 

<?php include ("category.php"); ?> 

 <tr>
 <td colspan="2" ><?php require("monitoring-cat.php"); ?></td>
  </tr>

  <tr>
  <td colspan="2">
  
  <!-- zacatek vlastniho obsahu -->
  
  <?php
  
  
   $cat_jmeno=$conn_mysql->real_escape_string($_POST["cat_jmeno"]);

if ( $cat_jmeno )
{
// ukladame
  $jmeno=$conn_mysql->real_escape_string($_POST["cat_jmeno"]);
  $sablona=$conn_mysql->real_escape_string($_POST["cat_sablona"]);

   echo "<H3>Zadáno: </H3><br>";

    echo "jméno : ".$jmeno." \n <br>";
    echo "šablona: ".$sablona." \n <br>";

    $uprava=$conn_mysql->query("insert into kategorie (jmeno,sablona) values ('$jmeno','$sablona') ");

    if ($uprava)echo "<br>MySql potvrdilo, takze:<br><H1> Data v databazi upravena.</H1><br>\n";
    else echo "Houstone tato uprava databaze selhala<br><br>";


}
else

{
//zobrazujeme form


echo '

	
<form method="POST" action="'.$_SERVER["PHP_SELF"].'">
<table border="1" width="100%" id="table1">

    <tr>
        <td width="30%" colspan="2">
            <p><H2>Přidání kategorie pro grafy</H2></p>
        </td>


    </tr>

    <tr>
        <td>
            <label>Jméno kategorie</label>
        </td>

        <td>
            <p><input type="text" name="cat_jmeno" size="20"></p>
        </td>

    </tr>

    <tr>
        <td><label>Šablona: </label></td>

        <td>
            <select size="1" name="cat_sablona">
                <option value="2">Pingy</option>
        	<option value="4">Routery (Mikrotik) </option>
	    </select>
        </td>

    </tr>

    <tr>
        <td><br></td>
        <td></td>
    </tr>

    <tr>
        <td><input type="reset" value="RESET" name="B2"> </td>
        <td >
            <p><input type="submit" value="OK" name="B1"></p>
        </td>
    </tr>

    </table>
    </form>';

    }

  
  
  ?>
  
  <!-- konec vlastniho obsahu -->

   </td>
  </tr>
 
 </table>

</body> 
</html> 

