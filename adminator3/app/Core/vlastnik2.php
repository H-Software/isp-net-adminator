<?php

class vlastnik2 {
	var $conn_mysql;

	function __construct($conn_mysql) {
		$this->conn_mysql = $conn_mysql;
	}

	function show_fakt_skupiny($fu_select) {
		$fu_sql_base = " SELECT * FROM fakturacni_skupiny ";
			
		if( $fu_select == 2)
		{ $fu_sql_select .= " WHERE typ = '2' "; } //Pouze FU
		if( $fu_select == 3 )
		{ $fu_sql_select .= " WHERE typ = '1' "; } //pouze DU
		
		try {
			$dotaz_fakt_skup = $this->conn_mysql->query($fu_sql_base." ".$fu_sql_select." ORDER BY nazev DESC");
		} catch (Exception $e) {
			die ("<h2 style=\"color: red; \">Login Failed (check login): Caught exception: " . $e->getMessage() . "\n" . "</h2></body></html>\n");
		}

		while( $data_fs = $dotaz_fakt_skup->fetch_array()){
			$fs[]= array( "id" => $data_fs["id"], "nazev" => $data_fs["nazev"], "typ" =>$data_fs["typ"] );
		}

		return $fs;
		
	} //konec funkce show_fakt_skupiny

} //konec tridy vlastnik2
