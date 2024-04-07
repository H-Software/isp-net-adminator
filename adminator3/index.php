<?php

require 'smarty/Smarty.class.php';

require "include/config.php";
require "include/main.function.php";

$smarty = new Smarty;

$smarty->compile_check = true;
//$smarty->debugging = true;

session_start();

//priprava promennych
$login = $_POST["login"];
$password = $_POST["password"];

$lo = $_GET["lo"];
$lp = $_GET["lp"];

if( isset($lp) )
{ $lp_on = 1; }

if( ( strlen($login) > 0) )
{
  if( !(ereg('^([[:alnum:]]+)$',$login)) )
  { 
    $error = true;
    $body .= "<div>Neplatna vstupni data v poli \"Login\" </div>";
  }
}
	 
if( ( strlen($password) > 0) )
{
 // if( !(ereg('^([[:alnum:]]+)$',$password)) )
 // { echo "Neplatna vstupni data"; exit; }
}

//hlavni porovnavani
if( (isset($login)) and (isset($password)) and !isset($error))
{

  $p = md5($password);
 
  global $MSQ;

  try {
    $MSQ = $conn_mysql->query(
      "SELECT * FROM users WHERE (login LIKE '$login') AND (password LIKE '$p') "
    );
  } catch (Exception $e) {
    $smarty->assign("lp_on",$lp_on);
    $smarty->assign("last_page",$lp);
    $smarty->assign("page_title", "Adminator3 :: login failed!");
    $smarty->assign("body", "<h2 style=\"color: red; \">Error: Login failed!</h2><h3 style=\"color: red; \">Caught exception: " .  $e->getMessage() . "</h3>");
    
    $smarty->display("login-form.tpl");
    die;
  }

 $MSQ_R = $MSQ->num_rows;
 
 if ( $MSQ_R <> 1 )
 {

  $body .= "<div>Chybný login / Chyba přístupu. Prosím zkuste se přihlásit znovu.</div>"; 

  $smarty->assign("lp_on",$lp_on);
  $smarty->assign("last_page",$lp);

  $display_page = "login-form.tpl";

  $page_title = "Adminator3 :: wrong login";

 }
 else
 { 
  // uzivatel se zalogoval spravne, ted to ulozit do db

  //hadry okolo session
  $SN = "autorizace"; 
  session_name("$SN"); 
  session_register("db_login_md5");
  session_register("db_level");
  session_register("db_nick");

  $time = date("U"); 
  $at = date("U") - 1800; 

  //co budeme ukladat do db ? zahashovany jmeno usera, nejdriv ho ale musime zjistit

  $radek = mysql_fetch_array($MSQ);
  $db_login = $radek["login"];
  $db_nick = $radek["login"];
  $db_level = $radek["level"];

  $db_login_md5 = md5($db_login); 

  //ted to nahazem do session
  $_SESSION["db_login_md5"] = $db_login_md5;
  $_SESSION["db_level"] = $db_level;
  $_SESSION["db_nick"] = $db_nick;

  //ted zjistime jestli nejde o refresh stanky :)
  $MSQ_A = mysql_query("SELECT * FROM autorizace WHERE (id LIKE '$db_login_md5')");

  if( mysql_num_rows($MSQ_A) == 1)
  {
   //ehm refresh, takze nic :)
  }
  elseif( mysql_num_rows($MSQ_A) < 1 )
  {
   // user v db neni, takze ho tam pridame
   $MSQ_A2 = mysql_query("INSERT INTO autorizace VALUES ('$db_login_md5', '$time', '$db_nick', '$db_level')");

   //hodime to este do logu
   $ip=$_SERVER['REMOTE_ADDR'];

   $MSQ_X = mysql_query("INSERT INTO login_log VALUES ('','$db_nick', '$time','$ip' )");
  }
  
  // $MSQ_D = MySQL_Query("DELETE FROM autorizace WHERE time < $at");

  // presmerovani na zakladnu :)
  $display_page = "login-redirect.tpl";

  $page_title = "Adminator3 :: login success";

  $smarty->assign("lp_on",$lp_on);
  $smarty->assign("last_page",$lp);
 
 }
 
} 
elseif (isset($lo))
{ //log out

 $SN = "autorizace";
 session_name("$SN"); 

 $sid=$_SESSION["db_login_md5"];

 $delka=strlen($sid);

 $MSQ_D = mysql_query("DELETE FROM autorizace WHERE (id LIKE '$sid')");
 $MSA_D = mysql_affected_rows($MSQ_D);

 $smarty->assign("delka",$delka);

 $smarty->assign("rs_delete",$MSQ_D);

 $page_title = "Adminator3 :: logout";
 
 $display_page = "login-form-logout.tpl";

 $smarty->assign("lp_on",$lp_on);
 $smarty->assign("last_page",$lp);
 
 session_destroy();
 
}
else
{ // prihlasovaci dialog ...
 
 $display_page = "login-form.tpl";
 $page_title = "Adminator3 :: login";

 $smarty->assign("lp_on",$lp_on);
 $smarty->assign("last_page",$lp);

}

$smarty->assign("page_title",$page_title);

$smarty->assign("body",$body);

$smarty->display($display_page);
