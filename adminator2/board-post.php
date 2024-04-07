<?php

//odstraníme nebezpečné znaky
$author = HTMLSpecialChars($author);
$email = HTMLSpecialChars($email);
$subject = HTMLSpecialChars($subject);

$body = SubStr($body, 0, 1500);		//bereme pouze 1500 znaků
$body = Trim($body);				//odstraníme mezery ze začátku a konce řetězce
$body = HTMLSpecialChars($body);	//odstraníme nebezpečné znaky
$body = Str_Replace("\r\n"," <BR> ", $body);	//nahradíme konce řádků na tagy <BR>

$body = WordWrap($body, 90, "\n", 1); //rozdělíme dlouhá slova

//vytvoříme odkazy
$body = EregI_Replace("(http://[^ ]+\.[^ ]+)", " <a href=\\1>\\1</a>", $body);
$body = EregI_Replace("[^/](www\.[^ ]+\.[^ ]+)", " <a href=http://\\1>\\1</a>", $body);

//povolíme tyto tagy - <b> <u> <i>, možnost přidat další
$tag = Array("b", "u", "i");
for($y=0;$y<Count($tag);$y++):
	$body = EregI_Replace("&lt;" . $tag[$y] . "&gt;", "<" . $tag[$y] . ">", $body);
	$body = EregI_Replace("&lt;/" . $tag[$y] . "&gt;", "</" . $tag[$y] . ">", $body);
endfor;

$from = Date("Y-m-d", MkTime(0,0,0,$from_month,$from_day,$from_year)); //od
$to = Date("Y-m-d", MkTime(0,0,0,$to_month,$to_day,$to_year));//do

require "include/config.php"; //otevřeme databázi
$add = mysql_query("INSERT INTO board VALUES ('', '$author', '$email', '$from', '$to', '$subject', '$body')") or die($query_error); //vložíme zprávu
// mysql_Close(); //zavřeme databázi

// echo "f pořádku";

header("Location: board-header.php"); //přesuneme se na úvodní stránku

?>

