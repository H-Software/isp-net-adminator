<?php

class Aglobal
{
    function restart_mon2()
    {
	//obsolete
    }
    
    function synchro_router_list()
    {
	global $nick;
	
	//pro duplikaci tabulky router_list do Postgre DB
    
	//muster::
	//mysqldump --user=backup -x --add-drop-table -nt --skip-opt --compatible=postgresql adminator2 router_list 

        $output .= "----- postgre synchro ---- \n";
		
        exec("mysqldump --user=backup -x --add-drop-table -nt --default-character-set=utf8 --skip-opt --compatible=postgresql adminator2 router_list ", $mysql_export);

        //konverze z pole do jedné promenne
        foreach ($mysql_export as $key => $val) {
            if( ereg("^INSERT.",$val) )
            { $mysql_export_all .= $val; }
        }
	
        $pg_enc = pg_query("set client_encoding to 'UTF8';");

        $pg_drop = pg_query("DELETE FROM router_list");

        if($pg_drop){ $output .= "  postgre - tabulka router_list úspěšně vymazána.\n"; }
        else
        { $output .= "  postgre - chyba pri vymazani router_list. ".pg_last_error()."\n"; }

        $pg_import = pg_query($mysql_export_all);

        if($pg_import){ $output .= "  postgre - data router_list importována. \n"; }
        else
        { $output .= "  postgre - chyba pri importu router_list. ".pg_last_error()."\n"; }

        $output .= "----------\n";
    
	return $output;
    }
    
    function work_handler($item_id)
    {
	global $nick;
	
	//item_id - cislo ktery odpovida vzdy nejaky akci :)
	
	//seznam cisel a akcí
	// 1 - osvezeni net-n/sikany na reinhard-3 
	// zbytek viz databáze
	
	$item_id = intval($item_id);	
    
	$count = mysql_result(mysql_query("SELECT COUNT(*) FROM workitems WHERE (number_request = '$item_id' AND in_progress = '0') "), 0);
	
	$item_name = mysql_result(mysql_query("SELECT name FROM workitems_names WHERE id = '$item_id' "), 0, 0);
	
	if($count > 1)
	{ echo "<div> WARNING: Požadavek na restart \"".$item_name."\" (No. ".$item_id.") nalezen vícekrát. </div>\n"; }
	
	if($count == 1)
	{ 
	    echo "<div> <span style=\"color: #1e90ff; \">INFO: </span>".
		 "Požadavak na restart <b>\"".$item_name."\"</b> (No. ".$item_id.") ".
		 "<span style=\"color: #1e90ff;\">není potřeba přidávat, již se nachází ve frontě restart. subsystému. </div>\n"; 
	}
	else
	{
	    //polozka na seznamu restart. subsystému není, tj. pridame
	    
	    $add = mysql_query ("INSERT INTO workitems (number_request) VALUES ('".intval($item_id)."') ");
	
	    if( $add == 1){ $rs_write="1"; }
	    else{ $rs_write="0"; }
	    
	    $akce_az = "<b>akce:</b> požadavek na restart;<br>[<b>item_id</b>] => ".$item_id;
	    $akce_az .= ", [<b>item_name</b>] => ".$item_name;
	    
	    $sql_az = "INSERT INTO archiv_zmen (akce,provedeno_kym,vysledek) VALUES ".
		      "('".mysql_real_escape_string($akce_az)."','".mysql_real_escape_string($nick)."','".intval($rs_write)."')";
		      
	    $add_az = mysql_query($sql_az);
	
	    echo "<div style=\"\">Požadavek na restart <b>\"".$item_name."\"</b> (No. ".$item_id.") - ";
	     
	    if($add)
	    { echo "<span style=\"color: green;\"> úspěšně přidán do fronty</span>"; }
	    else
	    { echo "<span style=\"color: red;\"> chyba při přidání požadavku do fronty</span>"; }
	
	    if($add_az)
	    { echo " - <span style=\"color: green;\"> úspěšně přidán do archivu změn.</span>"; }
	    else
	    { 
		echo " - <span style=\"color: red;\"> chyba při přidání požadavku do archivu změn.</span>"; 
		echo "</div><div> chyba: ".mysql_errno()." : ".mysql_error()."\n";
		echo "</div><div> sql: ".$sql_az."\n";
	    }
	    
	    echo "</div>";
	}    
        
    } //end of function work_handler
    
    
    function find_reinhard($id)
    {
	$id = intval($id);
	
	$rs_objekt = pg_query("SELECT id_nodu FROM objekty WHERE id_komplu = '$id' ");
    
	if( (pg_num_rows($rs_objekt) == 1) )
	{
	    while($data = pg_fetch_array($rs_objekt) )
	    { $id_nodu = $data["id_nodu"]; }
	}
	else
	{ $id_nodu = 0; /* chyba :)*/ }
	
	$rs_nod = mysql_query("SELECT router_id FROM nod_list WHERE id = '$id_nodu' ");
	
	while($data2 = mysql_fetch_array($rs_nod))
	{ $router_id = $data2["router_id"]; }
		
	$reinhard_id = Aglobal::find_parent_reinhard($router_id);
	
	return $reinhard_id;
	    
    } //end of function find_reinhard
    
    function find_parent_reinhard($router_id)
    {
	$router_id = intval($router_id);
	
	$rs_router = mysql_query("SELECT nazev, parent_router FROM router_list WHERE id = '$router_id' ");
	
	if( mysql_num_rows($rs_router) == 1 )
	{
	    while($data = mysql_fetch_array($rs_router))
	    { 
		$r_nazev = $data["nazev"]; 
		$r_parent = $data["parent_router"];
	    }
	}
	else
	{ return 0; /* chyba :) */ }
	
	if( ereg("^reinhard*",$r_nazev) )
	{ 
	    //mame reinharda... vracime jeho ID
	    return $router_id; 
	}
	else
	{
	    if( $r_parent == 0)
	    { return 1; }
	    else
	    { 
		$rs = Aglobal::find_parent_reinhard($r_parent); 
		
		return $rs;
	    }
	}
	
    } //end of function find_parent_reinhard

    /** Kontrola e-mailové adresy
    * @param string e-mailová adresa
    * @return bool syntaktická správnost adresy
    * @copyright Jakub Vrána, http://php.vrana.cz/
    */
    
    // function check_email($email) {
	// 	$atom = '[-a-z0-9!#$%&\'*+/=?^_`{|}~]'; // znaky tvořící uživatelské jméno
	// 	$domain = '[a-z0-9]([-a-z0-9]{0,61}[a-z0-9])'; // jedna komponenta domény
	// 	return preg_match("/^$atom+(\\.$atom+)*@($domain?\\.)+$domain\$/i", $email);
    // }

	/* 
	*	check email by w3s
	*
	*   https://www.w3schools.com/php/php_form_url_email.asp
	*/
	public static function check_email($email) {
		return filter_var($email, FILTER_VALIDATE_EMAIL);
	}

    public static function pg_last_inserted_id($con, $table){ 
         
         //make the initial query 
         $sql = "SELECT * FROM " . $table; 
         //execute 
         $ret = pg_query($con, $sql); 
         //get the field name 
         $campoId = pg_field_name($ret, 0); 
         
         //change the query, using currval() 
         $sql = "SELECT currval('".$table."_".$campoId."_seq')"; 
         
         //exec 
         $retorno =pg_query($con, $sql); 
         
         if(pg_num_rows($ret)>0){ 
             //array 
             $s_dados = pg_fetch_all($retorno); 
             
             //vars 
             extract($s_dados[0],EXTR_OVERWRITE); 
             
             return $currval; 
             
         } else { 
             //case error, returns false 
             return false; 
         }
         
    } //end of function pg_last_inserted_id
    
    function create_link_to_owner($owner_id) {
    
	$owner_id = intval($owner_id);
	
	$vlastnik_dotaz=pg_query("SELECT firma, archiv FROM vlastnici WHERE id_cloveka = '".$owner_id."' ");
	$vlastnik_radku=pg_num_rows($vlastnik_dotaz);
	      
	while($data_vlastnik=pg_fetch_array($vlastnik_dotaz))
	{ 
	    $firma_vlastnik=$data_vlastnik["firma"]; 
	    $archiv_vlastnik=$data_vlastnik["archiv"]; 
	}
    
	if( $vlastnik_radku <= 0){
	    return false;
	}
	
	if ( $archiv_vlastnik == 1)
	{ $odkaz = "<a href=\"vlastnici-archiv.php?".urlencode("find_id")."=".urlencode($owner_id)."\" >".$owner_id."</a>\n"; }
	else
	{ $odkaz = "<a href=\"vlastnici2.php?".urlencode("find_id")."=".urlencode($owner_id)."\" >".$owner_id."</a>\n"; }

	return $odkaz;
	
    } //end of function create_link_to_owner
    
    function test_snmp_function()
    {
    
    	$ret_array = array();

        $ret_array[0] = true;
        
	if( !(function_exists('snmpget')) ) {

    	    $ret_array[0] = false;
	    $ret_array[1] = "Chyba! Neexistuje funkce \"snmpget\"!";
	    
        }
    
	if( !(function_exists('snmpwalk')) ) {

    	    $ret_array[0] = false;
	    $ret_array[1] = "Chyba! Neexistuje funkce \"snmpwalk\"!";
	    
        }
            
        return $ret_array;
	        
    } //konec funkce test_snmp_function
                
                    
    function test_router_for_monitoring($router_id){
    
	// autoloaded, probably :)
	// require_once("include/routeros.class.php");
		
	$ret_array = array();
        
        //default hodnoty, ktere se pripadne prepisou..
//        $ret_array[0] = true;
//	$ret_array[1] = "Všechny testy v pořádku! \n";

	$router_id = intval($router_id);
	
	$rs_q = mysql_query("SELECT ip_adresa, id FROM router_list WHERE id = '".$router_id."'");
	$rs_q_num = mysql_num_rows($rs_q);
	
	if( $rs_q_num <> 1){
	    
    	    $ret_array[0] = false;
	    $ret_array[1] .= "Chyba! Nelze najít router dle předaných parametrů (id: ".$router_id.") \n";

    	    return $ret_array;
	
	}
	
	$router_ip = mysql_result($rs_q, 0, 0);
	
	$rs_login = mysql_query("SELECT value FROM settings WHERE name IN ('routeros_api_login_name', 'routeros_api_login_password') ");
	
	$login_name = mysql_result($rs_login, 0, 0);
	$login_pass = mysql_result($rs_login, 1, 0);
	
	//
	// test pingu
	//
	
	exec("scripts/ping.sh ".$router_ip, $ping_output, $ping_ret);

	if( !($ping_output[0] > 0) )
	{
	    //  NENI ODEZVA NA PING
	
	    $ret_array[0] = false;
	    $ret_array[1] = "Chyba! Router neodpovídá na odezvu Ping (id: ".$router_id.", ping: ".$ping_output[0].")";
	
	    return $ret_array;
	
	}
	
	//
	// test API
	//
	$API = new RouterOS();
	
	//pokus o spojeni krz API
	$conn = $API->connect($router_ip, $login_name, $login_pass);
	
	if($conn == false){
	
	    $ret_array[0] = false;
	    $ret_array[1] .= "Chyba! Nelze se spojit s routerem krz API. (ROS_API say: couldn't connect to router) \n";
	
	    return $ret_array;
	
	}
	
	//
	// test SNMP
	//
	
	//test zda máme k dispozici SNMP funkce v PHP
	
	$rs_snmp_f = $this->test_snmp_function();
	
	if($rs_snmp_f[0] === false){
	
	    $ret_array[0] = false;
	    $ret_array[1] .= "Chyba! ".$rs_snmp_f[1]."\n";
	
	    return $ret_array;
	
	}
	
	$rs_snmp = snmpget($router_ip, "public", ".1.3.6.1.2.1.25.3.3.1.2.1", 300000);
	
	if($rs_snmp === false){
	
	    $ret_array[0] = false;
	    $ret_array[1] .= "Chyba! Router korektne neodpovídá na SNMP GET dotaz. (".$rs_snmp.") \n";
	
	    return $ret_array;
	
	}
	          
//debug result	
/*
	$ret_array[0] = false;
	$ret_array[1] = " generic error, (router_id: ".$router_id.", router_id: ".$router_ip." ";	

	$ret_array[1] .=  " INFO: Ping: Average: ".$ping_avg."ms, Packetloss: ".$ping_packetloss."% ";

//	$ret_array[1] .=  "\n INFO: SNMP GET load: ".$rs_snmp." \n";

//	$ret_array[1] .= " login_name: ".$login_name.", login_pass: ".$login_pass."";
	$ret_array[1] .= ")";
*/
//end of debug result
	
	
        $ret_array[0] = true;
	$ret_array[1] = "Všechny testy v pořádku! \n";
	
        //final return...
        return $ret_array;
		
    } //end of function test_router_for_monitoring
    
	
} //konec tridy Aglobal
