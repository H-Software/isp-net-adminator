<?php

class vlastnik2
{

    function show_fakt_skupiny($fu_select)
    {
	$fu_sql_base = " SELECT * FROM fakturacni_skupiny ";
        
	if( $fu_select == 2)
	{ $fu_sql_select .= " WHERE typ = '2' "; } //Pouze FU
	if( $fu_select == 3 )
	{ $fu_sql_select .= " WHERE typ = '1' "; } //pouze DU
				     
	$dotaz_fakt_skup = mysql_query($fu_sql_base." ".$fu_sql_select." ORDER BY nazev DESC");
	
	while( $data_fs = mysql_fetch_array($dotaz_fakt_skup) )
	{
	    $fs[]= array( "id" => $data_fs["id"], "nazev" => $data_fs["nazev"], "typ" =>$data_fs["typ"] );
	}
	
	return $fs;
		
    } //konec funkce show_fakt_skupiny

} //konec tridy vlastnik2
