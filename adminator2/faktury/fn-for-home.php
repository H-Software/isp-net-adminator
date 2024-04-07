<?php

$table_faktury_neuhrazene = "faktury_neuhrazene";

echo "<div style=\"width: 800px; position: relative; margin-left: auto;
	margin-right: auto; padding-bottom: 30px; \">";

$db_mssql_no_exit = 1;

require_once($cesta."include/config.ms.php");
	
echo "<div style=\"font-weight: bold; font-size: 17px; padding-bottom: 10px; \">
	Informace z modulu <a href=\"fn.php\" >\"Neuhrazené faktury\"</a></div> 
    <div style=\"padding-left: 5px; \">
	<span style=\"color: #555555; \">Celkový počet neuhrazených faktur:</span> ";

    // TODO: fix this
    
    // $dotaz_fn=mysql_query("SELECT * FROM ".$table_faktury_neuhrazene." ");
    // $dotaz_fn_radku=mysql_num_rows($dotaz_fn);
    
    // $dotaz_fn4=mysql_query("SELECT * FROM ".$table_faktury_neuhrazene." WHERE ( ignorovat = '1' ) order by id");
    // $dotaz_fn4_radku=mysql_num_rows($dotaz_fn4);
    
    // echo " <span style=\"font-weight: bold; \">".$dotaz_fn_radku."</span> ";
    // echo "<span style=\"color: grey; \">( z toho ignorovaných: ";
    // echo "<span style=\"font-weight: bold; color: black; \">".$dotaz_fn4_radku."</span> )</span></div>";

    // $dotaz_fn2=mysql_query("SELECT * FROM ".$table_faktury_neuhrazene." WHERE par_id_vlastnika = '0' ");
    // $dotaz_fn2_radku=mysql_num_rows($dotaz_fn2);
    
    // echo "<div style=\"padding-left: 5px; \">
	// <span style=\"color: #555555; \">Počet nespárovaných neuh. faktur:</span>";
    
    // echo " <span style=\"font-weight: bold; \">".$dotaz_fn2_radku."</span></div>";
    
    // $dotaz_fn3=mysql_query("SELECT datum,DATE_FORMAT(datum, '%d.%m.%Y %H:%i:%s') as datum FROM fn_import_log order by id");
    // $dotaz_fn3_radku=mysql_num_rows($dotaz_fn3);
    
    // while( $data3=mysql_fetch_array($dotaz_fn3) )
    // { $datum_fn3=$data3["datum"]; }
    
    // echo "<div style=\"padding-left: 5px; \">".
	//  "<span style=\"color: #555555; \">Datum a čas poslední synchronizace neuhr. faktur: </span>". 
	//  "<span style=\"font-weight: bold; \">".$datum_fn3."</span>".
	//  "<span style=\"color: #555555; padding-left: 34px;\">// Pohoda SQL -> Adminátor</span>". 
	// "</div>";
    
    // $dotaz_pohoda_banka = mssql_query("SELECT TOP 1 Datum, convert(char, Datum, 104) AS DatumCZ FROM BV ORDER BY DatPlat DESC");
    
    // echo "<div style=\"padding-left: 5px; \">
	//     <span style=\"color: #555555; \">Datum a čas posledního importu plateb z banky: ".$xxx." </span>
	//     <span style=\"font-weight: bold;\" >".mssql_result($dotaz_pohoda_banka, '0', 'DatumCZ')."</span>
	//     <span style=\"color: #555555; padding-left: 100px;\">// načteno z Pohoda SQL</span>

	//   </div>";
    
    // echo "</div>";

?>