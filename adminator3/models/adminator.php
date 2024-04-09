<?php

class adminator {
    var $conn_mysql;
    var $smarty;
    var $logger;
    var $auth;
    var $app;
    
    public function __construct($conn_mysql, $smarty, $logger, $auth)
    {
		$this->conn_mysql = $conn_mysql;
        $this->smarty = $smarty;
        $this->logger = $logger;
        $this->auth = $auth;
        
        $this->logger->addInfo("adminator\__construct called");
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
            
         $ret[3] = $datum_fn3;

        return $ret;
    }
}