<?php

 //$pole3 .= "<br>";
 
 $pole2 .= "[id_stb]=> ".$update_id.",";
 
 $pole2 .= " diferencialni data: ";
 
     //novy zpusob archivovani dat
     foreach($pole_puvodni_data as $key => $val)
     {
      
      if( !($obj_upd[$key] == $val) )
      {
        if( !($key == "id_stb") )
        {
            if( $key == "ip_adresa" )
            {
              $pole3 .= "změna <b>IP adresy</b> z: ";
              $pole3 .= "<span class=\"az-s1\">".$val."</span> na: <span class=\"az-s2\">".$obj_upd[$key]."</span>";
              $pole3 .= ", ";
            } //konec key == ip
            elseif( $key == "mac_adresa" )
            {
              $pole3 .= "změna <b>MAC adresy</b> z: ";
              $pole3 .= "<span class=\"az-s1\">".$val."</span> na: <span class=\"az-s2\">".$obj_upd[$key]."</span>";
              $pole3 .= ", ";
            } //konec key == mac
            elseif( $key == "sw_port" )
            {
              $pole3 .= "změna <b>Čísla portu (ve switchi)</b> z: ";

              if( $val == "a"){ $pole3 .= "<span class=\"az-s1\">Ano</span> na: <span class=\"az-s2\">Ne</span>"; }
              elseif( $val == "n"){ $pole3 .= "<span class=\"az-s1\">Ne</span> na: <span class=\"az-s2\">Ano</span>"; }
              else{ $pole3 .= "<span class=\"az-s1\">".$val."</span> na: <span class=\"az-s2\">".$obj_upd[$key]."</span>"; }

              $pole3 .= ", ";
            } //konec key == sw_port
	    elseif($key == "id_nodu")
            {
              $pole3 .= "změna <b>Přípojného bodu</b> z: ";

              $vysl_t1=mysql_query("SELECT jmeno FROM nod_list WHERE id = '".intval($val)."'");
              while ($data_t1=mysql_fetch_array($vysl_t1) )
              { $pole3 .= "<span class=\"az-s1\">".$data_t1["jmeno"]."</span>"; }

              $pole3 .= " na: ";

              $val2 = $obj_upd[$key];

              $vysl_t2=mysql_query("select jmeno FROM nod_list WHERE id = '$val2'" );
              while ($data_t2=mysql_fetch_array($vysl_t2) )
              { $pole3 .= "<span class=\"az-s2\">".$data_t2["jmeno"]."</span>"; }

              $pole3 .= ", ";                                                                                                                 
            } // konec key == id_nodu
            elseif( $key == "id_tarifu" ){
              $pole3 .= "změna <b>Tarifu</b> z: ";

              $vysl_t1=mysql_query("SELECT jmeno_tarifu FROM tarify_iptv WHERE id_tarifu = '".intval($val)."'");
              while ($data_t1=mysql_fetch_array($vysl_t1) )
              { $pole3 .= "<span class=\"az-s1\">".$data_t1["jmeno_tarifu"]."</span>"; }

              $pole3 .= " na: ";

              $val2 = $obj_upd[$key];

              $vysl_t2 = mysql_query("SELECT jmeno_tarifu FROM tarify_iptv WHERE id_tarifu = '".intval($val2)."'");
              while ($data_t2=mysql_fetch_array($vysl_t2) )
              { $pole3 .= "<span class=\"az-s2\">".$data_t2["jmeno_tarifu"]."</span>"; }

              $pole3 .= ", ";                                                                                                                 
            
            } //konec key == id_tarifu
            else
            { // ostatni mody, nerozpoznane
              $pole3 .= "změna pole: <b>".$key."</b> z: <span class=\"az-s1\" >".$val."</span> ";
              $pole3 .= "na: <span class=\"az-s2\">".$obj_upd[$key]."</span>, ";
            }

         } //konec if nejde-li od id_komplu ( to v tom poli neni )
       } // konec if obj == val
     } // konec foreach
			   
   $pole2 .= "".$pole3;

   //automaticke ovezovani
   
   if( 
	(preg_match("/.*změna.*Přípojného.*bodu.*/", $pole3) > 0) 
	or
	(preg_match("/.*změna.*MAC.*adresy.*/", $pole3) > 0)
        or
        (preg_match("/.*změna.*Čísla.*portu.*/", $pole3) > 0)
     ){

	Aglobal::work_handler("4"); //rh-fiber - radius
	Aglobal::work_handler("7"); //trinity - sw.h3c.vlan.set.pl update
	Aglobal::work_handler("21"); //artemis - radius (tunel. verejky, optika)
   
   }
   
   //manualni restart - upozorneni :)
   
   if(preg_match("/.*změna.*Tarifu.*/", $pole3) > 0)
   {
   
	$p_link1 = "http://app01.cho01.iptv.grapesc.cz:9080/admin/admin/provisioning/stb-search.html?".
	            "searchText=".urlencode($pole_puvodni_data["mac_adresa"])."&amp;type=".urlencode("MAC_ADDRESS")."&amp;submit=OK";
	                                  
	echo "<div style=\"color: #ff4500; font-weight: bold; font-size: 18px;\" >".
		"<b>Pozor!</b> Změnu tarifu je třeba také provést v IPTV portálu. ".
		"<a href=\"".$p_link1."\" target=\"_new\" >".
		    "odkaz".
		"</a>";
		
	echo "<span style=\"padding-left: 25px; \" >
	           <a href=\"admin-login-iptv.php\" target=\"_new\" >aktivace funkcí IPTV portálu (přihlašení)</a>
	      </span>";
		                      
	 echo "</div>";
	     
	
   }

   
   if( $res == 1){ $vysledek_write="1"; }
   
   $add=mysql_query("INSERT INTO archiv_zmen (akce,provedeno_kym,vysledek) VALUES ('$pole2','$nick','$vysledek_write')");
 
?>
