<?php

$mssql_host = "mssql";
$mssql_user = "SA";
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
							"LoginTimeout" => 5,
							"Encrypt" => "No"
							);

	// https://www.php.net/manual/en/ref.pdo-sqlsrv.connection.php#refsect1-ref.pdo-sqlsrv.connection-examples
	$mssqlDSN = "sqlsrv:"
				. "Server=" . $mssql_host . ";"
				. "Database=" . $mssql_db . ";"
				. "Encrypt=No" . ";"
				;

	// first we try PDO, because there is working error printing
	try {
		$mssqlConn = new PDO($mssqlDSN, $mssql_user, $mssql_pass, array("LoginTimeout" => 5));
	
		$mssqlConn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	
		$mssqlQ = $mssqlConn->query('SELECT @@VERSION');
		
		// echo 'MSSQL VERSION: ' . $mssqlQ->fetchColumn() . '<br>';

		$logger->info("config\MSSQL: PDO created. version: " . var_export($mssqlQ->fetchColumn(), true));
	} catch (Exception $e) {
		// Error message and terminate the script
		print_r($e->getMessage()."<br>\n");

		// TODO: povolit toto, az bude funkcni MSSQL
		// if( !($db_mssql_no_exit == 1) )
		// { exit(); }
	}

	// and now we're done; close it
	$mssqlQ = null;
	$mssqlConn = null;

	// init "classic" connection
	try {
		$mssql_spojeni = sqlsrv_connect($mssql_host, $mssqlConnectionInfo);
		$logger->info("config\sqlsrv_connect: connect to host \"" . $mssql_host . "\" OK.");
	} catch(Exception $e) {
		print_r($e->getMessage());
	}
	
    if($mssql_spojeni === false) {
		echo "\nERROR: mssql_connect (host: ".$mssql_host.", db: " . $mssql_db. ") failed <br>\n";
		print_r( sqlsrv_errors(), true);

		if( !($db_mssql_no_exit == 1) )
		{ exit(); }
    }
}
