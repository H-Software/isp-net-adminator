<?php

function check_level ($user_level,$id) {
  // co mame
  // v promeny level mame level prihlaseneho uzivatele
  // databazi levelu pro jednotlivy stranky

  // co chceme
  // porovnat level uzivatele s prislusnym levelem 
  // stranky podle jejiho id

  global $conn_mysql;

  try {
    $dotaz = $conn_mysql->query("SELECT level FROM leveling WHERE id = '".intval($id)."' ");
    $radku = $dotaz->num_rows;
  } catch (Exception $e) {
    die ("<h2 style=\"color: red; \">Check level Failed: Caught exception: " . $e->getMessage() . "\n" . "</h2></body></html>\n");
  }

  if ($radku==0)
  {
    return false; 
    // "Chyba: NELZE ZJISTIT LEVEL prvku! ";

    //exit;
  }

  while ($data = $dotaz->fetch_array())
  {
    $level_stranky=$data["level"];
  }

  if ( $user_level >= $level_stranky)
  {
    return true;
  }
}

function check_level2 ($user_level,$level_col)
{
  global $conn_mysql, $nick;
  try {
    $dotaz = $conn_mysql->query("SELECT ".$conn_mysql->real_escape_string($level_col)." FROM users WHERE login = '".$conn_mysql->real_escape_string($nick)."'");
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
