<?php

include ("include/config.php"); 
include ("include/check_login.php");

include ("include/check_level.php");

if ( !( check_level($level,19) ) )
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

<title>Adminator 2 :: administrace :: výpis uživatelů</title> 

</head> 

<body> 

<? include ("head.php"); ?> 

<? include ("category.php"); ?> 
 
  <tr>
   <td colspan="2" height="20" bgcolor="silver">
    <? include("admin-subcat2-inc.php"); ?>
   </td>
  </tr>
       
 <tr>
  <td colspan="2">
   
 <?
  
    $vysledek=$conn_mysql->query("select * from users_old order by name asc" );
    $radku=mysql_num_rows($vysledek);
	
    if ($radku==0) echo "Zadní uživatelé v db (divny) ";
    else
    {
	    echo "<br><br>Výpis uživatelů: <BR><BR>";
				    
	    echo "<table border=\"1\" width=\"100%\" >";
						    
	    echo "\n<tr>
	    <td width=\"5%\"><b>id:</b></td>
	    <td width=\"20%\"><b>Přihlašovací jméno: </b></td>
	    <td width=\"20%\"><b>Jméno: </b></td>
	    <td width=\"20%\"><b>Heslo: </b></td>
													    
	    <td width=\"15%\"><b>Email: </b></td>
	    <td width=\"20%\"><b>Level: </b></td>
															    
	    <td width=\"\"><b>Samba user: </b></td>
	    <td width=\"\"><b>Samba heslo: </b></td>
					    
	    <td><b>Úprava: </b></td>
	    <td><b>Smazání: </b></td>
	    </tr>\n";
							
	    echo "\n";
	
	while ($zaznam=mysql_fetch_array($vysledek)):
	 
	  echo "<tr><td>".$zaznam["id"]."</td>\n";
	  echo "<td>".$zaznam["login"]."</td>\n";
	  echo "<td>".$zaznam["name"]."</td>\n";
	  echo "<td >".$zaznam["password"]."</td>\n";
	  echo "<td>".$zaznam["email"]."</td>\n";
	  echo "<td>".$zaznam["level"]."</td>\n";
	  
	  echo "<td>".$zaznam["smb_user"]."</td>\n";
	  echo "<td>".$zaznam["smb_pass"]."</td>\n";
	  
	  
	  echo '<td><form method="POST" action="admin-user-add.php">
	      <input type="hidden" name="update_id" value="'.$zaznam["id"].'">
	      <input type="submit" value="update">
	      </form></td>';
								      
	 echo "</tr>";
	
	endwhile;
    }
?>

  </td>
  </tr>
  
 </table>

</body>
</html>

