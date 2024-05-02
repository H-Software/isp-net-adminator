<?php

class zmeny_ucetni {
    var $conn_mysql;

    //promene pro pridani
    var $send;
    var $typ;
    var $text;
    var $odeslano;
    
    var $fail;
    var $error;
    var $info;
    
    var $writed;
    
    var $loggedUserEmail = "";

    function __construct($conn_mysql, $logger, $auth) {
      $this->conn_mysql = $conn_mysql;
      $this->logger = $logger;

      
      $this->loggedUserEmail = $i['username'];
    }

    function load_sql_result() {

      $rs_main = array();

      $sql = "SELECT az_ucetni.zu_id , az_ucetni.zu_typ, az_ucetni.zu_text, az_ucetni.zu_akceptovano, ";
      $sql .= "az_ucetni.zu_akceptovano_kdy, az_ucetni.zu_akceptovano_kym, az_ucetni.zu_akceptovano_pozn, ";
      $sql .= "DATE_FORMAT(az_ucetni.zu_akceptovano_kdy, '%d.%m.%Y %H:%i') zu_akceptovano_kdy2, ";
      $sql .= " az_ucetni.zu_vlozeno_kdy, DATE_FORMAT(az_ucetni.zu_vlozeno_kdy, '%d.%m.%Y %H:%i') zu_vlozeno_kdy2, zu_vlozeno_kym, ";
      $sql .= " az_ucetni_typy.zu_nazev_typ AS typ_nazev ";
      
      $sql .= " FROM az_ucetni LEFT JOIN az_ucetni_typy ON az_ucetni.zu_typ = az_ucetni_typy.zu_id_typ ORDER BY zu_id DESC";
      
      try {
        $qu = $this->conn_mysql->query($sql);
      } catch (Exception $e) {
        // die (init_helper_base_html("adminator3") . "<h2 style=\"color: red; \">Error: Database query failed! Caught exception: " . $e->getMessage() . "\n" . "</h2></body></html>\n");
      }

      while( $rs = $qu->fetch_assoc() ) { 
          if( ( $rs["zu_vlozeno_kym"] == $this->loggedUserEmail ) and ($rs["zu_akceptovano"] == 0) )
          { 
            $uprava = "<a href=\"".$_SERVER["php_self"]."?action=update";
            $uprava .= "&id=".$rs["zu_id"]."\" >upravit</a>";
            
            $rs["uprava"] = $uprava; 
          }
          else
          { 
            $rs["uprava"] = "<span style=\"color: gray;\" >upravit</span>"; 
          }

          //globalni presunuti pole do pole :)
          $rs_main[] = $rs; 
      }
	    
	    return $rs_main;
	
    } //konec funkce load_sql_result
    
    function get_types() {
      $sql .= "SELECT zu_id_typ AS id,zu_nazev_typ AS nazev FROM az_ucetni_typy ";
      
      try {
        $qu = $this->conn_mysql->query($sql);
      } catch (Exception $e) {
        die (init_helper_base_html("adminator3") . "<h2 style=\"color: red; \">Error: Database query failed! Caught exception: " . $e->getMessage() . "\n" . "</h2></body></html>\n");
      }
	
      while( $rs = $qu->fetch_assoc() )
      { $rs_main[] = $rs; }
	
	    return $rs_main;
	
    } //konec funkce get_types

    function check_inserted_vars()
    {
      if( !(preg_match('/^([[:digit:]]+)$/',$this->typ)) )
      {
        $this->fail = true;
        $this->error .= "<div class=\"form-add-fail\" ><H4>Zadaný typ (".$this->typ." ) není ve  správném formátu!!!</H4></div>";
      }
						    
    } //konec funkce checK-inserted_vars
    
    function save_vars_to_db()
    {

      $this->logger->info("archivZmenUcetni\save_vars_to_db called");

      try {
        $add = $this->conn_mysql->query("INSERT INTO az_ucetni (zu_typ, zu_text, zu_vlozeno_kdy, zu_vlozeno_kym)
                                          VALUES ('" . $this->typ . "','" . $this->text . "',now(),'" . $this->loggedUserEmail . "') ");
      } catch (Exception $e) {
        $this->logger->info("archivZmenUcetni\save_vars_to_db exception: " .var_export($e->getMessage(), true));
        // die (init_helper_base_html("adminator3") . "<h2 style=\"color: red; \">Error: Database query failed! Caught exception: " . $e->getMessage() . "\n" . "</h2></body></html>\n");
      }
			
      // pridame to do archivu zmen
      $pole="<b>akce: pridani zmeny pro ucetni; </b><br>";
      $pole .= "[typ_id]=> ".$this->typ.", [text]=> ".$this->text."";
      
      if ( $add == 1){ $vysledek_write=1; }
      try {
        $add = $this->conn_mysql->query("INSERT INTO archiv_zmen (akce,provedeno_kym,vysledek) VALUES ('" . $pole . "','" . $this->loggedUserEmail . "','" . $vysledek_write . "')");
      } catch (Exception $e) {
        // die (init_helper_base_html("adminator3") . "<h2 style=\"color: red; \">Error: Database query failed! Caught exception: " . $e->getMessage() . "\n" . "</h2></body></html>\n");
      }
      
      $this->writed = "true";
	
      if($add == 1){ 
        return true; }
      else { 
        return false; }
	
    } //save_vars_to_db
    
}
