<?php

function check_level ($user_level,$id)
{

// co mame
// v promeny level mame level prihlaseneho uzivatele
// databazi levelu pro jednotlivy stranky

// co chceme
// porovnat level uzivatele s prislusnym levelem 
// stranky podle jejiho id

 $dotaz = mysql_query("SELECT level FROM leveling WHERE id = '".intval($id)."' ");

 $radku=mysql_num_rows($dotaz);

 if ($radku==0)
 {
  return false; 
  // "Chyba: NELZE ZJISTIT LEVEL prvku! ";

  //exit;
 }

 while ($data = mysql_fetch_array($dotaz))
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
 global $nick;

 $dotaz = mysql_query("SELECT ".mysql_real_escape_string($level_col)." FROM users WHERE login = '".mysql_real_escape_string($nick)."'");
 $radku = mysql_num_rows($dotaz);

 if($radku <> 1)
 { return false; }

 while ($data = mysql_fetch_array($dotaz))
 { $level_col_db = $data["$level_col"]; }

 if( $level_col_db == 1)
 { return true; }

}


?>
