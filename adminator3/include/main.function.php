<?php

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
 
  global $conn_mysql;
  $ret = array();
 
// TODO: doresit
//  $dotaz_fn=mysql_query("SELECT * FROM faktury_neuhrazene ");
//  $dotaz_fn_radku=mysql_num_rows($dotaz_fn);
 
//  $ret[0] = $dotaz_fn_radku;
 
//  $dotaz_fn4=mysql_query("SELECT * FROM faktury_neuhrazene WHERE ( ignorovat = '1' ) order by id");
//  $dotaz_fn4_radku=mysql_num_rows($dotaz_fn4);
   
//  $ret[1] = $dotaz_fn4_radku;
 
//  $dotaz_fn2=mysql_query("SELECT * FROM faktury_neuhrazene WHERE par_id_vlastnika = '0' ");
//  $dotaz_fn2_radku=mysql_num_rows($dotaz_fn2);

//  $ret[2] = $dotaz_fn2_radku;
      
//  $dotaz_fn3=mysql_query("SELECT datum,DATE_FORMAT(datum, '%d.%m.%Y %H:%i:%s') as datum FROM fn_import_log order by id");
//  $dotaz_fn3_radku=mysql_num_rows($dotaz_fn3);
       
//  while( $data3=mysql_fetch_array($dotaz_fn3) )
//  { $datum_fn3=$data3["datum"]; }
	 
//  $ret[3] = $datum_fn3;

 return $ret;
}
