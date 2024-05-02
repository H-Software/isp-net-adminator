<?php

 $nod_upd["jmeno"] = $jmeno; 		$nod_upd["adresa"] = $adresa;
 $nod_upd["pozn"] = $pozn; 		$nod_upd["ip_rozsah"] = $ip_rozsah;
 $nod_upd["stav"] = $stav;		$nod_upd["typ_vysilace"] = $typ_vysilace;
 $nod_upd["router_id"] = $router_id;	$nod_upd["typ_nodu"] = $typ_nodu;

 $nod_upd["vlan_id"] = $vlan_id;	$nod_upd["filter_router_id"] = $filter_router_id;
 
 $nod_upd["device_type_id"] = $device_type_id;
 
 //$pole .= "<br> aktualni data: ";
 //foreach ($nod_upd as $key => $val) { $pole .= " [".$key."] => ".$val."\n"; }

 $pole3 .= "[id_nodu] => ".$id_new;
 $pole3 .= " diferencialni data: ";
 
     //novy zpusob archivovani dat
     foreach($pole_puvodni_data as $key => $val)
     {
       if( !($nod_upd[$key] == $val) )
       {
        if( $key == "pozn" )
        {
          $pole3 .= "změna <b>Poznámky</b> z: ";
          $pole3 .= "<span class=\"az-s1\">".$val."</span> na: <span class=\"az-s2\">".$nod_upd[$key]."</span>";
          $pole3 .= ", ";
        } //konec key == pozn
	elseif( $key == "typ_nodu" )
        {
          $pole3 .= "změna <b>Módu</b> z: "."<span class=\"az-s1\">";
          
	  if( $val == 0){ $pole3 .= "Nezvoleno"; }
	  elseif( $val == 1){ $pole3 .= "bezdrátová síť"; }
	  elseif( $val == 2){ $pole3 .= "optická síť"; }
	  
	  else{ $pole3 .= $val; }
	  
	  $pole3 .= "</span>";
	  $pole3 .= " na: <span class=\"az-s2\">";
	 
	  if( $nod_upd[$key] == 0 ){ $pole3 .= "Nezvoleno"; } 
	  elseif( $nod_upd[$key] == 1 ){ $pole3 .= "bezdrátová síť"; } 
	  elseif( $nod_upd[$key] == 2 ){ $pole3 .= "optická síť"; } 
	  else{ $pole3 .= $nod_upd[$key]; }
	 
	  $pole3 .= "</span>";
	  $pole3 .= ", ";
        } //konec key == typ_nodu
	elseif( $key == "typ_vysilace" )
        {
          $pole3 .= "změna <b>Typu vysílače</b> z: ";
	  
	  if( $val == 0 ){ $pole3 .= "<span class=\"az-s1\">Nezvoleno</span>"; } 
	  elseif( $val == 1 ){ $pole3 .= "<span class=\"az-s1\">Metallic</span>"; } 
	  elseif( $val == 2 ){ $pole3 .= "<span class=\"az-s1\">ap-2,4GHz-OMNI</span>"; } 
	  elseif( $val == 3 ){ $pole3 .= "<span class=\"az-s1\">ap-2,4Ghz-sektor</span>"; } 
	  elseif( $val == 4 ){ $pole3 .= "<span class=\"az-s1\">ap-2.4Ghz-smerovka</span>"; } 
	  elseif( $val == 5 ){ $pole3 .= "<span class=\"az-s1\">ap-5.8Ghz-OMNI</span>"; } 
	  elseif( $val == 6 ){ $pole3 .= "<span class=\"az-s1\">ap-5.8Ghz-sektor</span>"; } 
	  elseif( $val == 7 ){ $pole3 .= "<span class=\"az-s1\">ap-5.8Ghz-smerovka</span>"; } 
	  elseif( $val == 8 ){ $pole3 .= "<span class=\"az-s1\">jiné</span>"; } 
	  else{ $pole3 .= "<span class=\"az-s1\">".$val."</span>"; }
	  
          $pole3 .= " na: <span class=\"az-s2\">";
	  
	  if( $nod_upd[$key] == 0 ){ $pole3 .= "Nezvoleno"; } 
	  elseif( $nod_upd[$key] == 1 ){ $pole3 .= "Metallic"; } 
	  elseif( $nod_upd[$key] == 2 ){ $pole3 .= "ap-2,4GHz-OMNI"; } 
	  elseif( $nod_upd[$key] == 3 ){ $pole3 .= "ap-2,4Ghz-sektor"; } 
	  elseif( $nod_upd[$key] == 4 ){ $pole3 .= "ap-2.4Ghz-smerovka"; } 
	  elseif( $nod_upd[$key] == 5 ){ $pole3 .= "ap-5.8Ghz-OMNI"; } 
	  elseif( $nod_upd[$key] == 6 ){ $pole3 .= "ap-5.8Ghz-sektor"; } 
	  elseif( $nod_upd[$key] == 7 ){ $pole3 .= "ap-5.8Ghz-smerovka"; } 
	  elseif( $nod_upd[$key] == 8 ){ $pole3 .= "jiné"; } 
	  else{ $pole3 .= $nod_upd[$key]; }
	  
	  $pole3 .= "</span>";
          $pole3 .= ", ";
        } //konec key == typ_vysilace
	elseif( $key == "stav" )
        {
          $pole3 .= "změna <b>Stavu</b> z: ";
	  $pole3 .= "<span class=\"az-s1\">";
	  
	  if( $val == 0 ){ $pole3 .= "Není zvoleno"; }
	  elseif( $val == 1 ){ $pole3 .= "v pořádku "; }
	  elseif( $val == 2 ){ $pole3 .= "vytížen"; }
	  elseif( $val == 3 ){ $pole3 .= "přetížen"; }
	  else{ echo $val; }
			     
          $pole3 .= "</span> na: <span class=\"az-s2\">";
	
	  if( $nod_upd[$key] == 0 ){ $pole3 .= "Není zvoleno"; }
	  elseif( $nod_upd[$key] == 1 ){ $pole3 .= "v pořádku "; }
	  elseif( $nod_upd[$key] == 2 ){ $pole3 .= "vytížen"; }
	  elseif( $nod_upd[$key] == 3 ){ $pole3 .= "přetížen"; }
	  else{ echo $nod_upd[$key]; }
	  
	  $pole3 .= "</span>";
          $pole3 .= ", ";
        } //konec key == stav
	elseif( $key == "router_id" )
        {
          $pole3 .= "změna <b>Routeru</b> z: ";
          $pole3 .= "<span class=\"az-s1\">";
	  
	  $dotaz_router1 = mysql_query("SELECT * FROM router_list WHERE id = '".intval($val)."'");
	  if( (mysql_num_rows($dotaz_router1) == 1 ))
	  {
	    while( $data = mysql_fetch_array($dotaz_router1))
	    { $pole3 .= $data["nazev"]." (".$val.")"; }
	  }
	  else{ $pole3 .= $val; }
			  
	  $pole3 .= "</span> na: <span class=\"az-s2\">";
	
	  $nod_zmena = $nod_upd[$key];
	  $dotaz_router2 = mysql_query("SELECT * FROM router_list WHERE id = '".intval($nod_zmena)."'");
	  if( (mysql_num_rows($dotaz_router2) == 1 ))
	  {
	    while( $data = mysql_fetch_array($dotaz_router2))
	    { $pole3 .= $data["nazev"]." (".$nod_zmena.")"; }
	  }
	  else{ $pole3 .= $nod_zmena; }
	  
	  $pole3 .= "</span>";
          $pole3 .= ", ";
        } //konec key == router_id
	elseif( $key == "filter_router_id" )
	{

          $pole3 .= "změna <b>Routeru, kde se provádí filtrace</b> z: ";
          $pole3 .= "<span class=\"az-s1\">";
	  
	  $dotaz_router1 = mysql_query("SELECT nazev FROM router_list WHERE id = '".intval($val)."'");
	  if( (mysql_num_rows($dotaz_router1) == 1 ))
	  {
	    while( $data = mysql_fetch_array($dotaz_router1))
	    { $pole3 .= $data["nazev"]." (".$val.")"; }
	  }
	  else{ $pole3 .= $val; }
			  
	  $pole3 .= "</span> na: <span class=\"az-s2\">";
	
	  $nod_zmena = $nod_upd[$key];
	  $dotaz_router2 = mysql_query("SELECT nazev FROM router_list WHERE id = '".intval($nod_zmena)."'");
	  if( (mysql_num_rows($dotaz_router2) == 1 ))
	  {
	    while( $data = mysql_fetch_array($dotaz_router2))
	    { $pole3 .= $data["nazev"]." (".$nod_zmena.")"; }
	  }
	  else{ $pole3 .= $nod_zmena; }
	  
	  $pole3 .= "</span>";
          $pole3 .= ", ";
	
	}
	elseif( $key == "device_type_id" )
	{
	    
	    $pole3 .= "změna <b>koncového zařízení</b> z: ";
            $pole3 .= "<span class=\"az-s1\">";
	  
	    $rs_device_id = mysql_query("SELECT name FROM nod_device_type WHERE id = '".intval($val)."' ");
	    $pole3 .= mysql_result($rs_device_id, 0, 0)." (".intval($val).")";
	    
	    $pole3 .= "</span> na: <span class=\"az-s2\">";
	
	    $rs_device_id2 = mysql_query("SELECT name FROM nod_device_type WHERE id = '".intval($nod_upd[$key])."' ");
	    $pole3 .= mysql_result($rs_device_id2, 0, 0)." (".intval($nod_upd[$key]).")";
	
	    $pole3 .= "</span>";
            $pole3 .= ", ";
	    
	} // device_type_id
	else
        { // ostatni mody, nerozpoznane
          $pole3 .= "změna pole: <b>".$key."</b> z: <span class=\"az-s1\" >".$val."</span> ";
          $pole3 .= "na: <span class=\"az-s2\">".$nod_upd[$key]."</span>, ";
        } //konec else
       } // konec if key == val
       
     } // konec foreach

  $pole .= "".$pole3;

  if ( $uprava == 1){ $vysledek_write="1"; }
  $add=mysql_query("INSERT INTO archiv_zmen (akce,provedeno_kym,vysledek) VALUES ".
		    "('".$conn_mysql->real_escape_string($pole)."',".
		    "'".$conn_mysql->real_escape_string(\Cartalyst\Sentinel\Native\Facades\Sentinel::getUser()->email)."',".
		    "'".$conn_mysql->real_escape_string($vysledek_write)."') ");

?>
