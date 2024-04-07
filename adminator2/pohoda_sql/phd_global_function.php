<?php

//
// global function
//


 function generate_fully_fin_index($id_vlastnika)
 {
    $sql_rows = "id, fakturacni, ucetni_index, archiv, billing_freq ";

    $dotaz_vlastnik = pg_query("
                                SELECT ".$sql_rows." FROM vlastnici LEFT JOIN fakturacni
                                ON vlastnici.fakturacni = fakturacni.id
                                WHERE id_cloveka = '$id_vlastnika' ");
    $dotaz_vlastnik_radku = pg_num_rows($dotaz_vlastnik);

    if($dotaz_vlastnik_radku <> 1)
    {
        echo "Chyba! Nelze zjistit informace o vlastnikovi! (".$dotaz_vlastnik_radku.")<br> E: ".
        pg_last_error($db_ok2);

        return false;
    }
    else
    {
        while($data=pg_fetch_array($dotaz_vlastnik))
        {
            $uc_index = $data["ucetni_index"];

	    if( $data["archiv"] == "1" )
            { //archivacni
                $ui_full  = "27VYŘ".sprintf("%04d", $uc_index);
            }
	    elseif(( ($data["billing_freq"] == 1) and ($data["fakturacni"] > 0) ) )
	    { // ctvrtletní fakturacni
	        $ui_full  = "37".sprintf("%05d", $uc_index);
	    }
            elseif( $data["billing_freq"] == 1 )
            { //ctvrtletni fakturace domaci
	        $ui_full = "47".sprintf("%05d", $uc_index);
	    }
            elseif( ($data["fakturacni"] > 0) )
            { //faturacni
                $ui_full  = "27".sprintf("%05d", $uc_index);
            }
            else
            {  //domaci uzivatel
                $ui_full  = "27DM".sprintf("%05d", $uc_index);
            }

        } //konec while

        return $ui_full;

    } //else if dotaz_vlastnik_radku

 } //end of function generate_fully_fin_index

?>
