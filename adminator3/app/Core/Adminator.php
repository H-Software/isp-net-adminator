<?php

namespace App\Core;

class adminator {
  var $conn_mysql;
  var $smarty;
  var $logger;
    
  public function __construct($conn_mysql, $smarty, $logger)
  {
    $this->conn_mysql = $conn_mysql;
    $this->smarty = $smarty;
    $this->logger = $logger;

    $this->logger->addInfo("adminator\__construct called");
  }

  public function getTarifIptvListForForm($show_zero_value = true)
  {

    $this->logger->addInfo("adminator\getTarifIptvListForForm called");

    if($show_zero_value === true)
    {
        $tarifs[0] = "Není vybráno";
    }

    $q = $this->conn_mysql->query("SELECT id_tarifu, jmeno_tarifu FROM tarify_iptv ORDER by jmeno_tarifu ASC");
  
    $num_rows = $q->num_rows;
    
    if($num_rows < 1)
    {
      $tarifs[0] =  "nelze zjistit / žádný tarif nenalezen";
      return $tarifs;
    }
    
    while( $data = $q->fetch_array())
    {
      $tarifs[$data['id_tarifu']] = $data["jmeno_tarifu"];    
    }

    return $tarifs;
  }

    function show_stats_faktury_neuhr()
    {
        //
        // vypis neuhrazenych faktur
        //
        // return hodnoty
        //
        // 0. neuhr. faktur celkem
        // 1. nf ignorovane
        // 2. nf nesparovane
        // 3. datum posl. importu
        
        $ret = array();

     try {
			$dotaz_fn = $this->conn_mysql->query("SELECT * FROM faktury_neuhrazene");
            $dotaz_fn_radku = $dotaz_fn->num_rows;
            $ret[0] = $dotaz_fn_radku;
		} catch (Exception $e) {
			die (init_helper_base_html("adminator3") . "<h2 style=\"color: red; \">Error: Database query failed! Caught exception: " . $e->getMessage() . "\n" . "</h2></body></html>\n");
		}

    try {
            $dotaz_fn4 = $this->conn_mysql->query("SELECT * FROM faktury_neuhrazene WHERE ( ignorovat = '1' ) order by id");
            $dotaz_fn4_radku = $dotaz_fn4->num_rows;
            $ret[1] = $dotaz_fn4_radku;
		} catch (Exception $e) {
			die (init_helper_base_html("adminator3") . "<h2 style=\"color: red; \">Error: Database query failed! Caught exception: " . $e->getMessage() . "\n" . "</h2></body></html>\n");
		}

    try {
            $dotaz_fn2 = $this->conn_mysql->query("SELECT * FROM faktury_neuhrazene WHERE par_id_vlastnika = '0' ");
            $dotaz_fn2_radku = $dotaz_fn2->num_rows;
            $ret[2] = $dotaz_fn2_radku;
		} catch (Exception $e) {
			die (init_helper_base_html("adminator3") . "<h2 style=\"color: red; \">Error: Database query failed! Caught exception: " . $e->getMessage() . "\n" . "</h2></body></html>\n");
		}

    try {
            $dotaz_fn3 = $this->conn_mysql->query("SELECT datum,DATE_FORMAT(datum, '%d.%m.%Y %H:%i:%s') as datum FROM fn_import_log order by id");
            $dotaz_fn3_radku = $dotaz_fn3->num_rows;
		} catch (Exception $e) {
			die (init_helper_base_html("adminator3") . "<h2 style=\"color: red; \">Error: Database query failed! Caught exception: " . $e->getMessage() . "\n" . "</h2></body></html>\n");
		}

         while( $data3=$dotaz_fn3->fetch_array() )
         { $datum_fn3=$data3["datum"]; }
        
         if(strlen($datum_fn3) > 0){
            $ret[3] = $datum_fn3;
         } else{
            $ret[3] = "Unknown";
         }
         
        return $ret;
    }
}