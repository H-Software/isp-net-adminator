<?php

 //$pole3 .= "<br>";
 
 $pole3 .= "[id_komplu]=> ".$update_id.",";
 
 $pole3 .= " diferencialni data: ";
 
     //novy zpusob archivovani dat
     foreach ($pole_puvodni_data as $key => $val)
     {
      if ( !($obj_upd[$key] == $val) )
      {
        if ( !($key == "id_komplu") )
        {
            if( $key == "ip" )
            {
              $pole3 .= "změna <b>IP adresy</b> z: ";
              $pole3 .= "<span class=\"az-s1\">".$val."</span> na: <span class=\"az-s2\">".$obj_upd[$key]."</span>";
              $pole3 .= ", ";
            } //konec key == ip
            elseif( $key == "mac" )
            {
              $pole3 .= "změna <b>MAC adresy</b> z: ";
              $pole3 .= "<span class=\"az-s1\">".$val."</span> na: <span class=\"az-s2\">".$obj_upd[$key]."</span>";
              $pole3 .= ", ";
            } //konec key == mac
	    elseif( $key == "dov_net" )
            {
              $pole3 .= "změna <b>Povolen Inet</b> z: ";

              if( $val == "a"){ $pole3 .= "<span class=\"az-s1\">Ano</span> na: <span class=\"az-s2\">Ne</span>"; }
              elseif( $val == "n"){ $pole3 .= "<span class=\"az-s1\">Ne</span> na: <span class=\"az-s2\">Ano</span>"; }
              else{ $pole3 .= "<span class=\"az-s1\">".$val."</span> na: <span class=\"az-s2\">".$obj_upd[$key]."</span>"; }

              $pole3 .= ", ";
	      
            } //konec key == dov_net
            elseif( $key == "id_nodu" )
            {
              $pole3 .= "změna <b>Připojného bodu</b> z: ";

                $vysl_t1=mysql_query("select jmeno from nod_list WHERE id = '$val'" );
                while ($data_t1=mysql_fetch_array($vysl_t1) )
                { $pole3 .= "<span class=\"az-s1\">".$data_t1["jmeno"]."</span>"; }

              $pole3 .= " na: ";

              $val2 = $obj_upd[$key];

                $vysl_t2=mysql_query("select jmeno from nod_list WHERE id = '$val2'" );
                while ($data_t2=mysql_fetch_array($vysl_t2) )
                { $pole3 .= "<span class=\"az-s2\">".$data_t2["jmeno"]."</span>"; }

              $pole3 .= ", ";                                                                                                                 
            } // konec key == id_nodu
            elseif( $key == "sikana_status" )
	    {
	      $pole3 .= "změna <b>Šikana</b> z: ";

              if( $val == "a"){ $pole3 .= "<span class=\"az-s1\">Ano</span> na: <span class=\"az-s2\">Ne</span>"; }
              elseif( $val == "n"){ $pole3 .= "<span class=\"az-s1\">Ne</span> na: <span class=\"az-s2\">Ano</span>"; }
              else{ $pole3 .= "<span class=\"az-s1\">".$val."</span> na: <span class=\"az-s2\">".$obj_upd[$key]."</span>"; }

              $pole3 .= ", ";
            
	      
	    } //konec sikana_status
	    elseif($key == "id_tarifu"){
 
        	$rs_tarif = mysql_query("SELECT jmeno_tarifu FROM tarify_int WHERE id_tarifu = '".intval($val)."' ");
                $tarif = mysql_result($rs_tarif,0, 0);
        	
        	$rs_tarif2 = mysql_query("SELECT jmeno_tarifu FROM tarify_int WHERE id_tarifu = '".intval($obj_upd[$key])."' ");
                $tarif2 = mysql_result($rs_tarif2,0, 0);
                
                $pole3 .= "změna <b>Tarifu</b> z: "."<span class=\"az-s1\">".$tarif."</span>";
                $pole3 .= " na: <span class=\"az-s2\">".$tarif2."</span>".", ";
        
                //$pole3 .= "změna pole: <b>".$key."</b> z: <span class=\"az-s1\" >".$val."</span> ";
                //$pole3 .= "na: <span class=\"az-s2\">".$obj_upd[$key]."</span>, ";
                                             
            } //konec elseif id_tarifu
    	    else
            { // ostatni mody, nerozpoznane
              $pole3 .= "změna pole: <b>".$key."</b> z: <span class=\"az-s1\" >".$val."</span> ";
              $pole3 .= "na: <span class=\"az-s2\">".$obj_upd[$key]."</span>, ";
            }

         } //konec if nejde li od id_komplu ( to v tom poli neni )
       } // konec if obj == val
     } // konec foreach
			   
    $pole2 .= "".$pole3;
				     
   if ( $res == 1){ $vysledek_write="1"; }  
   $add=mysql_query("INSERT INTO archiv_zmen (akce,provedeno_kym,vysledek) VALUES ('$pole2','$nick','$vysledek_write')");

 // 
 //pro osvezovani
 //
 
 //zjistit, krz kterého reinharda jde objekt
 $reinhard_id = Aglobal::find_reinhard($update_id);

 //zmena sikany
 if( ereg(".*změna.*Šikana.*z.*", $pole3) )
 {
   if($reinhard_id == 177){ Aglobal::work_handler("1"); } //reinhard-3 (ros) - restrictions (net-n/sikana)
   elseif($reinhard_id == 1){ Aglobal::work_handler("2"); } //reinhard-wifi (ros) - restrictions (net-n/sikana)
   elseif($reinhard_id == 236){ Aglobal::work_handler("24"); } //reinhard-5 (ros) - restrictions (net-n/sikana)
   else{
    
      //nenalezet pozadovany reinhard, takze osvezime vsechny
     
      Aglobal::work_handler("1"); //reinhard-3 (ros) - restrictions (net-n/sikana)   
      Aglobal::work_handler("2"); //reinhard-wifi (ros) - restrictions (net-n/sikana)
      Aglobal::work_handler("24"); //reinhard-5 (ros) - restrictions (net-n/sikana)
      
   }
   
 }

 //zmena NetN
 if( ereg(".*změna.*Povolen.*Inet.*z.*", $pole3) )
 {
   if($reinhard_id == 177){ Aglobal::work_handler("1"); } //reinhard-3 (ros) - restrictions (net-n/sikana)
   elseif($reinhard_id == 1){ Aglobal::work_handler("2"); } //reinhard-wifi (ros) - restrictions (net-n/sikana)
   elseif($reinhard_id == 236){ Aglobal::work_handler("24"); } //reinhard-5 (ros) - restrictions (net-n/sikana)
   else{
   
      //nenalezet pozadovany reinhard, takze osvezime vsechny
      
      Aglobal::work_handler("1"); //reinhard-3 (ros) - restrictions (net-n/sikana)   
      Aglobal::work_handler("2"); //reinhard-wifi (ros) - restrictions (net-n/sikana)
      Aglobal::work_handler("24"); //reinhard-5 (ros) - restrictions (net-n/sikana)
   
   }
 }

 //zmena IP adresy pokud je aktivni Sikana ci NetN
 if( ( 
       ereg(".*změna.*IP.*adresy.*z.*", $pole3) 
       and
       (
        ($pole_puvodni_data["sikana_status"] == "a")
	or
	($pole_puvodni_data["dov_net"] == "n")
       )
     )
   )
 {
      //radsi vynutit restart net-n/sikany u vseho
      
      Aglobal::work_handler("1"); //reinhard-3 (ros) - restrictions (net-n/sikana)   
      Aglobal::work_handler("2"); //reinhard-wifi (ros) - restrictions (net-n/sikana)
      Aglobal::work_handler("3"); //reinhard-fiber (linux) - iptables (net-n/sikana)
      Aglobal::work_handler("24"); //reinhard-5 (ros) - restrictions (net-n/sikana)

      Aglobal::work_handler("5");  //reinhard-fiber - shaper
      Aglobal::work_handler("13"); //reinhard-wifi (ros) - shaper (client's tariffs)
      Aglobal::work_handler("20"); //reinhard-3 (ros) - shaper (client's tariffs)
      Aglobal::work_handler("23"); //reinhard-5 (ros) - shaper (client's tariffs)
      
      Aglobal::work_handler("14"); //(trinity) filtrace-IP-on-Mtik's-restart
      
 }
 //zmena IP adresy bez aktivovaného omezení
 elseif( ereg(".*změna.*IP.*adresy.*z.*", $pole3) )
 {
      Aglobal::work_handler("5");  //reinhard-fiber - shaper
      Aglobal::work_handler("13"); //reinhard-wifi (ros) - shaper (client's tariffs)
      Aglobal::work_handler("20"); //reinhard-3 (ros) - shaper (client's tariffs)
      Aglobal::work_handler("23"); //reinhard-5 (ros) - shaper (client's tariffs)
      
      Aglobal::work_handler("14"); //(trinity) filtrace-IP-on-Mtik's-restart
  
 } 

 //zmena linky -- shaper / filtrace
 if( ereg(".*změna.*pole.*id_tarifu.*", $pole3) 
      or
     ereg(".*změna.*Tarifu.*", $pole3)
    )
 {
      if($reinhard_id == 177){ Aglobal::work_handler("20"); } //reinhard-3 (ros) - shaper (client's tariffs)      
      elseif($reinhard_id == 1){ Aglobal::work_handler("13"); } //reinhard-wifi (ros) - shaper (client's tariffs)
      elseif($reinhard_id == 236){ Aglobal::work_handler("23"); } //reinhard-5 (ros) - shaper (client's tariffs)
      else
      {
        Aglobal::work_handler("13"); //reinhard-wifi (ros) - shaper (client's tariffs)
        Aglobal::work_handler("20"); //reinhard-3 (ros) - shaper (client's tariffs)      
        Aglobal::work_handler("23"); //reinhard-5 (ros) - shaper (client's tariffs)
      }
      
      // filtrace asi neni treba
     // Aglobal::work_handler("14"); //(trinity) filtrace-IP-on-Mtik's-restart
 
 }
 
 //zmena tunneling_ip ci tunel záznamů  
   // --> radius artemis
   // zde dodelat zmenu IP adresy, pokud tunelovana verejka
 if( 
	ereg(".*změna.*pole.*tunnelling_ip.*", $pole3)
	or
	ereg(".*změna.*pole.*tunnel_user.*", $pole3) 
	or
	ereg(".*změna.*pole.*tunnel_pass.*", $pole3)
   )
 {
      Aglobal::work_handler("21"); //artemis - radius (tunel. verejky, optika)
 }
 
 //zmena MAC adresy .. zatim se nepouziva u wifi
 
 //zmena DNS záznamu, asi jen u veřejných IP adresa
 // --> restart DNS auth. serveru
 if( ereg(".*změna.*pole.*dns_jmeno.*", $pole3) )
 {
      Aglobal::work_handler("9"); //erik - dns-restart
      Aglobal::work_handler("10"); //trinity - dns restart
      Aglobal::work_handler("11"); //artemis - dns restart
      Aglobal::work_handler("12"); //c.ns.simelon.net - dns.restart
 }
 
 if( ereg(".*změna.*pole.*client_ap_ip.*", $pole3) ){
 
      Aglobal::work_handler("14"); //(trinity) filtrace-IP-on-Mtik's-restart
 
      if($reinhard_id == 177){ Aglobal::work_handler("20"); } //reinhard-3 (ros) - shaper (client's tariffs)      
      elseif($reinhard_id == 1){ Aglobal::work_handler("13"); } //reinhard-wifi (ros) - shaper (client's tariffs)
      elseif($reinhard_id == 236){ Aglobal::work_handler("23"); } //reinhard-5 (ros) - shaper (client's tariffs)
      else
      {
        Aglobal::work_handler("13"); //reinhard-wifi (ros) - shaper (client's tariffs)
        Aglobal::work_handler("20"); //reinhard-3 (ros) - shaper (client's tariffs)      
        Aglobal::work_handler("23"); //reinhard-5 (ros) - shaper (client's tariffs)
      }
 }
 
 //nic vic mi nenapada :-) 
 
 
?>
