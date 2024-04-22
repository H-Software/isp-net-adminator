<?php

use Illuminate\Database\Capsule\Manager as DB;

// $pole3 .= "<br>";
 $pole2 .= " diferencialni data: ";
 
     //novy zpusob archivovani dat
     foreach($pole_puvodni_data as $key => $val)
     {
      if ( !($vlast_upd[$key] == $val) )
      {
        if ( !($key == "id_cloveka") )
        {
            if( $key == "vs" )
            {
              $pole3 .= "změna <b>Variabilního symbolu</b> z: ";
              $pole3 .= "<span class=\"az-s1\">".$val."</span> na: <span class=\"az-s2\">".$vlast_upd[$key]."</span>";
              $pole3 .= ", ";
            } //konec key == vs
	    elseif( $key == "archiv" )
	    {
	      $pole3 .= "změna <b>Archivu</b> z: ";
				
	      if( $val == "0" and $vlast_upd[$key] == "1")
	       { $pole3 .= "<span class=\"az-s1\">Ne</span> na: <span class=\"az-s2\">Ano</span>"; }
	      elseif( $val == "1" and $vlast_upd[$key] == "0")
	       { $pole3 .= "<span class=\"az-s1\">Ano</span> na: <span class=\"az-s2\">Ne</span>"; }
	      else
	       { $pole3 .= "<span class=\"az-s1\">".$val."</span> na: <span class=\"az-s2\">".$vlast_upd[$key]."</span>"; }
										  
	      $pole3 .= ", ";
	    } //konec key == archiv												    
            elseif( $key == "sluzba_int" )
	    {
	      $pole3 .= "změna <b>Služba Internet</b> z: ";
				
	      if( $val == "0" and $vlast_upd[$key] == "1")
	       { $pole3 .= "<span class=\"az-s1\">Ne</span> na: <span class=\"az-s2\">Ano</span>"; }
	      elseif( $val == "1" and $vlast_upd[$key] == "0")
	       { $pole3 .= "<span class=\"az-s1\">Ano</span> na: <span class=\"az-s2\">Ne</span>"; }
	      else
	       { $pole3 .= "<span class=\"az-s1\">".$val."</span> na: <span class=\"az-s2\">".$vlast_upd[$key]."</span>"; }
										  
	      $pole3 .= ", ";
	    } //konec key == sluzba_int												    
            elseif( $key == "sluzba_iptv" )
	    {
	      $pole3 .= "změna <b>Služba IPTV</b> z: ";
				
	      if( $val == "0" and $vlast_upd[$key] == "1")
	       { $pole3 .= "<span class=\"az-s1\">Ne</span> na: <span class=\"az-s2\">Ano</span>"; }
	      elseif( $val == "1" and $vlast_upd[$key] == "0")
	       { $pole3 .= "<span class=\"az-s1\">Ano</span> na: <span class=\"az-s2\">Ne</span>"; }
	      else
	       { $pole3 .= "<span class=\"az-s1\">".$val."</span> na: <span class=\"az-s2\">".$vlast_upd[$key]."</span>"; }
										  
	      $pole3 .= ", ";
	    } //konec key == sluzba_iptv											    
            elseif( $key == "sluzba_voip" )
	    {
	      $pole3 .= "změna <b>Služba VoIP</b> z: ";
				
	      if( $val == "0" and $vlast_upd[$key] == "1")
	       { $pole3 .= "<span class=\"az-s1\">Ne</span> na: <span class=\"az-s2\">Ano</span>"; }
	      elseif( $val == "1" and $vlast_upd[$key] == "0")
	       { $pole3 .= "<span class=\"az-s1\">Ano</span> na: <span class=\"az-s2\">Ne</span>"; }
	      else
	       { $pole3 .= "<span class=\"az-s1\">".$val."</span> na: <span class=\"az-s2\">".$vlast_upd[$key]."</span>"; }
										  
	      $pole3 .= ", ";
	    } //konec key == sluzba_voip											    
	    elseif( $key == "billing_freq" )
	    {
	      $pole3 .= "změna <b>Frekvence fakturování</b> z: ";
				
	      if( $val == "0" and $vlast_upd[$key] == "1")
	       { $pole3 .= "<span class=\"az-s1\">Měsíční</span> na: <span class=\"az-s2\">Čtvrtletní</span>"; }
	      elseif( $val == "1" and $vlast_upd[$key] == "0")
	       { $pole3 .= "<span class=\"az-s1\">Čtvrtletní</span> na: <span class=\"az-s2\">Měsíční</span>"; }
	      else
	       { $pole3 .= "<span class=\"az-s1\">".$val."</span> na: <span class=\"az-s2\">".$vlast_upd[$key]."</span>"; }
										  
	      $pole3 .= ", ";
	    }
	    elseif( $key == "fakturacni_skupina_id" )
	    {
	      $pole3 .= "změna <b>Fakturační skupiny</b> z: ";
	    
		  $fs_ols_rs = $conn_mysql->query("SELECT nazev FROM fakturacni_skupiny WHERE id = '".intval($val)."'");	      
		  $fs_old_rs->data_seek(0);
		  list($fs_old) = $fs_old_rs->fetch_row();

		  $fs_new_rs = $conn_mysql->query("SELECT nazev FROM fakturacni_skupiny WHERE id = '".intval($vlast_upd[$key])."'");

		  $fs_new_rs->data_seek(0);
		  list($fs_new) = $fs_new->fetch_row();
	    
	      if( isset($fs_old) )
	      { $pole3 .= "<span class=\"az-s1\">".$fs_old."</span> "; }
	      else
	      { $pole3 .= "<span class=\"az-s1\">".$val."</span> "; }
	      
	      if( isset($fs_new) )
	      { $pole3 .= "na: <span class=\"az-s2\">".$fs_new."</span>"; }
	      else
	      { $Pole3 .= "na: <span class=\"az-s2\">".$vlast_upd[$key]."</span>"; }
	      
	      $pole3 .= ", ";
	      
	    } //end of elseif fakturacni_skupina_id
	    elseif( $key == "billing_suspend_status" )
	    {
	      $pole3 .= "změna <b>Pozastavené fakturace</b> z: ";
	    
	      if( $val == "0" and $vlast_upd[$key] == "1")
	       { $pole3 .= "<span class=\"az-s1\">Ne</span> na: <span class=\"az-s2\">Ano</span>"; }
	      elseif( $val == "1" and $vlast_upd[$key] == "0")
	       { $pole3 .= "<span class=\"az-s1\">Ano</span> na: <span class=\"az-s2\">Ne</span>"; }
	      else
	       { $pole3 .= "<span class=\"az-s1\">".$val."</span> na: <span class=\"az-s2\">".$vlast_upd[$key]."</span>"; }
										  
	      $pole3 .= ", ";
	    
	    }
	    elseif( $key == "billing_suspend_reason" )
	    {
	      $pole3 .= "změna <b>Důvod pozastavení</b> z: ";
	      $pole3 .= "<span class=\"az-s1\" >".$val."</span> ";
              $pole3 .= "na: <span class=\"az-s2\">".$vlast_upd[$key]."</span>, ";
	    }
	    elseif($key == "billing_suspend_start")
	    {
	      $pole3 .= "změna <b>Poz. fakturace - od kdy</b> z: ";
	    
	      list($b_s_s_rok,$b_s_s_mesic,$b_s_s_den) = explode("-",$val);
	      $val_cz = $b_s_s_den.".".$b_s_s_mesic.".".$b_s_s_rok;
	                                                  
	      $pole3 .= "<span class=\"az-s1\" >".$val_cz."</span> ";
            
           list($b_s_s_rok,$b_s_s_mesic,$b_s_s_den) = explode("-",$vlast_upd[$key]);
	      $val_cz_2 = $b_s_s_den.".".$b_s_s_mesic.".".$b_s_s_rok;
	    
            $pole3 .= "na: <span class=\"az-s2\">".$val_cz_2."</span>, ";
	                                 
	    }
	    elseif($key == "billing_suspend_stop")
	    {
	      $pole3 .= "změna <b>Poz. fakturace - do kdy</b> z: ";
	    
	      list($b_s_s_rok,$b_s_s_mesic,$b_s_s_den) = explode("-",$val);
	      $val_cz = $b_s_s_den.".".$b_s_s_mesic.".".$b_s_s_rok;
	                                                  
	      $pole3 .= "<span class=\"az-s1\" >".$val_cz."</span> ";
            
              list($b_s_s_rok,$b_s_s_mesic,$b_s_s_den) = explode("-",$vlast_upd[$key]);
	      $val_cz_2 = $b_s_s_den.".".$b_s_s_mesic.".".$b_s_s_rok;
	    
              $pole3 .= "na: <span class=\"az-s2\">".$val_cz_2."</span>, ";
	                                 
	    }
	    else
            { // ostatni mody, nerozpoznane
              $pole3 .= "změna pole: <b>".$key."</b> z: <span class=\"az-s1\" >".$val."</span> ";
              $pole3 .= "na: <span class=\"az-s2\">".$vlast_upd[$key]."</span>, ";
            }
         } //konec if nejde li od id cloveka
       } // konec if obj == val
     } // konec foreach

    //$pole2 .=",<br> stavajici data: ";

    foreach ($vlast_upd_old as $key => $val) 
    { 
      //if( $key == "id_cloveka" )
      { $pole2 .= " [".$key."] => ".$val." , "; }
    }
    		   
    $pole2 .= "".$pole3;
				     
   if ( $res == 1){ $vysledek_write="1"; }
   else{
	$vysledek_write = "0";
   }

   $id = DB::table('archiv_zmen')
   					->insertGetId([
						'akce' => $pole2,
						'vysledek' => $vysledek_write,
						'provedeno_kym' => $nick
						]);

	if( $id > 0 )
	{ echo "<br><H3><div style=\"color: green;\" >Změna byla úspěšně zaznamenána do archivu změn.</div></H3>\n"; } 
	else
	{ echo "<br><H3><div style=\"color: red;\" >Chyba! Změnu do archivu změn se nepodařilo přidat.</div></H3>\n"; }	

   // $add=mysql_query("INSERT INTO archiv_zmen (akce,provedeno_kym,vysledek) VALUES ('$pole2','$nick','$vysledek_write')");
 