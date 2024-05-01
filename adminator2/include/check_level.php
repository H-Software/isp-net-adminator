<?php

use Cartalyst\Sentinel\Native\Facades\Sentinel;

function check_level ($user_level, $id, $adminator = null) {
  // co mame
  // v promeny level mame level prihlaseneho uzivatele
  // databazi levelu pro jednotlivy stranky

  // co chceme
  // porovnat level uzivatele s prislusnym levelem 
  // stranky podle jejiho id

  global $conn_mysql, $logger, $smarty;

  $logger->debug("checkLevel: called");

  if(is_object($adminator))
  {
      $a = $adminator;
  }
  else
  {
      $a = new \App\Core\adminator($conn_mysql, $smarty, $logger);
  }

  if ($id < 1){
      $logger->error("checkLevel: \$id < 1");
      return false;
  }

  $a->page_level_id = $id;
  $a->userIdentityUsername = Sentinel::getUser()->email;
  $logger->debug("checkLevel: current identity: ".var_export($a->userIdentityUsername, true));

  $checkLevel = $a->checkLevel();
  
  $logger->info("checkLevel: A->checkLevel result: ".var_export($checkLevel, true));

  if($checkLevel === false){
      return false;
  }
  
  return true;

}

function check_level2 ($user_level,$level_col)
{
  global $conn_mysql, $nick;
  try {
    $dotaz = $conn_mysql->query("SELECT ".$conn_mysql->real_escape_string($level_col)." FROM users_old WHERE login = '".$conn_mysql->real_escape_string($nick)."'");
    $radku = $dotaz->num_rows;
  } catch (Exception $e) {
    die ("<h2 style=\"color: red; \">Check level Failed: Caught exception: " . $e->getMessage() . "\n" . "</h2></body></html>\n");
  }

 if($radku <> 1)
 { return false; }

 while ($data = $dotaz->fetch_array())
 { $level_col_db = $data["$level_col"]; }

 if( $level_col_db == 1)
 { return true; }

}
