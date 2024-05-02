<?php
 
 $pole_upd["login"] = $login_jmeno;
 $pole_upd["name"] = $jmeno;
 $pole_upd["email"] = $email;
 $pole_upd["login_level"] = $login_level;
 $pole_upd["smb_user"] = $smb_user;
 
 $pole3 .= "[admin_id]=> ".intval($update_id).",";
 
 $pole3 .= " diferencialni data: ";
 
     //novy zpusob archivovani dat
     foreach ($pole_puvodni_data as $key => $val)
     {
      if( !($pole_upd[$key] == $val) )
      {
        if( !($key == "id") )
        {
            if( $key == "name" )
            {
              $pole3 .= "změna <b>jména</b> z: ";
              $pole3 .= "<span class=\"az-s1\">".$val."</span> na: <span class=\"az-s2\">".$pole_upd[$key]."</span>";
              $pole3 .= ", ";
            } //konec key == name
	    elseif( $key == "login_level" )
            {
              $pole3 .= "změna <b>levelu</b> z: ";
              $pole3 .= "<span class=\"az-s1\">".$val."</span> na: <span class=\"az-s2\">".$pole_upd[$key]."</span>";
              $pole3 .= ", ";
            } //konec key == login_level
	    else
            { // ostatni mody, nerozpoznane
              $pole3 .= "změna pole: <b>".$key."</b> z: <span class=\"az-s1\" >".$val."</span> ";
              $pole3 .= "na: <span class=\"az-s2\">".$pole_upd[$key]."</span>, ";
            }

         } //konec if nejde li od id ( to v tom poli neni )
       } // konec if obj == val
     } // konec foreach
			   
   $pole2 .= "".$pole3;
				
   //debug:
   //$pole2 .= "count pole_puvodni_data: ".count($pole_puvodni_data);
    
   if($res == 1){ $vysledek_write=1; }  
   
   $add=$conn_mysql->query("INSERT INTO archiv_zmen (akce,provedeno_kym,vysledek) VALUES ".
		    "('".$conn_mysql->real_escape_string($pole2)."','".
			$conn_mysql->real_escape_string(\Cartalyst\Sentinel\Native\Facades\Sentinel::getUser()->email)."','".
			$conn_mysql->real_escape_string($vysledek_write)."') ");
    
?>
