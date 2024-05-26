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
  $logger->debug("checkLevel: current identity: ".var_export($a->userIdentityUsername, true) . ", level: " . var_export($a->userIdentityLevel, true));

  $checkLevel = $a->checkLevel();
  
  $logger->info("checkLevel: A->checkLevel result: ".var_export($checkLevel, true));

  if($checkLevel === false){
      return false;
  }
  
  return true;

}

