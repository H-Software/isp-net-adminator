<?php

function list_logged_users_history($conn_mysql, $smarty, $action = "assign") {
  $r = array();

  $rs=$conn_mysql->query(
    "SELECT nick, date, ip FROM login_log ORDER BY date DESC LIMIT 5"
  );

  while ($data=$rs->fetch_array()){
     $datum = strftime("%d.%m.%Y %H:%M:%S", $data["date"] );
     $logged_users[] = array( "nick" => $data["nick"], "datum" => $datum, "ip" => $data["ip"]);    
  }
 
  if($action == "assign"){
    $smarty->assign("logged_users",$logged_users);
    $r[0] = TRUE;
  }
  elseif($action == "fetch"){
    $smarty->assign("logged_users",$logged_users);
    $render = $smarty->fetch("inc.home.list-logged-users.tpl");
    $r[0] = TRUE;
    $r[1] = $render;
  }
  else{
    $r[0] = FALSE;
    $r[1] = "unknown action";
  }

  return $r;
}
