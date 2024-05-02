<?php

    $nod_upd["nazev"] = $nazev;
    $nod_upd["ip_adresa"] = $ip_adresa;
    $nod_upd["parent_router"] = $parent_router;
    $nod_upd["mac"] = $mac;
    $nod_upd["monitoring"] = $monitoring;
    $nod_upd["monitoring_cat"] = $monitoring_cat;
    $nod_upd["alarm"] = $alarm;
//    $nod_upd["device_type_id"] = $device_type_id;
 
    $nod_upd["filtrace"] = $filtrace;
    $nod_upd["id_nodu"] = $selected_nod;
    $nod_upd["poznamka"] = $poznamka;
 
 //$pole3 .= "[id_nodu] => ".$id_new;
 $pole3 .= " [id_routeru] => <a href=\"topology-router-list.php\">".$update_id."</a> diferencialni data: ";
 
     //novy zpusob archivovani dat
     foreach($pole_puvodni_data as $key => $val)
     {
       if( !($nod_upd[$key] == $val) )
       {
        if( $key == "parent_router" )
        {
          $pole3 .= "změna <b>Nadřazený router</b> z: ";
          $pole3 .= "<span class=\"az-s1\">";

          $dotaz_router1 = mysql_query("SELECT nazev FROM router_list WHERE id = '$val'");
          if( (mysql_num_rows($dotaz_router1) == 1 ))
          {
            while( $data = mysql_fetch_array($dotaz_router1))
            { $pole3 .= $data["nazev"]." (".$val.")"; }
          }
          else{ $pole3 .= $val; }

          $pole3 .= "</span> na: <span class=\"az-s2\">";

          $id = $nod_upd[$key];
          $dotaz_router2 = mysql_query("SELECT nazev FROM router_list WHERE id = '$id'");
          if( (mysql_num_rows($dotaz_router2) == 1 ))
          {
            while( $data = mysql_fetch_array($dotaz_router2))
            { $pole3 .= $data["nazev"]." (".$id.")"; }
          }
          else{ $pole3 .= $id; }
	  
	  $pole3 .= "</span>";
          $pole3 .= ", ";
        } //konec key == parent_router
	elseif( $key == "monitoring" )
        {
          $pole3 .= "změna <b>Monitorování</b> z: "."<span class=\"az-s1\">";
          
	  if( $val == 1){ $pole3 .= "Ano"; }
	  elseif( $val == 0){ $pole3 .= "Ne"; }
	  else{ $pole3 .= $val; }
	  
	  $pole3 .= "</span>";
	  $pole3 .= " na: <span class=\"az-s2\">";
	 
	  if( $nod_upd[$key] == 1 ){ $pole3 .= "Ano"; } 
	  elseif( $nod_upd[$key] == 0 ){ $pole3 .= "Ne"; } 
	  else{ $pole3 .= $nod_upd[$key]; }
	 
	  $pole3 .= "</span>";
	  $pole3 .= ", ";
        } //konec key == monitoring
	elseif( $key == "monitoring_cat" )
        {
          $pole3 .= "změna <b>Monitoring kategorie</b> z: "."<span class=\"az-s1\">".$val."</span>";
	  $pole3 .= " na: <span class=\"az-s2\">".$nod_upd[$key]."</span>, ";
        } //konec key == monitoring_cat
	elseif( $key == "alarm" )
        {
          $pole3 .= "změna <b>Alarmu</b> z: "."<span class=\"az-s1\">";
          
	  if( $val == 1){ $pole3 .= "Zapnuto"; }
	  elseif( $val == 0){ $pole3 .= "Vypnuto"; }
	  else{ $pole3 .= $val; }
	  
	  $pole3 .= "</span>";
	  $pole3 .= " na: <span class=\"az-s2\">";
	 
	  if( $nod_upd[$key] == 1 ){ $pole3 .= "Zapnuto"; } 
	  elseif( $nod_upd[$key] == 0 ){ $pole3 .= "Vypnuto"; } 
	  else{ $pole3 .= $nod_upd[$key]; }
	 
	  $pole3 .= "</span>";
	  $pole3 .= ", ";
        } //konec key == alarm	
	elseif( $key == "id_nodu" )
        {
          $pole3 .= "změna <b>Připojného bodu</b> z: ";

            $vysl_t1=mysql_query("select jmeno FROM nod_list WHERE id = '$val'" );
            while ($data_t1=mysql_fetch_array($vysl_t1) )
            { $pole3 .= "<span class=\"az-s1\">".$data_t1["jmeno"]."</span>"; }

          $pole3 .= " na: ";

          $val2 = $nod_upd[$key];

    	    $vysl_t2=mysql_query("SELECT jmeno FROM nod_list WHERE id = '$val2'");
            while ($data_t2=mysql_fetch_array($vysl_t2) )
            { $pole3 .= "<span class=\"az-s2\">".$data_t2["jmeno"]."</span>"; }

            $pole3 .= ", ";                                                                                                                
        } // konec key == id_nodu
	elseif( $key == "filtrace" )
        {
          $pole3 .= "změna <b>Filtrace</b> z: "."<span class=\"az-s1\">";
          
	  if( $val == 1){ $pole3 .= "Ano"; }
	  elseif( $val == 0){ $pole3 .= "Ne"; }
	  else{ $pole3 .= $val; }
	  
	  $pole3 .= "</span>";
	  $pole3 .= " na: <span class=\"az-s2\">";
	 
	  if( $nod_upd[$key] == 1 ){ $pole3 .= "Ano"; } 
	  elseif( $nod_upd[$key] == 0 ){ $pole3 .= "Ne"; } 
	  else{ $pole3 .= $nod_upd[$key]; }
	 
	  $pole3 .= "</span>";
	  $pole3 .= ", ";
        } //konec key == filtrace
	
	else 
        { // ostatni mody, nerozpoznane
          $pole3 .= "změna pole: <b>".$key."</b> z: <span class=\"az-s1\" >".$val."</span> ";
          $pole3 .= "na: <span class=\"az-s2\">".$nod_upd[$key]."</span>, ";
        } //konec else
       } // konec if key == val
     } // konec foreach

  if( ! ereg(".*změna.*", $pole3) )
  { $pole3 .= " <b>nebyly provedeny žádné změny</b> "; }
       
  $pole .= "".$pole3;
  
  if( $uprava == 1){ $vysledek_write="1"; }
  
  $add=mysql_query("INSERT INTO archiv_zmen (akce,provedeno_kym,vysledek) VALUES ".
		    "('".mysql_real_escape_string($pole)."',".
		    "'".mysql_real_escape_string(\Cartalyst\Sentinel\Native\Facades\Sentinel::getUser()->email)."',".
		    "'".mysql_real_escape_string($vysledek_write)."') ");
		    
?>
