<?php

$action=$_GET["action"];
$what=$_GET["what"];
$page=$_GET["page"];
// $ac$[];

$submit=$_POST["submit"];
$send=$_POST["send"];
$sent=$send;

$author=$_POST["author"];
$email=$_POST["email"];

$from_day=$_POST["from_day"];
$from_month=$_POST["from_month"];
$from_year=$_POST["from_year"];

if ( !isset($from_day) )
{ 
$from_month = date("m");
$from_day = date("d");
$from_year = date("Y");

}

$to_day=$_POST["to_day"];
$to_month=$_POST["to_month"];
$to_year=$_POST["to_year"];

if ( !isset($to_day) )
{ 

$to_month = date("m");
$to_day = date("d");
$to_year = date("Y");

$to_day = $to_day + "7";

if ( $to_day > 31)
{
$to_day = "1";
$to_month = $to_month + "1";
}

}

$subject=$_POST["subject"];
$body=$_POST["body"];	


$query_error = 'Došlo k chybě při zpracování SQL dotazu v databázi.'; //chybová hláška
if ( ( (!isset($action)) and (!isset($send)) ) ) $action = "view"; //ještě není zinicializována proměnná $action
if (!isset($what)) $what = "new"; //ještě není zinicializována proměnná $what
if (!isset($page)) $page = 0; //ještě není zinicializována proměnná $page

if ($action=="view"): //zobrazujeme zprávy
 require("board-header.php"); //vložíme hlavičku
 // require "db.php"; //otevřeme databázi

if( !isset($author) )
{
 global $nick;
 $author=$nick;
}

?>
 
 <table width="600" border="0" cellspacing="0" cellpadding="0" align="center">
  <tr><td class="tableheading">
	<?
	//zobrazujeme aktuální nebo staré zprávy
	if($what=="new"):
		echo "->> Aktuální zprávy";
		$sql = "from_date <= NOW() AND to_date >= NOW()";
	else:
	  	echo "->> Staré zprávy";
		$sql = "to_date < NOW()";
	endif;
	?>	 	
 	<hr width="100%" size="1" color="#7D7642" noshade>
  </td></tr>
 </table>
 
 <?
 $view_number = 10; //zprávy budou zobrazeny po ...
 $start = $page*$view_number; //první zpráva, která se zobrazí
 $message = mysql_query("SELECT * FROM board WHERE $sql ORDER BY id DESC LIMIT $start,$view_number") or die($query_error); //vybíráme zprávy - seřazeno podle id

 //vypíšeme tabulky se zprávami
 while ($entry = mysql_fetch_array($message)):
 ?>
	<table width="600" border="0" cellspacing="0" cellpadding="1" align="center"><tr><td class="tableheading"><?echo "zpráva č. " . $entry["id"]?></td></tr></table>
	<table width="600" border="0" cellspacing="0" cellpadding="1" align="center" bgcolor="#7D7642"><tr><td>
	 <table width="100%" border="0" cellspacing="0" cellpadding="2" align="center" bgcolor="#eaead7">
	  <tr>
	 	<td class="table">
		 <?
		 $from = Explode("-", $entry["from_date"]); //od
		 $to = Explode("-", $entry["to_date"]); //do

 		 if ($entry["email"]!="") echo '<a href="mailto:' . $entry["email"] . '">'; //zadal autor svůj email
		 echo "<b>" . $entry["author"] . "</b>"; //jméno
		 if ($entry["email"]!="") echo '</a>';
		 echo "<br>";
		 echo "<b>" . $entry["subject"] . "</b>" . " [". $from[2] . ". " . $from[1] . ". " . $from[0] . " - " . $to[2] . ". " . $to[1] . ". " . $to[0] . "]"; //předmět [od - do]
		 echo "<br><br>";
		 echo $entry["body"]; //zpráva
 	 	 ?>
 		 </td>
	  </tr>
	 </table>
	</table><br>
 <?php endwhile;?>
	
 <table width="600" border="0" cellspacing="0" cellpadding="0" align="center">
  <tr><td align="right" class="table">
 	<hr width="100%" size="1" color="#7D7642" noshade>
	<b>strana: 
	<?
	//odkazy na starší zprávy (u právě zobrazené zprávy se odkaz nevytvoří)
	$count = mysql_query("SELECT id FROM board WHERE $sql") or die($query_error); //vybíráme zprávy
	$page_count = ceil(mysql_num_rows($count)/$view_number); //počet stran, na kterých se zprávy zobrazí
	for($i=0;$i<$page_count;$i++):
		echo " | ";
		if($page!=$i) echo '<a href="board-main.php?action=view&what=' . $what . '&page=' . $i . '">';
		echo ($i+1);
		if($page!=$i) echo '</a> ';
	endfor;
	//MySQL_Close(); //zavřeme databázi
	?>
	|</b>
  </td></tr>
 </table>

<?
else: //formulář nebo uložení zprávy

 $write = false; //předpokládáme zobrazení formuláře

 if(isset($sent)):	//byl odeslán formulář?
	 if($author=="" || $subject=="" || $body==""):	//byly vyplněny všechny povinné údaje?
		$error = 'Musíte vyplnit všechny povinné údaje - označeny tučným písmem.';
	 elseif(mktime(0,0,0,$from_month,$from_day,$from_year) > mktime(0,0,0,$to_month,$to_day,$to_year)): //zkontrolujeme data od-do
	 	$error = 'Datum OD nesmí být větší než datum DO.';
	 elseif(mktime(0,0,0,$from_month,$from_day,$from_year) < mktime(0,0,0, date("m"), date("d"), date("Y"))):
	 	$error = 'Datum OD nesmí být menší než dnešní datum.';
	 else:
	 	$write = true; //provedeme zápis
	 endif;
 endif;

 if($write): //ulozeni dat
	// echo "tututu ";
  	require("board-post.php");
 else: //zobrazujeme formulář
 	require("board-header.php");
 	require("board-form.php");
 endif;
endif;
?>


 <!-- konec vlastniho obsahu -->
   </td>
     </tr>
     
      </table>
      
      </body>
      </html>
      