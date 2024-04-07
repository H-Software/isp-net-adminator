<?php

include("../include/config.php");

include("../include/rss.function.php");

class rss_wrong_login
{
    var $subject,$body, $author;

    function rss_wrong_login($subject,$body,$author) 
    {
	$this->subject = $subject;
	$this->body = $body;
	$this->author = $author;
    }
		
}

//prvne pokus o autorizaci
$rs_check_login = check_login_rss($_GET["sid"]);

if( $rs_check_login == false )
{
  $row = new rss_wrong_login("spatny login", "Špatný login, prosím přihlašte se do administračního systému.","System");
  
  putHeader();
  putItem($row);
  putEnd();
}
else
{
  exportRSSS();
}

?>
