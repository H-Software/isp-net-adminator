<?php

include ("include/config.php"); 
include ("include/check_login.php");

include ("include/check_level.php");

if ( !( check_level($level,20) ) )
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

<title>Adminator 2</title> 

</head> 

<body> 

<? include ("head.php"); ?> 

<? include ("category.php"); ?> 

 
 <tr>
 <td colspan="2" height="50" bgcolor="silver">
  <? include("admin-subcat2-inc.php"); ?>
 </tr>
 
  <tr>
  <td colspan="2">
 
  <?
  
   if ( ( $_POST["popis"] ) )
   
       {
     //budeme ukladat
   $popis=$_POST["popis"];
   $level=$_POST["level"];
   
      
     echo "Zadáno do formuláre : <br><br>";
   // echo "id: ".$jmeno."<br>";
     echo "Popis stránky: ".$popis."<br>";
     echo "Level: ".$level."<br>";
     
   // echo "Email: ".$email."<br><br>";
   // echo "Level: ".$level."<br><br>";
	
   												 
   $add=mysql_query("INSERT INTO leveling (popis, level ) VALUES ('$popis','$level')");
													   
											   
     if ($add) echo "<br><br>MySql potvrdilo, takze: <br><H2>Data v databazi upravena.</H2><br><br>";
       else echo "Houstone, tento zapis do databaze nevysel :)";
    
    																       
    }
    else

   {
   //zobrazime formular
      print '
           <br><H4>Přidání levelu stránky: </H4><br>
    <form method="POST" action="'.$_SERVER["PHP_SELF"].'">
     <table border="0" width="100%" id="table2">

  <tr>
  <td width="25%"><label>Popis stránky: </label></td>
  <td><input type="text" name="popis" size="30"></td
 </tr>

  <tr>
       <td><label>Level: </label></td>
        <td><input type="text" name="level" size="10"></td>
 </tr>

  <tr>
    <td><br></td>
   <td></td>
   </tr>

   <tr>
    <td><br></td>
    <td></td>
   </tr>

    <tr>
     <td></td>
      <td><input type="submit" value="OK" name="B1">
   <input type="reset" value="vymazat" name="B2"></td>
    </tr>

 </table>

 </form>';

 }
  
 ?>
  
  </td>
  </tr>
  
 </table>

</body> 
</html> 

