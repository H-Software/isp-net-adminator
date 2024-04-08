<?php

#echo E_ALL & ~E_NOTICE & ~E_DEPRECATED;

/*
require_once("../include/config.php");

$rs = mysql_query("SELECT * FROM archiv_zmen WHERE id = '62616' ");
$num_rows = mysql_num_rows($rs);

//$row = mssql_fetch_array($version);

echo "radku: ".$num_rows." <br>\n";
*/

/*
$upd = mssql_query("UPDATE dbo.qSTWFA_upd SET VarSym = qstwfa_upd.Smlouva;");
	      
if($upd === false)
{
  //echo "ERROR update query <br>\n";
  echo("MSSQL error: ". mssql_get_last_message()."<br>\n");
  die();
}

$num_aff = mssql_rows_affected($mssql_spojeni);

echo "radku update affected: ".$num_aff." \n";
*/
/*

$query = mssql_query("SELECT count(*) FROM FA");
*/

?>