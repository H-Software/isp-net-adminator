<?php

include ("include/config.php"); 
include ("include/check_login.php");

include ("include/check_level.php");

if ( !( check_level($level,87) ) )
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

<title>Adminator 2 - Nástěnka </title> 

<link href="board-css.css" rel="stylesheet" type="text/css">
</head> 

<body> 

<? include ("head.php"); ?> 

<? include ("category.php"); ?> 

 
 <tr>
 <td colspan="2" height=""></td>
  </tr>
 
  <tr>
  <td colspan="2">

<table width="600" border="0" cellspacing="1" cellpadding="0" align="center" bgcolor="Black">
 <tr><td>
    <table width="100%" border="0" cellspacing="2" cellpadding="2" align="center" bgcolor="#7D7642">
	 <tr><td class="heading">Bulletin Board - Nástěnka ver. 1.0</td></tr>
	    </table>
	     </td></tr>
	     </table>
	     
	     <table width="600" border="0" cellspacing="0" cellpadding="0" align="center" bgcolor="Black"><tr><td>
	     <table width="100%" border="0" cellspacing="1" cellpadding="3" align="center">
	      <tr bgcolor="#eaead7">
	        <td align="center" class="tableheading"><a href="board-main.php?action=post">PŘIDAT ZPRÁVU</a></td>
		    <td align="center" class="tableheading"><a href="board-main.php?action=view&what=new"</a>AKTUÁLNÍ ZPRÁVY</a></td>
			<td align="center" class="tableheading"><a href="board-main.php?action=view&what=old">STARÉ ZPRÁVY</a></td>
			    <td align="center" class="small"><?echo Date("j. m. Y");?></td>
			     </tr>
			     </table>
			     </table>
  
  
 