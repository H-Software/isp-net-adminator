<?php
 
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
            elseif( $key == "port_id" )
            {
              $pole3 .= "změna <b>Číslo sw. portu</b> z: ";
	      $pole3 .= "<span class=\"az-s1\">".$val."</span> na: <span class=\"az-s2\">".$obj_upd[$key]."</span>";
              $pole3 .= ", ";
            } //konec key == vezeni
	    elseif( $key == "dov_net" )
            {
              $pole3 .= "změna <b>Povolen Inet</b> z: ";

              if( $val == "a"){ $pole3 .= "<span class=\"az-s1\">Ano</span> na: <span class=\"az-s2\">Ne</span>"; }
              elseif( $val == "n"){ $pole3 .= "<span class=\"az-s1\">Ne</span> na: <span class=\"az-s2\">Ano</span>"; }
              else{ $pole3 .= "<span class=\"az-s1\">".$val."</span> na: <span class=\"az-s2\">".$obj_upd[$key]."</span>"; }

              $pole3 .= ", ";
            } //konec key == dov_net
	    elseif( $key == "verejna")
            {

            } // konec key == verejna
	    elseif( $key == "typ_ip")
            {
	      $pole3 .= "změna <b>Typ IP adresy</b> z: ";

              if( $val == "1"){ $pole3 .= "<span class=\"az-s1\">Neveřejná</span> na: <span class=\"az-s2\">Veřejná</span>"; }
              elseif( $val == "2"){ $pole3 .= "<span class=\"az-s1\">Veřejná</span> na: <span class=\"az-s2\">Neveřejná</span>"; }
              else{ $pole3 .= "<span class=\"az-s1\">".$val."</span> na: <span class=\"az-s2\">".$obj_upd[$key]."</span>"; }

              $pole3 .= ", ";
            
            } // konec key == typ_ip	    
            elseif( $key == "id_nodu" )
            {
              $pole3 .= "změna <b>Připojného bodu</b> z: ";

                $vysl_t1=mysql_query("select * from nod_list WHERE id = '$val'" );
                while ($data_t1=mysql_fetch_array($vysl_t1) )
                { $pole3 .= "<span class=\"az-s1\">".$data_t1["jmeno"]."</span>"; }

              $pole3 .= " na: ";

              $val2 = $obj_upd[$key];

                $vysl_t2=mysql_query("select * from nod_list WHERE id = '$val2'" );
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
   $add=mysql_query("INSERT INTO archiv_zmen (akce,provedeno_kym,vysledek) VALUES ".
		    "('".mysql_real_escape_string($pole2)."','".
			mysql_real_escape_string($nick)."','".
			mysql_real_escape_string($vysledek_write)."') ");
 
 //zmena sikany nebo IP adresy
 if( ereg(".*změna.*Šikana.*z.*", $pole3) )
 {
    Aglobal::work_handler("3"); //reinhard-fiber - sikana/net-n
 }

 //zmena NetN nebo IP adresy
 if( ereg(".*změna.*Povolen.*Inet.*z.*", $pole3) )
 {
    Aglobal::work_handler("3"); //reinhard-fiber - sikana/net-n
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
      Aglobal::work_handler("1"); //reinhard-3 (ros) - restrictions (net-n/sikana)
      Aglobal::work_handler("2"); //reinhard-wifi (ros) - restrictions (net-n/sikana)
	      
      Aglobal::work_handler("3"); //reinhard-fiber - sikana/net-n
      
      Aglobal::work_handler("4"); //reinhard-fiber - radius      
      Aglobal::work_handler("21"); //artemis - radius (tunel. verejky, optika)
      
      Aglobal::work_handler("6"); //(reinhard-fiber) - mikrotik.dhcp.leases.erase

      Aglobal::work_handler("7"); //(trinity) - sw.h3c.vlan.set.pl update
    
   }
   elseif(ereg(".*změna.*IP.*adresy.*z.*", $pole3)){
   
      Aglobal::work_handler("4"); //reinhard-fiber - radius      

      Aglobal::work_handler("6"); //(reinhard-fiber) - mikrotik.dhcp.leases.erase

      Aglobal::work_handler("7"); //(trinity) - sw.h3c.vlan.set.pl update
   }

   if(ereg(".*změna.*MAC.*adresy.*", $pole3)){

      Aglobal::work_handler("4"); //reinhard-fiber - radius      
      Aglobal::work_handler("21"); //artemis - radius (tunel. verejky, optika)
      
      Aglobal::work_handler("6"); //(reinhard-fiber) - mikrotik.dhcp.leases.erase
      Aglobal::work_handler("7"); //(trinity) - sw.h3c.vlan.set.pl update
   
   }

   //zmena pripojneho bodu
   
   //zmena tarifu
   
   //zmena cisla portu
   if(ereg(".*Číslo sw. portu.*", $pole3)){
      Aglobal::work_handler("4"); //reinhard-fiber - radius      
      Aglobal::work_handler("21"); //artemis - radius (tunel. verejky, optika)

      Aglobal::work_handler("7"); //(trinity) - sw.h3c.vlan.set.pl update
     
   }
   
?>
