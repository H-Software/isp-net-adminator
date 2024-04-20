<?php

class objektypridanifiber{

    function generujdata( $selected_nod,$id_tarifu )
    {
  
       global $ip;
       //global $mac;
       //global $rra;
       global $ip_rozsah;
  
       if($selected_nod < 1 )
       {
         echo "";
         return false;
       }
           
       // skusime ip vygenerovat
       $vysl_nod=mysql_query("SELECT ip_rozsah FROM nod_list WHERE id = '".intval($selected_nod)."'");
       $radku_nod=mysql_num_rows($vysl_nod);
  
       if( $radku_nod <> 1 )
       { 
         if( ( strlen($ip) < 1 ) ){ $ip = "E_1"; }
         return false;
       }
       else
       {
           while( $data_nod=mysql_fetch_array($vysl_nod) ):
         $ip_rozsah=$data_nod["ip_rozsah"];
      //   $umisteni_aliasu=$data_nod["umisteni_aliasu"];  
      endwhile;
       }
      
       $vysl_tarif = mysql_query("SELECT gen_poradi FROM tarify_int WHERE id_tarifu = '".intval($id_tarifu)."' ");
       $radku_tarif=mysql_num_rows($vysl_tarif);
   
       if( $radku_tarif <> 1 )
       { 
         if( ( strlen($ip) < 1 ) ){ $ip = "E_2"; }
         return false;
       }
       else
       {
           while( $data_tarif = mysql_fetch_array($vysl_tarif) ):
         $gen_poradi = $data_tarif["gen_poradi"];
      endwhile;
       }
       
       if( !( $gen_poradi > 0 ) )
       {
         //znama chyba, nechame prazdne...
         //if( ( strlen($ip) < 1 ) ){ $ip = "E_3"; } 
         return false;
       }
       
       list($r_a, $r_b, $r_c, $r_d) =split("[.]",$ip_rozsah);
       
       if( $gen_poradi == 1 )	{ $r_d = $r_d + "0"; }
       elseif( $gen_poradi == 2 ) { $r_d = $r_d + "128"; }
       elseif( $gen_poradi == 3 ) { $r_c = $r_c + "1"; }
       elseif( $gen_poradi == 4 )
       { 	$r_c = $r_c + "1";	$r_d = $r_d + "128"; }
       elseif( $gen_poradi == 5 )
       {  $r_c = $r_c + "2";	$r_d = $r_d + "0";   }
       elseif( $gen_poradi == 6 )
       {  $r_c = $r_c + "2";	$r_d = $r_d + "128";   }
       elseif( $gen_poradi == 7 )
       {  $r_c = $r_c + "3";	$r_d = $r_d + "0";   }
       elseif( $gen_poradi == 8 )
       {  $r_c = $r_c + "3";	$r_d = $r_d + "128";   }
       
       else
       {
         if( ( strlen($ip) < 1 ) ){ $ip = "E_4"; }  
         return false;
       }
       
       $sub_rozsah = $r_a.".".$r_b.".".$r_c.".".$r_d;
       
       $sub_rozsah_d = $r_d;
       
       $r_d = $r_d + "8";
      
       $check_ip = pg_query("SELECT * FROM objekty WHERE ip <<= '$sub_rozsah/26' ORDER BY ip ASC");
       $check_ip_radku = pg_num_rows($check_ip);
       
       //echo "subrozsah: ".$sub_rozsah." xxx";
       
       if( $check_ip_radku == 0 ) // v rozsahu zadna ip, takze generujem prvni..
       { 
         $gen_ip = $r_a.".".$r_b.".".$r_c.".".$r_d; 
         //$gen_ip = "vole...";
       }
       else //v db je vice ip adres ...
       {
        //nacteni predchozi ip adresy ..
        while(  $data_check_ip = pg_fetch_array($check_ip) )
        { $gen_ip2=$data_check_ip["ip"]; }
            
        list($g_a,$g_b,$g_c,$g_d) = split("[.]",$gen_ip2);
  
        if( $sub_rozsah_d == "0" ){ $limit = 120; }
        elseif( $sub_rozsah_d == "128" ){ $limit = 250; }
        else
        {
          if( ( strlen($ip) < 1 ) ){ $ip = "E_5"; }  
          return false;
        }
           
        if( ( $g_d >= $limit ) ){ $gen_ip=$ip_rozsah; $ip_error="1"; }
        else
        {
         //zde tedy pricist udaje a predat ...
         $g_d = $g_d + 2;
         
         //zpetna kontrola jeslti to neni lichy..
         $rs = $g_d % 2;
         
         if( $rs == 1) //je to lichy, chyba ...
         {
           if( ( strlen($ip) < 1 ) ){ $ip = "E_5"; }  
           return false;
         } 
         else //neni to lichy, takze je to spravne, cili finalni predani .
         {
           $gen_ip = $g_a.".".$g_b.".".$g_c.".".$g_d;
         }
        } // konec else if g_d pres limit
       
       } // konec else if  check_ip_radku == 0
       
             
       //tady asi cosi neni-li zadana ip, tak gen_ip = ip;
       if( ( strlen($ip) < 1 ) ){ $ip = $gen_ip; }
       
       //return true;  
      } //konec funkce generujdata
      
  } // konec objektu objekty pridani fiber
  