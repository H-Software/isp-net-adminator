<?php

$mssql_user = "adminator2";
$mssql_pass = "archimedes112";

if(!isset($mssql_db)){
 
    //zjisteni ucetni jednotky / databaze
    if($db_mysql_db_sl === true){
    
	//zjisteni, zda se uctuje v "prechodnem obdobi"
	$mysql_q_ms1 = mysql_query("SELECT value FROM settings WHERE name LIKE 'pohoda_accounting_turn_year' ");
	$mysql_q_ms1_value = mysql_result($mysql_q_ms1, 0);
	
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

    $mssql_host = "10.70.17.14:1433";

    if( !function_exists('mssql_connect') or !function_exists('mssql_select_db') or !function_exists('mssql_query'))
    {
	echo "Nejsou potrebne funkce pro MSSQL databazi! \n";
  
	if( !($db_mssql_no_exit == 1) ){ exit(); }
    }

    $mssql_spojeni = mssql_connect($mssql_host,$mssql_user,$mssql_pass);

    if(!$mssql_spojeni)
    {
	echo " ERROR: myssql_connect (host: ".$mssql_host.") <br>\n";
	echo ' MSSQL error: '.mssql_get_last_message()."<br>\n";
    
	if( !($db_mssql_no_exit == 1) ){ exit(); }
    }

    if($mssql_spojeni)
    {
	if(mssql_select_db($mssql_db,$mssql_spojeni))
	{ 
	    //asi vse OK :)
    
	    //mssql_query("SET NAMES 'utf-8'");
	    //mssql_query("SET CHARACTER SET utf-8");
	}
	else
	{ 
	    echo "Nejde zmenit databazi na ".$mssql_db."<br>\n";
	    echo "MSSQL error: ". mssql_get_last_message()."<br>\n"; 
	    if( !($db_mssql_no_exit == 1) ){ exit(); }
    
	}
    }
    else
    {
	echo "Nelze se pripojit k MS SQL serveru (".$mssql_host.") <br>\n";
   
	if( !($db_mssql_no_exit == 1) ){ exit(); }
    }

}

?>
