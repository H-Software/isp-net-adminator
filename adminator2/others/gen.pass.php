<?php

echo "nove heslo: ";

echo md5('jursa123sim');

echo "<br><br>";

echo "rss datum: ".gmdate('D, d M Y H:i:s', $row["datum_pridani"] );

echo "datum je: ".time();

$caslogin = strftime("%H", time());
 
echo "datum2: $caslogin <br><br>";

   $obj_add = array( "dns_jmeno" => "naky dns jmeno", "ip" => $ip, "mac" => $mac,
          "rra" => $rra, "vezeni" => $vezeni_w, "dov_net" => $dov_net_w, "swz" => $swz_w,
           "sc" => $sc_w, "typ" => $typ, "poznamka" => $pozn, "verejna" => $verejna_w,
            "pridal" => $nick , "rb_ip" => $rb_ip, "rb_mac" => $rb_mac, "id_nodu" => $selected_nod );

    echo "vypis pole: ";
   $pole=print_r($obj_add);
//    $pole=array_values($obj_add);

foreach ($obj_add as $key => $val) 
    {
    echo "cislo: [".$key."] => ".$val."\n";
    }
        	
   echo "$pole";
   
   echo "request uri: ".$_SERVER["REQUEST_URI"];							
    echo " <br>http referer: ".$_SERVER["HTTP_REFERER"];
    echo "<br> php_self: ".$_SERVER["SCRIPT_FILENAME"];
?>