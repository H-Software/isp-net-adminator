<?php

require_once("../include/config.ms.php");

$version = mssql_query("SELECT * FROM FA");
$num_rows = mssql_num_rows($version);

//$row = mssql_fetch_array($version);

echo "radku FA: ".$num_rows." <br>\n";

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

while( $data = mssql_fetch_array($query))
{
    echo "-- ".$data["count"]."--";
}
*/

?>