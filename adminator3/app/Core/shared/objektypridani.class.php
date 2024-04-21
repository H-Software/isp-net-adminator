<?php

class objektypridani {

    function checkmac ($mac) 
    {
      $mac_check=ereg('^([[:xdigit:]]{2,2})\:([[:xdigit:]]{2,2})\:([[:xdigit:]]{2,2})\:([[:xdigit:]]{2,2})\:([[:xdigit:]]{2,2})\:([[:xdigit:]]{2,2})$',$mac);
      if ( !($mac_check) )
	{
	global $fail;	$fail="true";
	global $error;  $error .= "<div class=\"objekty-add-fail-mac\"><H4>MAC adresa ( ".$mac." ) není ve správném formátu !!! ( Správný formát je: 00:00:64:65:73:74 ) </H4></div>";
	}
		      
    //konec funkce check-mac
    }

    function checkSikanaCas($sikanacas) 
    {
        global $fail, $error;	
	
	$sikanacas = intval($sikanacas);
	
	if( ($sikanacas > 9) or ($sikanacas < 1) ){
	
	    $fail="true";
	
	    $error .= "<div class=\"objekty-add-fail-mac\">".
			"<H4>Do pole \"Šikana - počet dní\" je třeba vyplnit číslo 1 až 9.</H4></div>";	
	
	}
	 
    } //end of function checkSikanaCas

    function checkSikanaText($sikanatext) 
    {
        global $fail, $error;	

	if( (strlen($sikanatext) > 150) ){
	
	    $fail="true";
	
	    $error .= "<div class=\"objekty-add-fail-mac\">".
			"<H4>Do pole \"Šikana - text\" je možno zadat max. 150 znaků. (aktuálně: ".strlen($sikanatext).")</H4></div>";	
	
	}
	
    } //end of function checkSikanaText
    
    //function to validate ip address format in php by Roshan Bhattarai(http://roshanbh.com.np)
    function validateIpAddress($ip_addr)
    {
	//first of all the format of the ip address is matched
	if(preg_match("/^(\d{1,3})\.(\d{1,3})\.(\d{1,3})\.(\d{1,3})$/",$ip_addr))
        {
          //now all the intger values are separated
          $parts=explode(".",$ip_addr);
          //now we need to check each part can range from 0-255
          foreach($parts as $ip_parts)
          {
            if(intval($ip_parts)>255 || intval($ip_parts)<0)
            
            return false; //if number is not within range of 0-255
          }
        
          return true;
        }
        else
          return false; //if format of ip address doesn't matches
    }
                                                      
    function checkip ($ip)
    {      
      if ( !(objektypridani::validateIpAddress($ip)) )
      {
		global $fail;  $fail="true";
		global $error; $error .= "<div class=\"objekty-add-fail-ip\"><H4>IP adresa ( ".$ip." ) není ve správném formátu !!!</H4></div>";
      }
    } //konec funkce check-ip			 			 
    
    public static function checkcislo($cislo)
    {
     $rra_check=preg_match('/^([[:digit:]]+)$/',$cislo);
     
     if ( !($rra_check) )
     {
      global $fail;	$fail="true";
      global $error;	$error .= "<H4>Zadaný číselný údaj(e) ( ".$cislo." ) není ve  správném formátu !!! </H4>";
     }			        
    } //konec funkce check cislo
    
    public static function checkdns ($dns)
    {
		$dns_check=preg_match('/^([[:alnum:]]|\.|-)+$/',$dns);
		if ( !($dns_check) )
		{
			global $fail;	$fail="true";
			global $error; 	$error .= "<div class=\"objekty-add-fail-dns\"><H4>DNS záznam ( ".$dns." ) není ve správnem formátu !!! </H4></div>";
		}
    } // konec funkce check rra
    
    function check_l2tp_cr($cr)
    {
		$cr_check=preg_match('/^([[:alnum:]])+$/',$cr);
		
		if( !($cr_check) )
		{
			global $fail;	
			$fail="true";
			
			global $error; 	
			$error .= "<div class=\"objekty-add-fail-dns\"><H4>Tunel. login/heslo ( ".$cr." ) není ve správnem formátu !!! </H4></div>";
		}
    
		if( (strlen($cr) <> 4) )
		{
			global $fail;	
			$fail="true";
			
			global $error; 	
			$error .= "<div class=\"objekty-add-fail-dns\"><H4>Tunel. login/heslo ( ".$cr." ) musí mít 4 znaky !!! </H4></div>";
		
		}
    } //konec funkce check_l2tp_cr
    
    public static function generujdata ($selected_nod, $typ_ip, $dns, $conn_mysql)
    {
     // promenne ktere potrebujem, a ktere budeme ovlivnovat
     global $ip, $mac, $ip_rozsah, $umisteni_aliasu, $tunnel_user, $tunnel_pass, $fail, $error;    
	    
     // skusime ip vygenerovat
	 try {
		$vysl_ip = $conn_mysql->query("SELECT ip_rozsah FROM nod_list WHERE id = '".intval($selected_nod)."' ");
	 } catch (Exception $e) {
		die ("<h2 style=\"color: red; \">Error: Database query failed! Caught exception: " . $e->getMessage() . "\n" . "</h2></body></html>\n");
	 }

     $radku_ip=$vysl_ip->num_rows;

     //print "<div style=\"color: grey;\" >debug sql: "."SELECT ip_rozsah, umisteni_aliasu FROM nod_list WHERE id = '".intval($selected_nod)."' "."</div>";
    	    
     if($radku_ip == 1) 
     {
		while ($data_ip=$vysl_ip->fetch_array() ){
		
			$ip_rozsah=$data_ip["ip_rozsah"];
			
			list($a,$b,$c,$d) =preg_split("/[.]/",$ip_rozsah);
		}
	
	/*
	if( $ip_rozsah){
	
	       $gen_ip="E_4";
	       
	       if( ( strlen($ip) <= 0) ){ $ip=$gen_ip; }
	       return false;
	}
	*/
	
	 if( $c == 0)
	 {
	    // b-ckova ip
	    $gen_ip_find=$a.".".$b.".".$c.".".$d."/16";
	 
	     $msq_check_ip=pg_query("SELECT ip FROM objekty WHERE ip <<= '$gen_ip_find' order by ip asc");
	     $msq_check_ip_radku=pg_num_rows($msq_check_ip);
								 
	     if ( $msq_check_ip_radku == 0 ) { 
	        $c=10; 
	        $gen_ip=$a.".".$b.".".$c.".".$d;
	     }
	     else 
	     {
										 
	      while (  $data_check_ip=pg_fetch_array($msq_check_ip) ) 
	      { $gen_ip=$data_check_ip["ip"]; }
	
	      list($a,$b,$c,$d) = preg_split("/[.]/",$gen_ip);
		
	      $limit=250;
	      global $ip_error;
	     
	      if( ($a == "212") and ($b == "80") ){ $gen_ip=$ip_rozsah; $ip_error="1"; }
	      elseif( ( $c >= $limit ) ) { $gen_ip=$ip_rozsah; $ip_error="1"; }
	      else
	      {
	         list($a,$b,$c,$d) = split("[.]",$gen_ip);
	        $c=$c+1;
		$d="3";
		$gen_ip=$a.".".$b.".".$c.".".$d;
		
	      } //konec else gen ip > 255
	      
	     } //konec else msq_check_ip_radku == 0
		
	  } //konec if c == 0
	  elseif( ($a == "212") and ($b == "80") )
	  { //verejny, 2 -- rout. prima, 4 -- tunelovana
	     
	    $sql_src = "SELECT INET_NTOA(ip_address) AS ip_address FROM public_ip_to_use ";
	    
	    if($typ_ip==2)
	    {  $sql_src .= " WHERE mode = '1' "; }
	    elseif($typ_ip==4)
	    {  $sql_src .= " WHERE mode = '0' "; }
	    else
	    {
	       $gen_ip=$ip_rozsah; 
	       
	       if( ( strlen($ip) <= 0) ){ $ip=$gen_ip; }
	       return false;
	    }
	  
	    $sql_src .= " ORDER BY public_ip_to_use.ip_address ASC ";
		// try {
		// 	$ = $conn_mysql->query();
		//  } catch (Exception $e) {
		// 	die ("<h2 style=\"color: red; \">Error: Database query failed! Caught exception: " . $e->getMessage() . "\n" . "</h2></body></html>\n");
		//  }
	    $dotaz=mysql_query($sql_src);
	    
	    if( (mysql_num_rows($dotaz) == 0) )
	    {
	       $gen_ip="E_3";
	       
	       if( ( strlen($ip) <= 0) ){ $ip=$gen_ip; }
	       return false;
	    }
	
	
	    while($data = mysql_fetch_array($dotaz))
	    {
		$ip_address = $data["ip_address"];
	    
	        //kontrola :-)
		//if(true){ $gen_ip = $ip_address; }
		
		$dotaz_check = pg_query("SELECT ip FROM objekty WHERE ip <<= '$ip_address' ");
	        $dotaz_check_radku = pg_num_rows($dotaz_check);
		
	        if( ($dotaz_check_radku > 1) )
		{ //chyba, vice adres vyhovelo vyberu
	    	    $gen_ip="E_4";
	       
	    	    if( ( strlen($ip) <= 0) ){ $ip=$gen_ip; }
	    	    return false;
		}
		elseif($dotaz_check_radku == 0)
		{ //ip v DB není, OK
	    	    $gen_ip = $ip_address;
	       
	    	    if( ( strlen($ip) <= 0) ){ $ip=$gen_ip; }
	    	    break;
		}
		
	     } //end of while data fetch dotaz
	    	
	  } //end of generate public IP address
	  elseif ( ( $d==0 and $c != 0 ) )
	  {
	    // c-ckova ip	
		$gen_ip_find=$a.".".$b.".".$c.".".$d."/24";
		
		$msq_check_ip=pg_query("SELECT * FROM objekty WHERE ip <<= '$gen_ip_find' order by ip asc");
		$msq_check_ip_radku=pg_num_rows($msq_check_ip);
		
		if( $msq_check_ip_radku == 0 ){ $d=10; $gen_ip=$a.".".$b.".".$c.".".$d; }
		else 
		{
		 while( $data_check_ip=pg_fetch_array($msq_check_ip) )
		 { $gen_ip=$data_check_ip["ip"]; }
		     
		 list($a,$b,$c,$d) = split("[.]",$gen_ip);
		    
		 global $ip_error;
		     
		 if( $d >= "254"){ $gen_ip=$a.".".$b.".".$c.".0"; $ip_error="1"; $ip_rozsah=$gen_ip; }
		 else
		 {
		  $d=$d+2;
		  $gen_ip=$a.".".$b.".".$c.".".$d;
		 }
		} // konec else radku == 0
		
	      // konec gen. ceckovy ip
	   }
	   else
	   {
	     $gen_ip = "E1"; //echo "chybnej vyber";
	   }
		
	    // vysledek predame
	    if( ( strlen($ip) <= 0) ){ $ip=$gen_ip; }
		
	 
	 } //end of: if $radku_ip == 1                                     
         else{
        
	    // vysledek predame
	     if( ( strlen($ip) <= 0) ){ 
                $gen_ip = "E2"; //asi neprosel SQL dotaz	  
             }
             
             return false;
         }
         
         //zde generovani dalsich velicin
	 if($typ_ip == 4)
	 {
	   if( (  (strlen($dns)<= 0) and (strlen($tunnel_user) <= 0) and ( strlen($tunnel_pass) <= 0) ) )
	   {
	      $gen_user = "E_DNS";
	      $gen_pass = "E_DNS";
	   }
	   else
	   {
		$dns_trim = substr($dns, 0, 3).rand(0, 9);
		$dns_trim2 = substr($dns, 0, 2).rand(0, 99);
		
		
		$gen_user = $dns_trim;
		$gen_pass = $dns_trim2;
	   }
	   
	   if( ( strlen($tunnel_user) <= 0) ){ $tunnel_user=$gen_user; }
	   if( ( strlen($tunnel_pass) <= 0) ){ $tunnel_pass=$gen_pass; }
	 
	 
	 } //konec if typ_ip == 4
	 
    } // konec funkce generujdata
    
} //konec objketu objekty-pridani
