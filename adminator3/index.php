<?php

require_once "include/main.function.shared.php";
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

if( isset($lp) and (strlen($lp) > 0) )
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

  $body .= "<h2 style=\"color: red; \">Chybný login / Chyba přístupu. Prosím zkuste se přihlásit znovu.</h2>"; 

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

  $time = date("U"); 
  $at = date("U") - 1800; 

  //co budeme ukladat do db ? zahashovany jmeno usera, nejdriv ho ale musime zjistit

  $radek = $MSQ->fetch_array();
  $db_login = $radek["login"];
  $db_nick = $radek["login"];
  $db_level = $radek["level"];

  $db_login_md5 = md5($db_login); 

  //ted to nahazem do session
  $_SESSION["db_login_md5"] = $db_login_md5;
  $_SESSION["db_level"] = $db_level;
  $_SESSION["db_nick"] = $db_nick;

  //ted zjistime jestli nejde o refresh stanky :)
  try {
    $MSQ_A = $conn_mysql->query("SELECT id FROM autorizace WHERE (id LIKE '".$conn_mysql->real_escape_string($db_login_md5)."')");
  } catch (Exception $e) {
      die ("<h2 style=\"color: red; \">Login Failed (check refresh): Caught exception: " . $e->getMessage() . "\n" . "</h2></body></html>\n");
  }

  if($MSQ_A->num_rows == 1)
  {
      // ehm refresh, takze nic :)
  }
  elseif($MSQ_A->num_rows < 1)
  {
    // user v db neni, takze ho tam pridame
    try {
        $MSQ_A2 = $conn_mysql->query("INSERT INTO autorizace ".
        " VALUES ('".$conn_mysql->real_escape_string($db_login_md5)."',".
        " '".intval($time)."',".
        " '".$conn_mysql->real_escape_string($db_nick)."',".
        " '".intval($db_level)."') ");
    } catch (Exception $e) {
        die ("<h2 style=\"color: red; \">Login Failed (insert into autorizace): Caught exception: " . $e->getMessage() . "\n" . "</h2></body></html>\n");
    }

    //hodime to este do logu
    $ip=$_SERVER['REMOTE_ADDR'];
    try {
        $MSQ_X = $conn_mysql->query("INSERT INTO login_log ".
                            "VALUES (NULL,'".$conn_mysql->real_escape_string($db_nick)."',".
                            " '".$conn_mysql->real_escape_string($time)."',".
                            " '".$conn_mysql->real_escape_string($ip)."' )");
    } catch (Exception $e) {
        die ("<h2 style=\"color: red; \">Login Failed (insert into login_log): Caught exception: " . $e->getMessage() . "\n" . "</h2></body></html>\n");
    }
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

 $sid=$conn_mysql->real_escape_string($_SESSION["db_login_md5"]);

 $delka=strlen($sid);

 $MSQ_D = $conn_mysql->query("DELETE FROM autorizace WHERE (id LIKE '$sid')");
 $MSA_D = $conn_mysql->affected_rows;

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
