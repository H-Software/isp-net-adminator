<?php

include ("include/config.php"); 
include ("include/check_login.php");

include ("include/check_level.php");

if ( !( check_level($level,23) ) )
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

<title>Adminator 2</title> 

</head> 

<body> 

<? include ("head.php"); ?> 

<? include ("category.php"); ?> 

 
 <tr>
 <td colspan="2" height="50"><span style="background-color: #DDDDDD; font-size: 18px;">  Administrace  </span></td>
  </tr>
 
 <tr>
 <td colspan="2"></td>
  </tr>
  
  <tr>
  <td colspan="2">
  
  
  <table width=100%>
  <tr>
  <td width="20%" valign="top"><? include ("admin-cat.php"); ?></td>
  
  <td valign="top">
  
  <?
  
   if ( ( $_POST["popis_new"] ) )
   
       {
     //budeme ukladat
   $popis=$_POST["popis_new"];
   $level=$_POST["level_new"];
   
   $id_new=$_POST["new_id"];
   
      
     echo "Zadáno do formuláre : <br><br>";
   // echo "id: ".$jmeno."<br>";
     echo "Nový popis stránky: ".$popis."<br>";
     echo "Nový level stránky: ".$level."<br>";
     
   // echo "Email: ".$email."<br><br>";
   // echo "Level: ".$level."<br><br>";
	
   
       $uprava=mysql_query("UPDATE leveling SET popis='$popis', level='$level' where id=".$id_new );
       												 
							   											   
     if ($uprava) echo "<br><br>MySql potvrdilo, takze: <br><H2>Data v databazi upravena.</H2><br><br>";
       else echo "Houstone, tento zapis do databaze nevysel :)";
    
    																       
    }
    else

   {
   //zobrazime formular
   
   //nejdrive nacteme predchozi data
   $update_id=$_POST["update_id"];
   
   $vysledek=mysql_query("select * from leveling where id=$update_id " );
   $radku=mysql_num_rows($vysledek);
	   
   if ($radku==0) echo "Zadné levely v db (divny) ";
	else
        {

	while ($zaznam=MySQL_Fetch_Array($vysledek)):
	
	$id=$zaznam["id"];
	$popis=$zaznam["popis"];
	$level=$zaznam["level"];
	
	endwhile;
	
	
	}





      print '
                 <br><H4>Úprava levelu stránky: </H4><br>
    <form method="POST" action="'.$_SERVER["PHP_SELF"].'">
     <table border="0" width="100%" id="table2">

  <tr>
  <td width="25%"><label>Popis stránky: </label></td>
  <td><input type="text" name="popis_new" size="30" value="'.$popis.'"></td
 </tr>

  <tr>
       <td><label>Level: </label></td>
        <td><input type="text" name="level_new" size="10" value="'.$level.'"></td>
 </tr>

  <tr>
    <td><br></td>
   <td><input type="hidden" name="new_id" value="'.$id.'"></td>
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
  
  </td>
  </tr>
  
 </table>

</body> 
</html> 

