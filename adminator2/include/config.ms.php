<?php

$mssql_host = "mssql";
$mssql_user = "sa";
$mssql_pass = "Password123";

if(!isset($mssql_db)){
 
    //zjisteni ucetni jednotky / databaze
    if( $conn_mysql->connect_error === null){
    
		//zjisteni, zda se uctuje v "prechodnem obdobi"
		$mysql_q_ms1 = $conn_mysql->query("SELECT value FROM settings WHERE name LIKE 'pohoda_accounting_turn_year' ");
		$mysql_q_ms1_value = $mysql_q_ms1->data_seek(0);
		
		if( $mysql_q_ms1_value == 1){
			$mssql_db = "StwPh_26109824_".(date("Y")-1); 
		}
		else{
			$mssql_db = "StwPh_26109824_".date("Y"); 
		}
		
		//muzem provest 2cast kodu
		$mssql_db_ok = 1;        
    }
    else{
		echo " ERROR: myssql_connect: need mysql connection for load settings \n";
		if( !($db_mssql_no_exit == 1) ){ exit(); }	
    }
}
else{
    $mssql_db_ok = 1;
}


if($mssql_db_ok == 1)
{

    // if( !function_exists('mssql_connect') or !function_exists('mssql_select_db') or !function_exists('mssql_query'))
    if( !function_exists('sqlsrv_connect') or !function_exists('sqlsrv_query'))
	{
		echo "<div style=\"color; red; \">Error: Nejsou potrebne funkce pro MSSQL databazi!</div>\n";
	
		if( !($db_mssql_no_exit == 1) )
		{ exit(); }
    }

	$mssqlConnectionInfo = array( 
							"Database" => $mssql_db,
							"UID" => $mssql_user,
							"PWD" => $mssql_pass,
							"Driver" => "ODBC Driver 18 for SQL Server"
							);

	$mssqlDSN = "sqlsrv:"
				. "server=" . $mssql_db . ";"
				// . "Driver=ODBC Driver 18 for SQL Server;"
				;

	try {
		$mssql_spojeni = sqlsrv_connect($mssql_host, $mssqlConnectionInfo);
	} catch(Exception $e) {
		error_log("$e");
	}
	
    if($mssql_spojeni === false) {
		echo " ERROR: mssql_connect (host: ".$mssql_host.", db: " . $mssql_db. ") failed <br>\n";
		print_r( sqlsrv_errors(), true);

		// TODO: povolit toto, az bude funkcni MSSQL
		// if( !($db_mssql_no_exit == 1) )
		// { exit(); }
    }

	try {
		// Establish a connection to the SQL Server using PDO
		$conn = new PDO($mssqlDSN, $mssql_user, $mssql_pass, $mssqlConnectionInfo);
	
		// Set PDO attributes to enable error reporting and exceptions
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	
		// Execute a query to get the SQL Server version
		$q = $conn->query('SELECT @@VERSION');
		
		// Display the SQL Server version
		echo 'MSSQL VERSION: ' . $q->fetchColumn() . '<br>';
	} catch (Exception $e) {
		// Error message and terminate the script
		die(print_r($e->getMessage()));
	}

}
