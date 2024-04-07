<?php

 // konverze promennych
 $ec = iconv("UTF-8","CP1250", $ec);
 
 $jmeno = iconv("UTF-8","CP1250", $jmeno );
 
 if( (strlen($nazev_spol) > 1 ) )
 { $nazev_spol = iconv("UTF-8","CP1250", $nazev_spol); }
 else
 { $nazev_spol = "- - - - -"; }

 $adresa = iconv("UTF-8","CP1250", $adresa );
 
 if( (strlen($ico_dic) > 1) )
 { $ico_dic = iconv("UTF-8","CP1250", $ico_dic); }
 else
 { $ico_dic = "- - - - -"; }
 
 $mesto = iconv("UTF-8","CP1250", $mesto );
 $email = iconv("UTF-8","CP1250", $email );

 $telefon = iconv("UTF-8","CP1250", $telefon );

 if( (strlen($kor_adresa) > 1) )
 { $kor_adresa = iconv("UTF-8","CP1250", $kor_adresa); }
 else
 { $kor_adresa = "- - - - -"; }
 
 if( (strlen($kor_mesto) > 1) )
 { $kor_mesto = iconv("UTF-8","CP1250", $kor_mesto); }
 else
 { $kor_mesto = "- - - - -"; }

 //rozhodovani jestli budou udaje ci pomlcky
 if( $spec_prip_mista == 1 )
 {
    $spec_prip_mista = "X";
    
    $prip_misto_adresa = "- - - - - -";
    $prip_misto_cp = "- - -";
    $prip_misto_mesto = "- - - - - ";
    $prip_misto_psc = "- - -";
 }
 else
 {
    $spec_prip_mista = "-";
    $prip_misto_adresa = iconv("UTF-8","CP1250", $prip_misto_adresa);
    $prip_misto_cp = iconv("UTF-8","CP1250", $prip_misto_cp);
    $prip_misto_mesto = iconv("UTF-8","CP1250", $prip_misto_mesto);
    $prip_misto_psc = iconv("UTF-8","CP1250", $prip_misto_psc);    
 }
 
 if( $adr_prip_jako_kor == 2 )
 { $adr_prip_jako_kor = "X"; }
 else
 { $adr_prip_jako_kor = "-"; }

 if( $prip_tech == 1 )
 { $prip_tech_1 = "X"; }
 elseif( $prip_tech == 2 ) 
 { $prip_tech_2 = "X"; }
 elseif( $prip_tech == 3 )
 { $prip_tech_3 = "X"; }

 if( (strlen($int_sluzba_tarif_text) >= 1 ) )
 { $int_sluzba_tarif_text = iconv("UTF-8","CP1250", $int_sluzba_tarif_text); }
 else
 { $int_sluzba_tarif_text = "- - - - -"; }
 
 if( (strlen($int_sluzba_tarif_cena) >= 1 ) )
 { $int_sluzba_tarif_cena = iconv("UTF-8","CP1250", $int_sluzba_tarif_cena).",-"; }
 else
 { $int_sluzba_tarif_cena = "- - -"; }
 
 if( (strlen($int_sluzba_tarif_cena_s_dph) >= 1 ) )
 { $int_sluzba_tarif_cena_s_dph = iconv("UTF-8","CP1250", $int_sluzba_tarif_cena_s_dph).",-"; }
 else
 { $int_sluzba_tarif_cena_s_dph = "- - -"; }

 if( (strlen($int_sluzba_rychlost) >= 1 ) )
 { $int_sluzba_rychlost = intval(iconv("UTF-8","CP1250", $int_sluzba_rychlost)); }
 else
 { $int_sluzba_rychlost = "- -"; }
 
 if( (strlen($int_sluzba_tarif_agr) >= 1 ) )
 { $int_sluzba_tarif_agr = iconv("UTF-8","CP1250", $int_sluzba_tarif_agr); }
 else
 { $int_sluzba_tarif_agr = "-"; }
 
 //$int_verejna_ip = $_POST["int_verejna_ip"];
 
 if( $int_verejna_ip == 1 )
 { $int_verejna_ip_x = "X"; }
 else
 { $int_verejna_ip_x = "-"; }
 
 if( (strlen($int_verejna_ip_cena) >= 1 ) )
 { $int_verejna_ip_cena = iconv("UTF-8","CP1250", $int_verejna_ip_cena).",-"; }
 else
 { $int_verejna_ip_cena = "- - -"; }
 
 if( (strlen($int_verejna_ip_cena_s_dph ) >= 1 ) )
 { $int_verejna_ip_cena_s_dph = iconv("UTF-8","CP1250", $int_verejna_ip_cena_s_dph).",-"; }
 else
 { $int_verejna_ip_cena_s_dph = "- - -"; } 

 //$iptv_sluzba = $_POST["iptv_sluzba"];
 if( $iptv_sluzba_id_tarifu ==1 )
 {
  $iptv_sluzba_tarif_1 = "X";
  $iptv_sluzba_tarif_2 = "-";
 }
 elseif( $iptv_sluzba_id_tarifu == 2 )
 {
  $iptv_sluzba_tarif_1 = "-";
  $iptv_sluzba_tarif_2 = "X";
 }
 else
 {
  $iptv_sluzba_tarif_1 = "-";
  $iptv_sluzba_tarif_2 = "-";
 }
 
 if( (strlen($iptv_sluzba_cena ) >= 1 ) )
 { $iptv_sluzba_cena = $iptv_sluzba_cena.",-"; }
 else
 { $iptv_sluzba_cena = "- - -"; }
 
 if( (strlen($iptv_sluzba_cena_s_dph) >= 1 ) )
 { $iptv_sluzba_cena_s_dph = $iptv_sluzba_cena_s_dph.",-"; }
 else
 { $iptv_sluzba_cena_s_dph = "- - -"; }

 for($i=1; $i<=$pocet_tb; $i++)
 {
    $tb = "tb".$i;
    
    $tb_cena = "tb_cena_".$i;
    $tb_cena_s_dph = "tb_cena_s_dph_".$i;
    
    $tb_x = "tb".$i."_x";
	
    if( (strlen($$tb) > 1) )
    { 
	$$tb_x = "X";
	$$tb = iconv("UTF-8","CP1250", $$tb); 
    }
    else
    { 
	$$tb_x = " -";
	$$tb = "- - - - -"; 
    }

    if( (strlen($$tb_cena) >= 1) )
    { $$tb_cena = iconv("UTF-8","CP1250", $$tb_cena).",-"; }
    else
    { $$tb_cena = "- - -"; }
    
    if( (strlen($$tb_cena_s_dph) >= 1) )
    { $$tb_cena_s_dph = iconv("UTF-8","CP1250", $$tb_cena_s_dph).",-"; }
    else
    { $$tb_cena_s_dph = "- - -"; }
    
 }

 for($i=1; $i<=2; $i++)
 {
    $tb = "tb".$i;
    $tb_x = "tb".$i."_x";
    
    $tb_cena = "tb_cena_".$i;
    $tb_cena_s_dph = "tb_cena_s_dph_".$i;

    if( (strlen($$tb_x) < 1) )
    { $$tb_x = " -"; }
    
    if( (strlen($$tb) < 1) )
    { $$tb = "- - - - -"; }
    
    if( (strlen($$tb_cena) < 1) )
    { $$tb_cena = "- - -"; }

    if( (strlen($$tb_cena_s_dph) < 1) )
    { $$tb_cena_s_dph = "- - -"; }
    
 }
  
 if( (strlen($voip_cislo) < 1) )
 {
   $voip_cislo = "- - - - -";
 }

 if( $voip_typ == 1 )
 {
  $voip_postpaid = "X";
  $voip_prepaid = "-";
 }
 elseif( $voip_typ == 2 )
 {
  $voip_postpaid = "-";
  $voip_prepaid = "X";
 }
 else
 {
  $voip_postpaid = "-";
  $voip_prepaid = "-";
 }

 if( $zpusob_placeni == 1)
 {
  $zpusob_placeni_1 = "X";
  $zpusob_placeni_2 = " -";
  $zpusob_placeni_3 = " -";
 }
 elseif( $zpusob_placeni == 2)
 {
  $zpusob_placeni_1 = " -";
  $zpusob_placeni_2 = "X";
  $zpusob_placeni_3 = " -";
 }
 elseif( $zpusob_placeni == 3)
 {
  $zpusob_placeni_1 = " -";
  $zpusob_placeni_2 = " -";
  $zpusob_placeni_3 = "X";
 }
 else
 {
  $zpusob_placeni_1 = "-";
  $zpusob_placeni_2 = "-";
  $zpusob_placeni_3 = "-";
 }
 
 if( (strlen($celk_cena) > 1) )
 {
  $celk_cena = $celk_cena.",-";
 }

 if( (strlen($celk_cena_s_dph) > 1) )
 {
  $celk_cena_s_dph = $celk_cena_s_dph.",-";
 }	

 if( $sleva_select == 1 )
 {
  $sleva_on = "X";
  $sleva_hodnota = iconv("UTF-8","CP1250", $sleva_hodnota);
 }
 else
 { 
  $sleva_on = "-"; 
  $sleva_hodnota = "- -";
 }
 
 if( $min_plneni == 2 )
 { $min_plneni_on = "X"; }
 else
 { 
    $min_plneni_on = "-"; 
    $min_plneni_doba = "- -"; 
 }

 if($platba == 2)
 {
    $platba_mes = "-";
    $platba_ctvrt = "X";
 }
 else
 {
    $platba_mes = "X";
    $platba_ctvrt = "-";
 
 } 
// konec pripravy promennych
?>
