<?

 // $nod_upd["vlan_id"] = $vlan_id;
 
 //$pole .= "<br> aktualni data: ";
 //foreach ($nod_upd as $key => $val) { $pole .= " [".$key."] => ".$val."\n"; }

 $pole3 .= "[id_fs] => ".$update_id;
 $pole3 .= " diferencialni data: ";
 
     //novy zpusob archivovani dat
     foreach($pole_puvodni_data as $key => $val)
     {
       if( !($fs_upd[$key] == $val) )
       {
        if( $key == "pozn" )
        {
          $pole3 .= "změna <b>Poznámky</b> z: ";
          $pole3 .= "<span class=\"az-s1\">".$val."</span> na: <span class=\"az-s2\">".$fs_upd[$key]."</span>";
          $pole3 .= ", ";
        } //konec key == pozn
	else
        { // ostatni mody, nerozpoznane
          $pole3 .= "změna pole: <b>".$key."</b> z: <span class=\"az-s1\" >".$val."</span> ";
          $pole3 .= "na: <span class=\"az-s2\">".$fs_upd[$key]."</span>, ";
        } //konec else
       } // konec if key == val
     } // konec foreach

  $pole .= "".$pole3;

 // global $uprava;
  
  if ( $res == 1){ $vysledek_write="1"; }
  else{ $vysledek_write="0"; }
  
  $add=mysql_query("INSERT INTO archiv_zmen (akce,provedeno_kym,vysledek) VALUES ('$pole','$nick','$vysledek_write') ");
	     
?>
